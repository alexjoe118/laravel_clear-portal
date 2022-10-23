<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Partner;
use App\Models\Business;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Notifications\UserCreated;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Notification;

class RegisteredUserController extends Controller
{
	/**
	 * The registration steps.
	 *
	 * @var array
	 */
	public $steps = [
		'credentials',
		'business',
		'personal'
	];

	/**
	 * Generate an unique id to add to registered users.
	 *
	 * @return int
	 */
	public function generateId()
	{
		do {
            $id = random_int(1000000, 9999999);
        } while (User::where('customer_id', $id)->first());

		return $id;
	}

    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
	{
        return view('auth.register');
    }

	/**
     * Handle each step of the registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     *
     * @throws \Illuminate\Validation\ValidationException
     */
	public function step(Request $request)
	{
		$validator = $this->validateForm($request, $this->steps[$request->step]);

		// Regenerate session.
		session()->regenerate();
		$token = csrf_token();

		$response = collect([ 'token' => $token ]);

		// Validation failed.
		if ($validator->fails()) {
			$response = $response->merge([
				'errors' => $validator->errors()->keys(),
				'messages' => view('components.form-messages')
					->withErrors($validator)
					->render()
			]);
        }

		return $response;
	}

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
	{
        $validator = $this->validateForm($request, $this->steps);

		// Return if the registration failed.
		if ($validator->fails()) {
			return redirect()->back()->withErrors(['' => 'An error occurred during registration. Try again.']);
		}

		// Data to populate database.
		$data = $request->all();

		// Register business.
		$business = Business::create($data['business']);

		// Register partner(s).
		$partners = $data['partners'] ?? [];

		foreach ($partners as $partner) {
			$partner['signature'] = $this->storeSignature($partner['signature']);
			$partner['business_id'] = $business->id;

			Partner::create($partner);
		}

		// Add extra data to the user.
		$data['customer_id'] = $this->generateId();
		$data['password'] = Hash::make($request->password);
		$data['signature'] = $this->storeSignature($request->signature);
		$data['business_id'] = $business->id;

		// Get the advisor with less advised users and assign it to this newly created user.
		$advisorAvailable = User::where('role', 'advisor')
			->get()
			->reduce(function($prevAdvisor, $advisor) {
				if ( ! $prevAdvisor ) return $advisor;

				return $advisor->advised_users->count() < $prevAdvisor->advised_users->count()
					? $advisor
					: $prevAdvisor;
			});

		$data['advisor_id'] = $advisorAvailable->id;

		// Register user.
		$user = User::create($data);

		// Store the user's photo if there is any.
		$this->storePhoto($request->file('photo'), $user);

		// Register Loan Request.
		$loanRequest = $this->storeLoanRequest($request, $user->id);

		// Get pretty format of Gross Annual Sales max.
		$grossAnnualSalesMax = explode( '-', $user->business->gross_annual_sales )[1];
		if ( $grossAnnualSalesMax === 'more' ) $grossAnnualSalesMax = 5000001;

		// Prepare the lead data.
		$lead = [
			'web_id' => (int) $user->id,
			'loan_product_id' => (int) $request->loan_product_id,
			'requested_amount' => floatval($request->requested_amount),
			'funds_needed_estimate' => (string) $request->funds_needed_estimate,
			'funds_usage' => (string) $request->funds_usage,
			'communication_channel' => (string) $request->communication_channel,
			'required_documents' => [],
			'ssn' => (string) (Crypt::decrypt($user->ssn_1) . $user->ssn_2),
			'created_at' => Carbon::parse($user->created_at)->format('Y-m-d\TH:i:s.u\Z'),
			'updated_at' => Carbon::parse($user->updated_at)->format('Y-m-d\TH:i:s.u\Z'),
			'signature' => [
				'attachment_name' => (string) Str::after($user->getAttributes()['signature'], 'signatures/'),
				'attachment_body' => (string) $user->signature,
				'attachment_type' => (string) 'image/png',
			],
			'owner_first_name' => (string) $user->first_name,
			'owner_last_name' => (string) $user->last_name,
			'email' => (string) $user->email,
			'title' => (string) $user->title,
			'address_line_1' => (string) $user->address_line_1,
			'address_line_2' => (string) $user->address_line_2,
			'city' => (string) $user->city,
			'state' => (string) $user->state,
			'zip_code' => (string) $user->zip_code,
			'phone_number' => (string) str_replace([' ', 'ext'], '', formatPhoneNumber($user->phone_number)),
			'date_of_birth' => (string) Carbon::parse($user->getAttributes()['date_of_birth'])->format('Y-m-d'),
			'business_ownership' => floatval($user->business_ownership),
			'business' => [
				'web_id' => (int) $user->business->id,
				'name' => (string) $user->business->name,
				'dba' => (string) $user->business->dba,
				'address_line_1' => (string) $user->business->address_line_1,
				'address_line_2' => (string) $user->business->address_line_2,
				'city' => (string) $user->business->city,
				'state' => (string) $user->business->state,
				'zip_code' => (string) $user->business->zip_code,
				'phone_number' => (string) str_replace([' ', 'ext'], '', formatPhoneNumber($user->business->phone_number)),
				'federal_tax_id' => (string) $user->business->federal_tax_id,
				'start_date' => (string) Carbon::parse($user->business->getAttributes()['start_date'])->format('Y-m-d'),
				'website' => (string) $user->business->website,
				'type_of_entity' => (string) $user->business->type_of_entity,
				'industry' => (string) $user->business->industry,
				'gross_annual_sales' => floatval($grossAnnualSalesMax),
				'monthly_sales_volume' => (string) $user->business->monthly_sales_volume,
				'created_at' => (string) Carbon::parse($user->business->created_at)->format('Y-m-d\TH:i:s.u\Z'),
				'updated_at' => (string) Carbon::parse($user->business->updated_at)->format('Y-m-d\TH:i:s.u\Z')
			],
			'partners' => collect($user->partners ?? [])->map(function($partner) {
				return [
					'web_id' => (int) $partner->id,
					'first_name' => (string) $partner->first_name,
					'last_name' => (string) $partner->last_name,
					'title' => (string) $partner->title,
					'address_line_1' => (string) $partner->address_line_1,
					'address_line_2' => (string) $partner->address_line_2,
					'city' => (string) $partner->city,
					'state' => (string) $partner->state,
					'zip_code' => (string) $partner->zip_code,
					'ssn' => (string) (Crypt::decrypt($partner->ssn_1) . $partner->ssn_2),
					'phone_number' => (string) str_replace([' ', 'ext'], '', formatPhoneNumber($partner->phone_number)),
					'date_of_birth' => (string) Carbon::parse($partner->getAttributes()['date_of_birth'])->format('Y-m-d'),
					'signature' => [
						'attachment_name' => (string) Str::after($partner->getAttributes()['signature'], 'signatures/'),
						'attachment_body' => (string) $partner->signature,
						'attachment_type' => (string) 'image/png',
					],
					'business_ownership' => floatval($partner->business_ownership),
					'created_at' => (string) Carbon::parse($partner->created_at)->format('Y-m-d\TH:i:s.u\Z'),
					'updated_at' => (string) Carbon::parse($partner->updated_at)->format('Y-m-d\TH:i:s.u\Z')
				];
			})->all()
		];

		// Store the lead in Salesforce.
		$this->storeLead($lead);

		// Send all notifications needed.
        event(new Registered($user));
		$managers = User::where('role', 'manager')->get();
		Notification::send($managers, new UserCreated($user));

		// Authenticate registered user and redirect to the dashboard.
        Auth::login($user);
        return redirect(RouteServiceProvider::HOME);
    }
}
