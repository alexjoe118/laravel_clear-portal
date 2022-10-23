<?php

namespace App\Http\Controllers;

use App\Models\LoanProduct;
use App\Models\LoanRequest;
use App\Models\Notification;
use App\Models\OpenApproval;
use App\Models\User;
use App\Notifications\OpenApprovalCreated;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OpenApprovalController extends Controller
{
	public function getLoanProductsWithOpenApprovals($userId)
	{
		$loanProducts = LoanProduct::where('id', '!=', 2)->get();

		$loanProducts = $loanProducts->map(function($loanProduct) use($userId) {
			$openApprovalsQuery = OpenApproval::withoutGlobalScope('admin_deleted_resources')
				->where('user_id', $userId)
				->where('approval_expires', '>=', now())
				->orderBy('term_length');

			// Create the "Short Term Working Capital" group.
			if ($loanProduct->id === 1) {
				$loanProduct->title = 'Short Term Working Capital';
				$loanProduct->openApprovals = $openApprovalsQuery->whereIn('loan_product_id', [1, 2])->get();
			} else {
				$loanProduct->openApprovals = $openApprovalsQuery->where('loan_product_id', $loanProduct->id)->get();
			}

			return $loanProduct;
		})->filter(function($loanProduct) {
			return $loanProduct->openApprovals->isNotEmpty();
		});

		return $loanProducts;
	}

	/**
	 * Display a listing of the resource to the authenticated user.
	 *
	 * @param int|bool $userId
	 * @return \Illuminate\Http\Response
	 */
	public function userIndex($userId = false)
	{
		$preview = true;

		if (! $userId) {
			$userId = Auth::id();
			$preview = false;
		}

		$loanProducts = $this->getLoanProductsWithOpenApprovals($userId);

		return view('dashboard.user.open-approval-index', [
			'loanProducts' => $loanProducts,
			'preview' => $preview
		]);
	}

	/**
	 * Display a listing of the resource of a certain user.
	 *
	 * @param int $customerId
	 * @return \Illuminate\Http\Response
	 */
	public function landingUserIndex($customerId)
	{
		// If the user is logged in redirect to the actual Open Approvals page.
		if (Auth::check()) return redirect()->route('user.open-approval.index');

		$user = User::where('customer_id', $customerId)->first();
		if (! $user) abort(404);

		$loanProducts = $this->getLoanProductsWithOpenApprovals($user->id);

		return view('landing.open-approvals', [
			'loanProducts' => $loanProducts
		]);
	}

	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
	{
		$openApprovals = OpenApproval::orderBy('user_id')
			->where('approval_expires', '>=', now())->where('deleted_at', null)
			->get();
		$openApprovalsByUserAndType = $openApprovals->groupBy('user.listing_title')
			->mapWithKeys(function($openApprovalsByType, $user) {
				return [$user => $openApprovalsByType->groupBy(function($openApproval) {
					$expirationDate = Carbon::parse($openApproval->approval_expires)->format('m/d/Y');
					$loanProduct = $openApproval->loanProduct->title;

					return "$loanProduct - Expires At {$expirationDate}";
				})];
			})
			->sortKeys();

		return view('dashboard.admin.open-approval-index', [
			'openApprovalsByUserAndType' => paginateCollection($openApprovalsByUserAndType, 5)
		]);
    }

	/**
     * Show the form for editing the specified or a new resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function form(Request $request, $id = false)
    {
		$openApproval = OpenApproval::find($id);

		if ($openApproval)
			$openApproval->notes = $openApproval->notes();

        return view('dashboard.admin.open-approval-form', [
			'users' => User::all()->mapWithKeys(function($user) {
				return [$user->id => ($user->business->name ?? $user->full_name)];
			}),
			'loanRequests' => LoanRequest::all()->mapWithKeys(function($loanRequest) {
				return [$loanRequest->id => getTitle($loanRequest) . ' - ' . $loanRequest->user->business->name];
			}),
			'userId' => $request->user_id,
			'loanProductId' => $request->loan_product_id,
			'loanRequestId' => $request->loan_request_id,
			'openApproval' => $openApproval
		]);
    }

	/**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        OpenApproval::create($request->all());

		return $this->responseWithMessage('Open Approval', 'store');
    }

	/**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $openApproval = OpenApproval::find($id);

		$openApproval->update($request->all());

		return $this->responseWithMessage('Open Approval', 'update');
    }

	/**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        OpenApproval::destroy($id);

		return $this->responseWithMessage('Open Approval', 'destroy');
    }

	/**
	 * Restore a soft deleted resource.
	 *
	 * @param int $id
	 * @return \Illuminate\Http\Response
	 */
	public function restore($id)
	{
		OpenApproval::withTrashed()->where('id', $id)->restore();

		return $this->responseWithMessage('Open Approval', 'restore');
	}

	/**
	 * Notify an user about the specified resource.
	 *
	 * @param int $id
	 * @return void
	 */
	public function notify($id)
	{
		$openApproval = OpenApproval::find($id);

		Notification::create([
			'user_id' => $openApproval->user_id,
			'title' => 'New Open Approval',
			'message' => getTitle($openApproval),
			'url' => route('user.open-approval.index', [], false)
		]);

		$user = User::find($openApproval->user_id);

		$user->notify(new OpenApprovalCreated($openApproval));

		return $this->responseWithMessage('The User', 'notify');
	}
}
