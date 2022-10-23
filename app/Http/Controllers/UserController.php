<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\Business;
use App\Models\Notification as ModelsNotification;
use App\Models\User;
use App\Models\Document;
use App\Models\Loan;
use App\Models\OpenApproval;
use App\Models\LoanRequest;
use App\Notifications\AdvisorAssigned;
use App\Notifications\NewEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
	
	
	public $pagecount = 20; 
	/**
	 * Update all user-related data.
	 *
	 * @param object $userId
	 * @param array $data
	 * @return void
	 */
	
	public function updateUserRelatedData($user, $data)
	{
		// Update business.
		$business = Business::find($user->business_id);
		$business->update($data['business']);

		// Partners fallback.
		$partners = $data['partners'] ?? [];

		// Delete the partners and their respective signatures.
		Partner::where('business_id', $business->id)
			->whereNotIn('id', Arr::pluck($partners, 'id'))
			->delete();

		// Update/create partners.
		foreach ($partners as $partner) {
			$partner['business_id'] = $business->id;

			if (isset($partner['signature'])) {
				$partner['signature'] = $this->storeSignature($partner['signature']);
			}

			Partner::updateOrCreate(
				['id' => $partner['id'] ?? false],
				$partner
			);
		}
	}

    /**
     * Show the form to edit the authenticated user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userEdit()
	{
		return view('dashboard.user.user-edit');
    }

    /**
     * Update the authenticated user's information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function userUpdate(Request $request)
	{
		$user = User::find(Auth::id());

		$validator = $user->isAdmin()
			? Validator::make($request->all(), [
				'photo' => ['sometimes', 'nullable', 'mimes:jpg,png'],
				'first_name' => ['required', 'string'],
				'last_name' => ['required', 'string']
			])
			: $this->validateForm($request, 'personal');

		if ($validator->fails()) {
			return redirect()
				->back()
				->withErrors($validator)
				->withInput();
		}

		// Data to populate database.
		$data = $request->except('signature');
		if ($request->signature) $data['signature'] = $this->storeSignature($request->signature);

		// Update user.
		$user->update($data);

		// Store the photo if there is any.
		$this->storePhoto($request->file('photo'), $user);

		if ($user->isNotAdmin()) {
			$this->updateUserRelatedData($user, $data);
		}

		return $this->responseWithMessage('Your Profile', 'update');
	}

	/**
     * Update the authenticated user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function userUpdatePassword(Request $request)
	{
		$user = User::find(Auth::id());

		$validator = Validator::make($request->all(), [
			'new_password' => ['required', Rules\Password::defaults()],
			'new_password_confirmation' => ['required', Rules\Password::defaults(), 'same:new_password']
		]);

		$validator->after(function ($validator) use($request, $user) {
			if (! Hash::check($request->current_password, $user->password)) {
				$validator->errors()->add('current_password', 'The current password does not match.');
			}

			if ($request->current_password === $request->new_password) {
				$validator->errors()->add('new_password', 'The new password can\'t be like the current one.');
			}
		});

		if ($validator->fails()) {
			return redirect()
				->back()
				->withErrors($validator)
				->withInput();
		}

		$user->update([
			'password' => Hash::make($request->new_password)
		]);

		return $this->responseWithMessage('Your Password', 'update');
	}

	/**
	 * Resend the new email verification link to the user.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function userNotifyNewEmail()
	{
		Notification::route('mail', Auth::user()->new_email)
			->notify(new NewEmail(Auth::user()));

		return redirect()->back()->with([
			'message' => 'A new verification link has been sent to ' . Auth::user()->new_email . '.'
		]);
	}

	/**
	 * Verify the new email provided by the user.
	 *
	 * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
	 */
	public function userVerifyNewEmail(Request $request, $id, $hash)
	{
		// Determine if the user is authorized to make this request.
		if (
			! hash_equals((string) $id, (string) $request->user()->id) ||
			! hash_equals((string) $hash, sha1((string) $request->user()->email))
		) {
			echo $hash;
			echo '<br>';
			echo $request->user()->email;
        	// abort(401);
			return;
        }

		// Update the user's email.
		if (
			$request->user()->new_email &&
			! User::where('email', $request->user()->new_email)->exists()
		) {
			$request->user()->update([
				'email' => $request->user()->new_email,
				'new_email' => null
			]);
		}

		return redirect()->intended();
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
			
			if(isset($request->userid)){
				return view('dashboard.admin.user-index', [
					'users' => User::where('deleted_at',null)->where('role', 'user')->where('id', $request->userid)->paginate($this->pagecount),
					'all_users' => User::where('deleted_at',null)->where('role', 'user')->get(),
					'countperpage' => $this->pagecount,
					'searchword' => $request->searchword
				]);	
			}
			print_r($request->userid);
			return view('dashboard.admin.user-index', [
				'users' => User::where('deleted_at',null)->where('role', 'user')->paginate($this->pagecount),
				'all_users' => User::where('deleted_at',null)->where('role', 'user')->get(),
				'countperpage' => $this->pagecount,
				'searchword' => $request->searchword
			]);	
		}
		
		return view('dashboard.admin.user-index', [
			'users' => User::where('deleted_at',null)->where('role', 'user')->paginate($this->pagecount),
			'all_users' => User::where('deleted_at',null)->where('role', 'user')->get(),
			'countperpage' => $this->pagecount,
			'searchword' => ''

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
		$user = User::find($id);

        return view('dashboard.admin.user-form', [
			'user' => $user,
			'advisors' => User::where('role', 'advisor')->get()
				->mapWithKeys(function($advisor) {
					return [$advisor->id => $advisor->full_name];
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
		$validator = $this->validateForm($request, ['business', 'personal']);

		if ($validator->fails()) {
			return redirect()
				->back()
				->withErrors($validator)
				->withInput();
		}

        $user = User::find($id);

		// If a new advisor was assigned to this user, send in-app and email notifications.
		if ($request->advisor_id && $user->advisor_id != $request->advisor_id) {
			$advisor = User::find($request->advisor_id);

			ModelsNotification::create([
				'user_id' => $user->id,
				'title' => 'Meet your new Advisor',
				'message' => "{$advisor->full_name} will be your new Advisor. You can check all his contact information in the sidebar."
			]);

			$advisor->notify(new AdvisorAssigned($user));
		}

		// Update user's data.
		$user->update($request->all());

		// Store the photo if there is any.
		$this->storePhoto($request->file('photo'), $user);

		// Update user's related data.
		$this->updateUserRelatedData($user, $request->all());

		return $this->responseWithMessage('User', 'update');
    }

	/**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
		$user = User::find($id);

		Partner::where('business_id', $user->business_id)->get()->each->delete();

		foreach ([
			Document::class,
			Loan::class,
			OpenApproval::class,
			LoanRequest::class,
			ModelsNotification::class
		] as $model) {
			$model::where('user_id', $user->id)->get()->each->delete();
		}

        $user->delete();

		return $this->responseWithMessage('User', 'destroy');
    }

	/**
	 * Restore a soft deleted resource.
	 *
	 * @param int $id
	 * @return \Illuminate\Http\Response
	 */
	public function restore($id)
	{
		$user = User::withTrashed()->where('id', $id)->first();

		Partner::where('business_id', $user->business_id)->get()->each->restore();

		foreach ([
			Document::class,
			Loan::class,
			OpenApproval::class,
			LoanRequest::class,
			ModelsNotification::class
		] as $model) {
			$model::withTrashed()->where('user_id', $user->id)->get()->each->restore();
		}

		User::withTrashed()->where('id', $id)->restore();

		return $this->responseWithMessage('User', 'restore');
	}

	/**
	 * Update the user's email address.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  integer  $userId
	 * @return  \Illuminate\Http\Response
	 */
	public function updateEmail(Request $request, $userId)
	{
		// Bail early if the user is not authorized to update this email address.
		if (Auth::user()->isNotAdmin() && $userId != Auth::id()) return;

		$user = User::find($userId);

		if ($user->email === $request->new_email) {
			return redirect()->back()->withErrors([ 'new_email' => 'The new email can\'t be like the current one.' ]);
		}

		$validator = Validator::make($request->all(), [
			'new_email' => ['required', 'unique:users,email'],
			'new_email_confirmation' => ['required', 'same:new_email']
		]);

		if ($validator->fails()) {
			return redirect()
				->back()
				->withErrors($validator)
				->withInput();
		}

		// Store new email on database to use after verification.
		$user->update([
			'new_email' => $request->new_email
		]);

		// Send verification to the new email provided.
		Notification::route('mail', $request->new_email)
			->notify(new NewEmail($user));

		$message = Auth::id() != $userId ?
			'For security purporses, the user will need to verify its inbox to verify the new email address' :
			'For security purposes, please check your inbox to verify your new email address.';

		return redirect()->back()->with([ 'message' => $message ]);
	}


	
	/**
	 * Restore a soft deleted resource.
	 *
	 * @param int $id
	 * @return \Illuminate\Http\Response
	 */
	public function changePageCount(Request $request)
	{
		print_r ($request);
		die;
		// return view('dashboard.admin.user-index', [
		// 	'users' => User::where('deleted_at',null)->where('role', 'user')->paginate($_GET['pagenum']),
		// 	'countperpage' => $_GET['pagenum']
		// ]);

		

		
	}
}
