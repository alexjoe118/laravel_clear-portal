<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Loan;
use App\Models\LoanRequest;

class OverviewController extends Controller
{
	/**
	 * Display an overview of what's happening in the portal.
	 *
	 * @return \Illuminate\Http\Response
	 */
    public function index()
	{
		$loanRequests = LoanRequest::doesntHave('openApprovals')->doesntHave('loans')->get();
		$loans = Loan::where('funded', false)->get();

		return view('dashboard.admin.overview', [
			'loanRequests' => $loanRequests,
			'loans' => $loans
		]);
	}
}
