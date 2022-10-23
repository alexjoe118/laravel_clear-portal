<?php

namespace App\Http\Controllers;

use App\Models\DocumentGroup;
use Illuminate\Http\Request;

class DocumentGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
	{
		return view('dashboard.admin.document-group-index', [
			'documentGroups' => DocumentGroup::orderBy('id')->paginate(20)
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
		$documentGroup = DocumentGroup::find($id);

        return view('dashboard.admin.document-group-form', [
			'documentGroup' => $documentGroup
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
        DocumentGroup::create($request->all());

		return $this->responseWithMessage('Document Group', 'store');
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
        $documentGroup = DocumentGroup::find($id);

		$documentGroup->update($request->all());

		return $this->responseWithMessage('Document Group', 'update');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $documentGroup = DocumentGroup::find($id);

		if ($documentGroup->documents->isEmpty()) {
      $documentGroup->deleted_at = date("Y-m-d H:i:s");
      $documentGroup->update();
			return $this->responseWithMessage('Document Group', 'destroy');
		}

		return $this->responseWithMessage([$documentGroup, 'documents'], 'reassign');
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
        $documentGroup = DocumentGroup::find($id);
        $documentGroup->deleted_at = null;
        $documentGroup->update();

		    return $this->responseWithMessage('Document Group', 'restore');
    }
}
