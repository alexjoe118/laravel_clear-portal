<?php

namespace App\Http\Controllers;

use App\Models\DocumentSet;
use App\Models\DocumentType;
use App\Models\LoanGroup;
use App\Models\LoanProduct;
use App\Models\LoanRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanProductController extends Controller
{
	/**
	 * Display a listing of the resource to the authenticated user.
	 *
	 * @return \Illuminate\Http\Response
	 */
    public function userIndex()
	{
		return view('dashboard.user.loan-product-index', [
			'loanGroups' => LoanProduct::with('loanGroup')
				->get()
				->map(function($loanProduct) {
					$loanRequests = LoanRequest::where('user_id', Auth::id())
						->where('loan_product_id', $loanProduct->id)
						->get();

					// ? Loan Requests in progress.
					$inProgress = $loanRequests->first(function ($loanRequest) {
						return $loanRequest->loans()->exists() || $loanRequest->loans->where('funded', false);
					});

					// ? There is an application in progress.
					$loanProduct->in_progress = boolval($inProgress);

					// ? There are Open Approvals.
					$loanProduct->has_approvals = $inProgress
						? $inProgress->openApprovals()->exists()
						: false;

					// ? It's only pending funding.
					$loanProduct->is_closing = $inProgress
						? $inProgress->loans()->exists()
						: false;

					return $loanProduct;
				})
				->groupBy('loanGroup.title')
		]);
	}

	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		return view('dashboard.admin.loan-product-index', [
			'loanProducts' => LoanProduct::paginate(20)
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
		$loanProduct = LoanProduct::find($id);
		$loanGroups = LoanGroup::all()->mapWithKeys(function($loanGroup) {
			return [$loanGroup->id => $loanGroup->title];
		});
        $documentTypes = DocumentType::where('document_set_id', 0)->get(); // doc type that were not associated to a set.
        $documentSets = DocumentSet::all();

        return view('dashboard.admin.loan-product-form', [
			'loanProduct' => $loanProduct,
			'loanGroups' => $loanGroups,
            'documentTypes' => $documentTypes->mapWithKeys(function($documentType) {
                return [$documentType->id => $documentType->title];
            }),
			'documentSets' => $documentSets->mapWithKeys(function($set) {
				return [$set->id => $set->title];
			})
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
        $loanProduct = LoanProduct::find($id);

		$props = $request->props ? array_values(array_filter($request->props)) : [];

		$loanProduct->update($request->merge([
			'props' => ! empty($props) ? $props : null,
			'required_document_types' => $request->required_document_types ?? null
		])->all());

		return $this->responseWithMessage('Loan Product', 'update');
    }
}
