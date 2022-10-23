<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use App\Notifications\BasicNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Update the specified notification read status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function userUpdate(Request $request, $id)
	{
        $notification = Notification::find($id);

		// Ignore it if the notification is not associated to the user that requested the update.
		if ( $notification->user_id !== Auth::id() ) return;

		$notification->read = ! (boolean) $request->read;
		$notification->save();

		return $notification->fresh();
    }

	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
	{
        $notifications = Notification::where('system', false)
			->latest()
			->orderBy('user_id')
			->paginate(20);
		$notificationsByUser = $notifications->groupBy('user.listing_title')->sortKeys();

		return view('dashboard.admin.notification-index', [
			'notifications' => $notifications,
			'notificationsByUser' => $notificationsByUser
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
		$notification = Notification::find($id);
		$users = User::all()
			->mapWithKeys(function($user) {
				return [$user->id => ($user->business->name ?? $user->full_name_id)];
			})
			->prepend('All Users', 'all');

        return view('dashboard.admin.notification-form', [
			'notification' => $notification,
			'users' => $users
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
		// Get the user IDs that should receive the notification.
		$userIds = $request->user_id === 'all'
			? User::where('role', 'user')->get()->pluck('id')
			: [$request->user_id];

		// Store the Notification(s).
		foreach ($userIds as $userId) {
			$user = User::find($userId);
			$user->notify(new BasicNotification($request->all()));

			Notification::create($request->merge([
				'system' => false,
				'user_id' => $userId
			])->all());
		}

		$resource = 'Notification';

		if ($plural = count($userIds) > 1) {
			$resource .= 's';
		}

		return $this->responseWithMessage($resource, 'store', $plural);
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
        $notification = Notification::find($id);

		$notification->update($request->all());

		return $this->responseWithMessage('Notification', 'update');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Notification::destroy($id);

		return $this->responseWithMessage('Notification', 'destroy');
    }

	/**
	 * Restore a soft deleted resource.
	 *
	 * @param int $id
	 * @return \Illuminate\Http\Response
	 */
	public function restore($id)
	{
		Notification::withTrashed()->where('id', $id)->restore();

		return $this->responseWithMessage('Notification', 'restore');
	}
}
