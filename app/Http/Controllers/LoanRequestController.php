<?php

namespace App\Http\Controllers;

use App\Models\LoanProduct;
use App\Models\LoanRequest;
use App\Models\Notification;
use App\Models\User;
use App\Notifications\LoanRequestStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanRequestController extends Controller
{
	public $pagecount = 20; 
	/**
     * Store a newly created resource in storage to the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function userStore(Request $request)
	{
		$loanResponse = $this->storeLoanRequest($request, Auth::id());

		if ($loanResponse['error']) {
			return redirect()->back()->withErrors($loanResponse['validator']);
		}

		$loanProduct = LoanProduct::find($request->loan_product_id);
		$loanProductName = $loanProduct->title;
		if ($loanProduct->article) $loanProductName = $loanProduct->article . ' ' . $loanProductName;

		return redirect()->back()->with('applied_loan', $loanProductName);
	}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
	{
		if(isset($request->pagecount)){
			$this->pagecount = $request->pagecount;
			$loanRequests = LoanRequest::withTrashed()->paginate($this->pagecount);
			$loanRequestsByUser = $loanRequests->where('deleted_at', null)->groupBy('user.listing_title')->sortKeys();
			return view('dashboard.admin.loan-request-index', [
				'loanRequests' => $loanRequests,
				'loanRequestsByUser' => $loanRequestsByUser,
				'countperpage' => $this->pagecount
			]);
		}
		$loanRequests = LoanRequest::withTrashed()->paginate(20);
		$loanRequestsByUser = $loanRequests->where('deleted_at', null)->groupBy('user.listing_title')->sortKeys();
		return view('dashboard.admin.loan-request-index', [
			'loanRequests' => $loanRequests,
			'loanRequestsByUser' => $loanRequestsByUser,
			'countperpage' => $this->pagecount
		]);
    }

	/**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function form($id = false)
    {
		$loanRequest = LoanRequest::find($id);

        return view('dashboard.admin.loan-request-form', ['loanRequest' => $loanRequest]);
    }

	/**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
		$loanRequest = LoanRequest::find($id);

		$loanRequest->openApprovals()->delete();
		$loanRequest->delete();

		return $this->responseWithMessage('Loan Request', 'destroy');
    }

	/**
	 * Notify an user about the specified resource.
	 *
	 * @param int $id
	 * @return void
	 */
	public function notify($id)
	{
		$loanRequest = LoanRequest::withoutGlobalScope('admin_deleted_resources')->find($id);
		$loanRequest->openApprovals = $loanRequest->openApprovals->where('deleted_at', null);

		Notification::create([
			'user_id' => $loanRequest->user_id,
			'title' => $loanRequest->openApprovals->count() . ' New Open Approval(s)',
			'message' => 'You can visualize all of them on the "Open Approvals" page',
			'url' => route('user.open-approval.index', [], false)
		]);

		$user = User::find($loanRequest->user_id);

		$user->notify(new LoanRequestStatus($loanRequest));

		return $this->responseWithMessage('The User', 'notify');
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
        $loanRequest = LoanRequest::find($id);

		$loanRequest->update($request->all());

		return $this->responseWithMessage('Loan Request', 'update');
    }

	/**
	 * Restore a soft deleted resource.
	 *
	 * @param int $id
	 * @return \Illuminate\Http\Response
	 */
	public function restore($id)
	{
		LoanRequest::withTrashed()->where('id', $id)->restore();
		LoanRequest::find($id)->openApprovals()->restore();

		return $this->responseWithMessage('Loan Request', 'restore');
	}
}
