<?php

namespace App\Http\Controllers;

use App\Models\Lender;
use Illuminate\Http\Request;

class LenderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
	{
		return view('dashboard.admin.lender-index', [
			'lenders' => Lender::orderBy('name')->paginate(20)
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
		$lender = Lender::find($id);

        return view('dashboard.admin.lender-form', [
			'lender' => $lender
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
        Lender::create($request->all());

		return $this->responseWithMessage('Lender', 'store');
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
        $lender = Lender::find($id);

		$lender->update($request->all());

		return $this->responseWithMessage('Lender', 'update');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
		$lender = Lender::find($id);

		if ($lender->loans->isEmpty()) {
			$lender->delete();

			return $this->responseWithMessage('Lender', 'destroy');
		}

		return $this->responseWithMessage([$lender, 'loans'], 'reassign');
    }

	/**
	 * Restore a soft deleted resource.
	 *
	 * @param int $id
	 * @return \Illuminate\Http\Response
	 */
	public function restore($id)
	{
		Lender::withTrashed()->where('id', $id)->restore();

		return $this->responseWithMessage('Lender', 'restore');
	}
}
