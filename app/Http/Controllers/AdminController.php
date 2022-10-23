<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\AdminCreated;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
	/**
     * Instantiate a new controller instance.
     *
     * @return void
     */
	public function __construct()
	{
		$this->adminType = Str::between(Route::currentRouteName(), '.', '.');
	}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
	{
		return view("dashboard.admin.{$this->adminType}-index", [
			Str::plural($this->adminType) => User::where('role', $this->adminType)->orderBy('first_name')->paginate(20)
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
		$admin = User::find($id);

        return view("dashboard.admin.admin-form", [
			'adminType' => $this->adminType,
			'admin' => $admin
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
		// Create a random password.
		$password = Str::random(10);

		$data = collect(
			$request->merge([
				'role' => $this->adminType,
				'password' => $password
			])->except(['_token', '_method'])
		);

		// Create the admin.
        $admin = User::forceCreate(
			$data->merge([
				'password' => Hash::make($password),
				'email_verified_at' => now()
			])->all()
		);

		$this->storePhoto($request->file('photo'), $admin);

		// Send email to the admin containing the password.
		$admin->notify(new AdminCreated($data));

		return $this->responseWithMessage(Str::title($this->adminType), 'store');
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
        $admin = User::find($id);

		if ($admin->email !== $request->email && User::where('email', $request->email)->exists()) {
			return redirect()->back()->withErrors([
				'' => "The email {$request->email} is already in use."
			]);
		}

		$admin->update($request->all());
		$this->storePhoto($request->file('photo'), $admin);

		return $this->responseWithMessage(Str::title($this->adminType), 'update');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
		$admin = User::find($id);

		if (! $admin->advised_users || ($admin->advised_users && $admin->advised_users->isEmpty())) {
			$admin->delete();

			return $this->responseWithMessage(Str::title($this->adminType), 'destroy');
		}

		return $this->responseWithMessage([$admin, 'user', 'advised_users'], 'reassign');
    }

	/**
	 * Restore a soft deleted resource.
	 *
	 * @param int $id
	 * @return \Illuminate\Http\Response
	 */
	public function restore($id)
	{
		User::withTrashed()->where('id', $id)->restore();

		return $this->responseWithMessage(Str::title($this->adminType), 'restore');
	}
}
