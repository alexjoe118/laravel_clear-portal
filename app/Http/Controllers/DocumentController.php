<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentGroup;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use stdClass;

class DocumentController extends Controller
{
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

		$documentGroups = DocumentGroup::with(['documents' => function($query) use($userId) {
			$query->where('user_id', $userId)->latest();
		}])->get();

		$uncategorizedDocuments = Document::where([
			'document_group_id' => null,
			'user_id' => $userId
		])->get();

		if ($uncategorizedDocuments->isNotEmpty()) {
			$uncategorized = new stdClass();
			$uncategorized->id = null;
			$uncategorized->title = 'Uncategorized';
			$uncategorized->documents = $uncategorizedDocuments;
			$documentGroups[] = $uncategorized;
		}

		return view('dashboard.user.document-index', [
			'documentGroups' => $documentGroups,
			'preview' => $preview
		]);
	}

	/**
     * Update the specified resource in storage for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userUpdate(Request $request, $id)
	{
        $document = Document::find($id);

		// Ignore it if the document is not associated to the user that requested the delete.
		if ($document->user_id !== Auth::id()) return;

		// Prepare the new filename when needed.
		if ($request->filename) {
			$extension = pathinfo($document->filename)['extension'];
			$newFilename = pathinfo($request->filename)['filename'];

			if ($newFilename) {
				$request->merge([
					'filename' =>  $newFilename . '.' . $extension
				]);

			// Bail early if new filename is invalid.
			} else {
				return redirect()->back();
			}
		}

		$document->update($request->all());

		return $this->responseWithMessage('Document', 'update');
    }

	/**
     * Store a newly created resource in storage for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function userStore(Request $request)
	{
		$this->storeDocument(
			$request->file('document'),
			$request->document_group_id,
			Auth::id()
		);

		return $this->responseWithMessage('Document', 'upload');
	}

	/**
     * Remove the specified document from storage for the authenticated user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function userDestroy($id)
	{
		$document = Document::find($id);

		// Ignore it if the document is not associated to the user that requested the delete.
		if ($document->user_id !== Auth::id()) return;

		$document->delete();

		return $this->responseWithMessage('Document', 'destroy');
	}

	/**
     * Download the resource for the authenticated user.
     *
     * @return \Illuminate\Http\Response
     */
    public function download($id)
	{
        $document = Document::find($id);

		// Ignore it if the document is not associated to the user that requested the download.
		if ($document->user_id !== Auth::id() && !Auth::user()->isAdmin()) return;

		$response = Storage::disk('local')->download($document->file, $document->filename);
		return $response;
    }

	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
	{
        $documents = Document::where('deleted_at', null)->orderBy('user_id')->paginate(20);
		$documentsByUser = $documents->groupBy('user.listing_title')->sortKeys();

		return view('dashboard.admin.document-index', [
			'documents' => $documents,
			'documentsByUser' => $documentsByUser
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
		$document = Document::find($id);

        return view('dashboard.admin.document-form', [
			'document' => $document,
			'documentGroups' => DocumentGroup::all()->mapWithKeys(function($documentGroup) {
				return [$documentGroup->id => $documentGroup->title];
			}),
			'documentTypes' => DocumentType::all()->mapWithKeys(function($documentType) {
				return [$documentType->id => $documentType->title];
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
        $document = Document::find($id);

		$document->update($request->all());

		return $this->responseWithMessage('Document', 'update');
    }

	/**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Document::destroy($id);

		return $this->responseWithMessage('Document', 'destroy');
    }

	/**
	 * Restore a soft deleted resource.
	 *
	 * @param int $id
	 * @return \Illuminate\Http\Response
	 */
	public function restore($id)
	{
		Document::withTrashed()->where('id', $id)->restore();

		return $this->responseWithMessage('Document', 'restore');
	}
}
