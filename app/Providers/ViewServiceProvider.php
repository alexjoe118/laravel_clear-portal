<?php

namespace App\Providers;

use App\Models\LoanProduct;
use App\Models\Settings;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
		if (app()->runningInConsole()) return;

		// Menu options.
        View::composer('layouts.dashboard', function () {
			$sidebarMenu = [
				[
					'icon' => 'home',
					'url' => 'user.loan-product.index',
					'title' => 'Dashboard',
					'role' => ['user']
				],
				[
					'icon' => 'balloon-check',
					'url' => 'user.open-approval.index',
					'title' => 'Open Approvals',
					'role' => ['user']
				],
				[
					'icon' => 'dollar-sign-exchange',
					'url' => 'user.loan.index',
					'title' => 'My Loans',
					'role' => ['user']
				],
				[
					'icon' => 'paper',
					'url' => 'user.document.index',
					'title' => 'My Documents',
					'role' => ['user']
				],
				
				[
					'icon' => 'overview',
					'url' => 'admin.overview.index',
					'title' => 'Overview',
					'role' => ['advisor', 'manager']
				],
				[
					'icon' => 'thrash',
					'url' => 'admin.del-list.index',
					'title' => 'Deleted Items',
					'role' => ['manager']
				],
				[
					'icon' => 'paper-group-money',
					'url' => 'admin.loan-group.index',
					'title' => 'Loan Groups',
					'role' => ['manager']
				],
			
				[
					'icon' => 'paper-money',
					'url' => 'admin.loan-product.index',
					'title' => 'Loan Products',
					'role' => ['manager']
				],
				[
					'icon' => 'paper-writing',
					'url' => 'admin.loan-request.index',
					'title' => 'Loan Requests',
					'role' => ['advisor', 'manager']
				],
				[
					'icon' => 'dollar-sign-exchange',
					'url' => 'admin.loan.index',
					'title' => 'Loans',
					'role' => ['advisor', 'manager']
				],
				[
					'icon' => 'balloon-check',
					'url' => 'admin.open-approval.index',
					'title' => 'Open Approvals',
					'role' => ['advisor', 'manager']
				],
				[
					'icon' => 'money-trade',
					'url' => 'admin.lender.index',
					'title' => 'Lenders',
					'role' => ['advisor', 'manager']
				],
				[
					'icon' => 'paper-group',
					'url' => 'admin.document-group.index',
					'title' => 'Document Groups',
					'role' => ['manager']
				],
				[
					'icon' => 'attachment',
					'url' => 'admin.document-type.index',
					'title' => 'Document Types',
					'role' => ['manager']
				],
				[
					'icon' => 'paper-group',
					'url' => 'admin.document-set.index',
					'title' => 'Document Sets',
					'role' => ['manager']
				],
				[
					'icon' => 'paper',
					'url' => 'admin.document.index',
					'title' => 'Documents',
					'role' => ['advisor', 'manager']
				],
				[
					'icon' => 'bell',
					'url' => 'admin.notification.index',
					'title' => 'Notifications',
					'role' => ['advisor', 'manager']
				],
				[
					'icon' => 'customer-service',
					'url' => 'admin.advisor.index',
					'title' => 'Advisors',
					'role' => ['manager']
				],
				[
					'icon' => 'user-manager',
					'url' => 'admin.manager.index',
					'title' => 'Managers',
					'role' => ['manager']
				],
				[
					'icon' => 'user',
					'url' => 'admin.user.index',
					'title' => 'Users',
					'role' => ['advisor', 'manager']
				],
				[
					'icon' => 'file-chart',
					'url' => 'admin.report.index',
					'title' => 'Reports',
					'role' => ['manager']
				],
				[
					'icon' => 'settings',
					'url' => 'admin.settings.edit',
					'title' => 'Settings',
					'role' => ['manager']
				]
			];

			// The sidebar menu.
			View::share('sidebarMenu', $sidebarMenu);

			// The authenticated user's notifications.
			$userNotifications = Auth::user()->notifications->sortByDesc('created_at')->sortBy('read') ?? collect([]);
			$userNotificationsNew = $userNotifications->filter(function ($notification) {
				return ! $notification->read;
			})->count();

			View::share('userNotifications', $userNotifications);
			View::share('userNotificationsNew', $userNotificationsNew);

			// Authenticated user's advisor.
			View::share('userAdvisor', Auth::check() ? Auth::user()->advisor : false);
        });

		View::composer('*', function($view) {
			$viewName = Str::of($view->getName())->replace('.', '-');

			// Current view name.
			View::share('viewName', $viewName);

			// Portal's global settings.
			View::share('globalSettings', Settings::all());

			// Global select options.
			View::share('selectOptions', [
				'loanProducts' => LoanProduct::all()->mapWithKeys(function($loanProduct) {
					return [ $loanProduct->id => $loanProduct->title ];
				}),

				'termLengthEstimates' => [
					'less-12-months' => 'Less than 12 months',
					'12-24-months' => '12-24 months',
					'2-5-years' => '2-5 years',
					'5-7-years' => '5-7 years',
					'10-years-more' => '10 years+',
					'any' => 'Any'
				],

				'fundsNeededEstimates' => [
					'1-2-days' => '1-2 days',
					'1-week' => '1 week',
					'2-weeks' => '2 weeks',
					'1-month' => '1 month',
					'2-months' => '2 months',
					'2-months-more' => '2 months+',
					'none' => 'No specific timeframe'
				],

				'fundsUsages' => [
					'Working Capital',
					'Inventory',
					'Equipment',
					'Debt Consolidation',
					'Expansion',
					'Real Estate',
					'Other'
				],

				'approximateCreditScores' => [
					'800+',
					'750-799',
					'700-749',
					'650-699',
					'600-649',
					'550-559',
					'500-549',
					'Below 500'
				],

				'communicationChannels' => [
					'Office phone',
					'Cell phone',
					'Email',
					'Text',
					'No preference'
				],

				'typesOfEntities' => [
					'Corporation',
					'LLC',
					'Sole Proprietorship',
					'Partnership',
					'Other'
				],

				'industries' => [
					'Accounting',
					'Administrative/Support Services',
					'Agricultural',
					'Amusement/Recreation',
					'Auto Repair',
					'Auto Sales',
					'Business Services',
					'Construction/General Contractor',
					'E-commerce',
					'Educational Services',
					'Energy',
					'Engineering',
					'Financial Services',
					'Gas Station',
					'Grocery Store/Supermarkets',
					'Insurance',
					'Landscaping/Lawn & Gardening',
					'Laundromats/Dry-Cleaners',
					'Legal Services',
					'Manufacturing',
					'Medical/Healthcare',
					'Pharmacy',
					'Real-Estate',
					'Restaurant',
					'Retail',
					'Staffing',
					'Technology/IT',
					'Towing',
					'Trucking/Transportation',
					'Waste Management',
					'Wholesale',
					'Other'
				],

				'grossAnnualSales' => [
					'less-100000' => 'Less than $100,000',
					'100000-250000' => '$100,000 - $250,000',
					'250000-500000' => '$250,000 - $500,000',
					'500000-750000' => '$500,000 - $750,000',
					'750000-1000000' => '$750,000 - $1,000,000',
					'1000000-1500000' => '$1,000,000 - $1,500,000',
					'1500000-2500000' => '$1,500,000 - $2,500,000',
					'2500000-5000000' => '$2,500,000 - $5,000,000',
					'5000000-more' => 'Greater than $5,000,000'
				],

				'monthlySalesVolumes' => [
					'less-15000' => 'Less than $15,000',
					'15000-25000' => '$15,000 - $25,000',
					'25000-50000' => '$25,000 - $50,000',
					'50000-100000' => '$50,000 - $100,000',
					'100000-150000' => '$100,000 - $150,000',
					'150000-250000' => '$150,000 - $250,000',
					'250000-500000' => '$250,000 - $500,000',
					'500000-more' => 'Greater than $500,000'
				],

				'states' => [
					'AL' => 'Alabama',
					'AK' => 'Alaska',
					'AZ' => 'Arizona',
					'AR' => 'Arkansas',
					'CA' => 'California',
					'CO' => 'Colorado',
					'CT' => 'Connecticut',
					'DE' => 'Delaware',
					'DC' => 'District Of Columbia',
					'FL' => 'Florida',
					'GA' => 'Georgia',
					'HI' => 'Hawaii',
					'ID' => 'Idaho',
					'IL' => 'Illinois',
					'IN' => 'Indiana',
					'IA' => 'Iowa',
					'KS' => 'Kansas',
					'KY' => 'Kentucky',
					'LA' => 'Louisiana',
					'ME' => 'Maine',
					'MD' => 'Maryland',
					'MA' => 'Massachusetts',
					'MI' => 'Michigan',
					'MN' => 'Minnesota',
					'MS' => 'Mississippi',
					'MO' => 'Missouri',
					'MT' => 'Montana',
					'NE' => 'Nebraska',
					'NV' => 'Nevada',
					'NH' => 'New Hampshire',
					'NJ' => 'New Jersey',
					'NM' => 'New Mexico',
					'NY' => 'New York',
					'NC' => 'North Carolina',
					'ND' => 'North Dakota',
					'OH' => 'Ohio',
					'OK' => 'Oklahoma',
					'OR' => 'Oregon',
					'PA' => 'Pennsylvania',
					'RI' => 'Rhode Island',
					'SC' => 'South Carolina',
					'SD' => 'South Dakota',
					'TN' => 'Tennessee',
					'TX' => 'Texas',
					'UT' => 'Utah',
					'VT' => 'Vermont',
					'VA' => 'Virginia',
					'WA' => 'Washington',
					'WV' => 'West Virginia',
					'WI' => 'Wisconsin',
					'WY' => 'Wyoming'
				],
				'pageCount' => [
					'20' => '20',
					'50' => '50',
					'100' => '100',
				]
			]);

			// Authenticated user's advisor.
			View::share('userAdvisor', Auth::check() ? Auth::user()->advisor : false);
		});
    }
}
