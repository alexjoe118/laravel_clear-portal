<?php

namespace App\Http\Controllers;

use App\Models\DocumentSet;
use App\Models\DocumentType;
use App\Models\LoanProduct;
use Illuminate\Http\Request;

class DocumentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
	{
		return view('dashboard.admin.document-type-index', [
			'documentTypes' => DocumentType::orderBy('title')->paginate(20)
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
		$documentType = DocumentType::find($id);
		$documentSets = DocumentSet::all()->mapWithKeys(function($set) {
			return [$set->id => $set->title];
		});

        return view('dashboard.admin.document-type-form', [
			'documentType' => $documentType,
			'documentSets' => $documentSets
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
        DocumentType::create($request->all());

		return $this->responseWithMessage('Document Type', 'store');
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
        $documentType = DocumentType::find($id);

		$documentType->update($request->all());

		return $this->responseWithMessage('Document Type', 'update');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $documentType = DocumentType::find($id);

		if ($documentType->documents->isNotEmpty()) {
			return $this->responseWithMessage([$documentType, 'documents'], 'reassign');
		}

		if ($documentType->loanProducts->isNotEmpty()) {
			return $this->responseWithMessage([$documentType, 'loan-products'], 'reassign');
		}
		$documentType->deleted_at = date("Y-m-d H:i:s");
		$documentType->update();
		
		return $this->responseWithMessage('Document Type', 'destroy');
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
        $documentType = DocumentType::find($id);
        $documentType->deleted_at = null;
        $documentType->update();

		return $this->responseWithMessage('Document Type', 'restore');
    }
}
