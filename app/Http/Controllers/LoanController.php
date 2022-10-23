<?php

namespace App\Http\Controllers;

use App\Models\Lender;
use App\Models\Loan;
use App\Models\Notification as ModelsNotification;
use App\Models\OpenApproval;
use App\Models\User;
use App\Notifications\LoanCreatedAdmin;
use App\Notifications\LoanFunded;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class LoanController extends Controller
{
	public $pagecount = 20; 
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

		$loans = Loan::where('user_id', $userId)
			->where('funded', true)
			->orderBy('funded_date', 'DESC')
			->get();

		return view('dashboard.user.loan-index', [
			'loans' => $loans,
			'preview' => $preview,
		]);
	}

	/**
     * Store a newly created resource in storage to the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function userStore(Request $request)
	{
		$loan = Loan::create($request->merge([
			'user_id' => Auth::id()
		])->all());

		// Send notification to admins.
		$admins = User::where('role', 'manager')
			->orWhere('id', Auth::user()->advisor)
			->get();
		Notification::send($admins, new LoanCreatedAdmin($loan));

		return redirect()->back()->with('applied_open_approval', true);
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
			$loans = Loan::where('deleted_at', null)->orderBy('user_id')->paginate($this->pagecount);
			$loansByUser = $loans->where('deleted_at', null)->groupBy('user.listing_title')->sortKeys();

			return view('dashboard.admin.loan-index', [
				'loans' => $loans,
				'loansByUser' => $loansByUser,
				'countperpage' => $this->pagecount
			]);
		}

		$loans = Loan::where('deleted_at', null)->orderBy('user_id')->paginate(20);
		$loansByUser = $loans->where('deleted_at', null)->groupBy('user.listing_title')->sortKeys();

		return view('dashboard.admin.loan-index', [
			'loans' => $loans,
			'loansByUser' => $loansByUser,
			'countperpage' => $this->pagecount
		]);
    }

    /**
     * Show the form for editing the specified or a new resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function form($id = false)
	{
		$loan = Loan::find($id);
		$lenders = Lender::all()->mapWithKeys(function($lender) {
			return [$lender->id => $lender->name];
		});

        return view('dashboard.admin.loan-form', [
			'loan' => $loan,
			'lenders' => $lenders
		]);
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
        $loan = Loan::find($id);

		if ($documents = $request->file('contract_documents')) {

			// Store new documents.
			$documentFilenames = $loan->contract_documents ?? [];

			foreach ($documents as $document) {
				$filename = $document->getClientOriginalName();
				$documentFilenames[] = $filename;

				Storage::disk('local')->put("contract-documents/$id/$filename", file_get_contents($document));
			}

			// Store the document paths on database.
			$loan->contract_documents = $documentFilenames;
			$loan->save();
		}

		$wasNotFunded = ! $loan->funded;
		$loan->update($request->except('contract_documents'));

		if ($request->funded && $wasNotFunded) {
			$loan->update([
				'funded' => $request->funded,
				'funded_date' => $request->funded
					? ($request->funded_date ?? Carbon::now()->format('m/d/Y'))
					: null
			]);

			// In-app notification.
			ModelsNotification::create([
				'user_id' => $loan->user_id,
				'title' => 'Loan Funded',
				'message' => getTitle($loan),
				'url' => route('user.loan.index', [], false)
			]);

			// Email notification.
			$user = User::find($loan->user_id);
			$user->notify(new LoanFunded($loan));

			// Delete related Open Approvals.
			OpenApproval::where('user_id', $loan->user_id)
				->whereIn('loan_product_id', in_array($loan->loan_product_id, [1, 2]) ? [1, 2] : [$loan->loan_product_id])
				->delete();
		}

		return $this->responseWithMessage('Loan', 'update');
    }

	/**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Loan::destroy($id);

		return $this->responseWithMessage('Loan', 'destroy');
    }

	/**
	 * Restore a soft deleted resource.
	 *
	 * @param int $id
	 * @return void
	 */
	public function restore($id)
	{
		Loan::withTrashed()->where('id', $id)->restore();

		return $this->responseWithMessage('Loan', 'restore');
	}

	/**
     * Download the contract documents for a certain Loan.
     *
	 * @param integer $loanId
     * @return \Illuminate\Http\Response
     */
    public function downloadContractDocuments(Request $request, $id)
	{
        $loan = Loan::find($id);

		// Ignore it if the document is not associated to the user that requested the download.
		if ($loan->user_id != Auth::id() && Auth::user()->isNotAdmin()) abort(201);

		if ($request->filename) {
			return Storage::disk('local')->download("contract-documents/$id/{$request->filename}");
		} else {
			$zip = new ZipArchive;
			$uniqid = uniqid();
			$zipPath = storage_path("app/public/contract-documents-$uniqid.zip");
			$zip->open($zipPath, ZipArchive::CREATE);

			foreach ($loan->contract_documents as $contractDocument) {
				$zip->addFromString($contractDocument, Storage::disk('local')->get("contract-documents/$id/$contractDocument"));
			}

			$zip->close();

			return response()->download($zipPath)->deleteFileAfterSend(true);
		}
    }

	/**
     * Delete the contract documents for a certain Loan.
     *
	 * @param integer $loanId
     * @return \Illuminate\Http\Response
     */
    public function deleteContractDocuments(Request $request, $id)
	{
        $loan = Loan::find($id);

		// Ignore it if the document is not associated to the user that requested the download.
		if ($loan->user_id != Auth::id() && Auth::user()->isNotAdmin()) abort(201);

		if ($request->filename) {
			Storage::disk('local')->delete("contract-documents/$id/{$request->filename}");

			$loan->contract_documents = array_filter($loan->contract_documents, function($contractDocument) use($request) {
				return $contractDocument != $request->filename;
			});
		} else {
			foreach ($loan->contract_documents as $contractDocument) {
				Storage::disk('local')->delete("contract-documents/$id/$contractDocument");
			}

			$loan->contract_documents = NULL;
		}

		$loan->save();

		return $this->responseWithMessage('The Contract Document' . ($request->filename ? '' : 's'), 'destroy', ! boolval($request->filename));
    }
}
