<?php

namespace App\Http\Controllers;

use App\Models\DocumentSet;
use Illuminate\Http\Request;

class DocumentSetController extends Controller
{
	public function index()
	{
		return view('dashboard.admin.document-set-index', [
			'documentSets' => DocumentSet::orderBy('title')->paginate(20)
		]);
	}

	public function form($id = false)
	{
		$documentSet = DocumentSet::find($id);

		return view('dashboard.admin.document-set-form', ['documentSet' => $documentSet]);
	}

	public function store(Request $request)
	{
		DocumentSet::create($request->all());

		return $this->responseWithMessage('Document Set', 'store');
	}

	public function update(Request $request, $id)
	{
		$documentSet = DocumentSet::find($id);

		$documentSet->update($request->all());

		return $this->responseWithMessage('Document Set', 'update');
	}

	public function destroy($id)
	{
		$documentSet = DocumentSet::find($id);
		// Alex Error occured
		// if ($documentSet->documents->isNotEmpty()) {
		// 	return $this->responseWithMessage([$documentSet, 'documents'], 'reassign');
		// }
		// Alex Error occured
		if ($documentSet->loanProducts->isNotEmpty()) {
			return $this->responseWithMessage([$documentSet, 'loanProducts'], 'reassign');
		}
		$documentSet->deleted_at = date("Y-m-d H:i:s");
		$documentSet->update();
		return $this->responseWithMessage('Document Set', 'destroy');
	}
	  // Alex Added
     /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $documentSet = DocumentSet::find($id);
        $documentSet->deleted_at = null;
        $documentSet->update();

		return $this->responseWithMessage('Document Set', 'restore');
    }
}
