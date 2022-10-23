<?php

namespace App\Http\Controllers;

use App\Models\LoanGroup;
use Illuminate\Http\Request;

class LoanGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
	{
		return view('dashboard.admin.loan-group-index', [
			'loanGroups' => LoanGroup::orderBy('title')->paginate(20)
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
		$loanGroup = LoanGroup::find($id);

        return view('dashboard.admin.loan-group-form', [
			'loanGroup' => $loanGroup
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
        LoanGroup::create($request->all());

		return $this->responseWithMessage('Loan Group', 'store');
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
        $loanGroup = LoanGroup::find($id);

		$loanGroup->update($request->all());

		return $this->responseWithMessage('Loan Group', 'update');
    }

     /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $loanGroup = LoanGroup::find($id);
        $loanGroup->deleted_at = null;
        $loanGroup->update();

		return $this->responseWithMessage('Loan Group', 'restore');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $loanGroup = LoanGroup::find($id);

		if ($loanGroup->loanProducts->isEmpty()) {
      //$loanGroup->delete();
      $loanGroup->deleted_at = date("Y-m-d H:i:s");
			$loanGroup->update();
			return $this->responseWithMessage('Loan Group', 'destroy');
		}

		return $this->responseWithMessage([$loanGroup, 'loan-product'], 'reassign');
    }
}
