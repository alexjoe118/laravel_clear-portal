<?php

namespace App\Listeners;

use App\Models\Loan;
use App\Models\User;
use App\Models\Document;
use App\Models\LoanRequest;
use App\Models\Notification;
use App\Models\OpenApproval;
use App\Models\Business;
use App\Models\Partner;
use App\Models\Lender;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Database\Eloquent\Builder;

class UserLoggedIn
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(Authenticated $event)
    {
		// Scope all queries for advisors so they only see users and information related to them.
		if (Auth::user()->isAdvisor()) {
			User::addGlobalScope(
				'advised',
				function (Builder $query) {
					return $query->where('advisor_id', Auth::id())
						->orWhere('role', 'advisor');
				}
			);

			foreach ([
				Document::class,
				LoanRequest::class,
				Loan::class,
				Notification::class,
				OpenApproval::class
			] as $model) {
				$model::addGlobalScope(
					'advised_resources',
					function (Builder $query) {
						return $query->whereHas('user', function (Builder $q) {
							$q->where('advisor_id', Auth::id());
						});
					}
				);
			}
		}

		// Allow admins to visualize deleted resources.
		if (Auth::user()->isAdmin()) {
			foreach ([
				'documents' => Document::class,
				'loan_requests' => LoanRequest::class,
				'loans' => Loan::class,
				'notifications' => Notification::class,
				'open_approvals' => OpenApproval::class,
				'users' => User::class,
				'businesses' => Business::class,
				'partners' => Partner::class,
				'lenders' => Lender::class
			] as $table => $model) {
				$model::addGlobalScope(
					'admin_deleted_resources',
					function (Builder $query) use ($table) {
						$query->withTrashed();

						$wheres = $query->getQuery()->wheres;

						foreach ($wheres as $key => $data) {
							if (
								isset($data['column']) &&
								$data['column'] === "$table.deleted_at"
								&& $data['type'] === 'Null'
							) {
								unset($query->getQuery()->wheres[$key]);
							}
						}

						return $query;
					}
				);
			}
		}
    }
}
