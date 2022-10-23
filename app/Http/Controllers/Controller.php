<?php

namespace App\Http\Controllers;

use App\Rules\SSN;
use App\Models\Document;
use App\Models\LoanProduct;
use App\Models\LoanRequest;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	public function __construct()
    {
        $this->validationRules = [
			'credentials' => [
				'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
				'email_confirmation' => ['required', 'string', 'email', 'max:255', 'same:email'],
				'password' => ['required', Rules\Password::defaults()],
				'password_confirmation' => ['required', Rules\Password::defaults(), 'same:password'],
				'loan_product_id' => ['required', 'integer', 'exists:loan_products,id'],
				'requested_amount' => ['required', 'numeric'],
				'funds_needed_estimate' => ['required', 'string'],
				'funds_usage' => ['required', 'string'],
				'communication_channel' => ['required', 'string'],
				// 'required_documents.*' => ['sometimes', 'file'],
			],
			'business' => [
				'business.name' => ['required', 'string'],
				'business.dba' => ['sometimes', 'nullable', 'string'],
				'business.address_line_1' => ['required', 'string'],
				'business.address_line_2' => ['sometimes', 'nullable', 'string'],
				'business.city' => ['required', 'string'],
				'business.state' => ['required', 'string'],
				'business.zip_code' => ['required', 'numeric', 'digits:5'],
				'business.phone_number' => ['required'],
				'business.federal_tax_id' => ['required', 'numeric', 'digits:9'],
				'business.start_date' => ['required', 'date'],
				'business.website' => ['sometimes', 'nullable', 'string'],
				'business.type_of_entity' => ['required', 'string'],
				'business.industry' => ['required', 'string'],
				'business.gross_annual_sales' => ['required', 'string'],
				'business.monthly_sales_volume' => ['required', 'string']
			],
			'personal' => [
				'photo' => ['sometimes', 'nullable', 'mimes:jpg,png'],
				'first_name' => ['required', 'string'],
				'last_name' => ['required', 'string'],
				'phone_number' => ['required'],
				'title' => ['sometimes', 'nullable', 'string'],
				'date_of_birth' => ['required', 'date'],
				'address_line_1' => ['required', 'string'],
				'address_line_2' => ['sometimes', 'nullable', 'string'],
				'city' => ['required', 'string'],
				'state' => ['required', 'string'],
				'zip_code' => ['required', 'numeric', 'digits:5'],
				'ssn' => ['required', new SSN],
				'approximate_credit_score' => ['required', 'string'],
				'business_ownership' => ['required', 'numeric', 'between:0,100'],
				'signature' => ['sometimes', 'string'],
				'partners.*.first_name' => ['sometimes', 'string'],
				'partners.*.last_name' => ['sometimes', 'string'],
				'partners.*.phone_number' => ['sometimes'],
				'partners.*.title' => ['sometimes', 'nullable', 'string'],
				'partners.*.date_of_birth' => ['sometimes', 'date'],
				'partners.*.address_line_1' => ['sometimes', 'string'],
				'partners.*.address_line_2' => ['sometimes', 'nullable', 'string'],
				'partners.*.city' => ['sometimes', 'string'],
				'partners.*.state' => ['sometimes', 'string'],
				'partners.*.zip_code' => ['sometimes', 'numeric', 'digits:5'],
				'partners.*.ssn' => ['sometimes', new SSN],
				'partners.*.approximate_credit_score' => ['sometimes', 'string'],
				'partners.*.business_ownership' => ['sometimes', 'numeric', 'between:0,100'],
				'partners.*.signature' => ['sometimes', 'string']
			],
			'loan-request' => [
				'loan_product_id' => ['required', 'integer', 'exists:loan_products,id'],
				'requested_amount' => ['required', 'numeric'],
				'funds_needed_estimate' => ['required', 'string'],
				'funds_usage' => ['required', 'string'],
				'communication_channel' => ['required', 'string'],
				'required_documents.*' => ['sometimes', 'file'],
			]
		];
    }

	/**
	 * Validate the form submission.
	 *
	 * @param Request $request
	 * @param string|array $group
	 * @param array $exceptions
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	public function validateForm(Request $request, $group, $exceptions = []) {

		// When only one group of rules was passed.
		if (is_string($group)) {
			$rules = $this->validationRules[$group];

		// When multiple groups of rules were passed.
		} else {
			$rules = [];

			foreach ($this->validationRules as $index => $rulesGroup) {
				if (in_array($index, $group)) {
					$rules = array_merge($rules, $rulesGroup);
				}
			}
		}

		// Remove field exceptions.
		$rules = collect($rules)->filter(function($rule, $field) use($exceptions) {
			return ! in_array($field, $exceptions);
		})->toArray();

		$validator = Validator::make($request->all(), $rules);

		/**
		 * Specific rules.
		 */
		$errors = [];

		// Validate if the sum of business ownerships is equal to 100.
		if ($request->business_ownership) {
			$partnersOwnership = $request->partners
				? array_sum(array_column($request->partners, 'business_ownership'))
				: 0;
			$totalOwnership = $request->business_ownership + $partnersOwnership;

			if ($totalOwnership != 100) {
				$errors['business_ownership'] = 'The sum of all the business ownership percentages should be equal to 100%.';
			}
		}

		// Validate if the provided SSNs are not blacklisted.
		if ($request->ssn) {
			$blacklist = globalSettings('ssn_blacklist');

			if (is_array($blacklist)) {
				$ssnError = function ($ssn) {
					$ssn = formatSsn($ssn);
					return "The SSN {$ssn} is not allowed to join the Portal.";
				};

				if (in_array($request->ssn, $blacklist)) {
					$errors['ssn'] = $ssnError($request->ssn);
				}

				foreach ($request->partners ?? [] as $index => $partner) {
					if (in_array($partner['ssn'], $blacklist)) {
						$errors["partners.$index.ssn"] = $ssnError($partner['ssn']);
					}
				}
			}
		}

		// Validate if the provided Federal Tax ID is not blacklisted.
		if ($request->business && $federalTaxID = $request->business['federal_tax_id']) {
			$blacklist = globalSettings('federal_tax_id_blacklist');

			if (is_array($blacklist) && in_array($federalTaxID, $blacklist)) {
				$errors['business.federal_tax_id'] = "The Federal Tax ID $federalTaxID is not allowed to join the Portal.";
			}
		}

		$validator->after(function ($validator) use($errors) {
			foreach($errors as $field => $error) {
				$validator->errors()->add($field, $error);
			}
		});

		return $validator;
	}

	/**
	 * Store a Loan Request for a certain user.
	 *
	 * @param Illuminate\Http\Request $request
	 * @param integer $userId
	 * @return object
	 */
	public function storeLoanRequest($request, $userId)
	{
		// Validate the documents' size.
		$validator = Validator::make($request->all(), $this->validationRules['loan-request'], [
			'required_documents.*.uploaded' => 'Max allowed upload size for each file is 8MB'
		]);

		if ($validator->fails()) {
			return [
				'error' => true,
				'validator' => $validator
			];
		}

		// Get documents previously uploaded by user.
		$documents = $request->required_documents_existing[$request->loan_product_id] ?? [];

		// Store new documents.
		$documentFiles = collect($request->file('required_documents') ?? []);

		$documentFiles->each(function($documentFile, $index) use($request, $userId, &$documents) {
			$documentTypeId = $request->required_documents_type_id[$index];
			$document = $this->storeDocument($documentFile, null, $userId, $documentTypeId);
			$documents[] = $document->id;
		});

		$data = $request->all();
		$data['user_id'] = $userId;
		$data['documents'] = ! empty($documents) ? $documents : null;

		return LoanRequest::create($data);
	}

	/**
	 * Store document for certain user.
	 *
	 * @param \Illuminate\Http\UploadedFile|\Illuminate\Http\UploadedFile[]|array|null $document
	 * @param integer $groupId
	 * @param integer $userId
	 * @return object
	 */
	public function storeDocument($document, $groupId, $userId, $typeId = null)
	{
		if (! $document) return;

		// Store the document file.
		$path = uniqid() . '.' . $document->extension();
		$originalName = pathinfo($document->getClientOriginalName(), PATHINFO_FILENAME);
		$document->storeAs('documents', $path, 'local');

		// Suffix repeated file names to avoid duplicates.
		$i = 0;
		do {
			$suffix = $i ? ('-' . $i) : '';
            $filename = $originalName . $suffix . '.' . $document->getClientOriginalExtension();
			$i++;
        } while (Document::where([
			'filename' => $filename,
			'user_id' => $userId
		])->first());

		// Store document information in database.
		return Document::create([
			'file' => 'documents/' . $path,
			'filename' => $filename,
			'user_id' => $userId,
			'document_group_id' => $groupId,
			'document_type_id' => $typeId ? (int) $typeId : null
		]);
	}

	/**
	 * Store photo for certain profile.
	 *
	 * @param \Illuminate\Http\UploadedFile|\Illuminate\Http\UploadedFile[]|array|null $photo
	 * @param mixed $profile
	 * @return void
	 */
	public function storePhoto($photo, $profile)
	{
		if (! $photo) return;

		// Delete previous photo.
		if ($profile->photo) Storage::delete($profile->photo);

		// Get uploaded file and set a filename based on the user's email.
		$fileName = uniqid() . '.' . $photo->extension();

		// Resize photo.
		$photoFile = Image::make($photo);
		$width = 500;
		$height = 500;
		$photoFile->width() > $photoFile->height() ? $width = null : $height = null;
		$photoFile->resize($width, $height, function ($constraint) {
			$constraint->aspectRatio();

			// Prevent scaling image to a larger dimension than the original.
			$constraint->upsize();
		})->encode('jpg');

		// Upload new photo.
		Storage::put('photos/' . $fileName, $photoFile);

		// Store the photo's path on database.
		$profile->photo = 'photos/' . $fileName;
		$profile->save();
	}

	/**
	 * Store lead on Salesforce.
	 *
	 * @param array $lead
	 * @return void
	 */
	public function storeLead($lead)
	{
		$client = new Client;

		$auth = $client->post(config('app.sf_base_uri') .'/services/oauth2/token', [
			RequestOptions::FORM_PARAMS => [
				'grant_type' => 'password',
				'client_id' => config('app.sf_client_id'),
				'client_secret' => config('app.sf_client_secret'),
				'username' => config('app.sf_username'),
				'password' => config('app.sf_password')
			]
		]);

		$auth = json_decode($auth->getBody());

		try {
			$client->post($auth->instance_url . '/services/apexrest/api/v1.0/NewLeadIntegration', [
				RequestOptions::HEADERS => [
					'Authorization' => $auth->token_type . ' ' . $auth->access_token
				],
				RequestOptions::JSON => $lead
			]);
		} catch (\GuzzleHttp\Exception\ServerException $error) {
			dd($error->getResponse()->getBody()->getContents());
		}
	}

	/**
	 * Response after some CRUD action.
	 *
	 * @param string|array $resource
	 * @param string $actionKey
	 * @param bool $plural
	 * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
	 */
	public function responseWithMessage($resource, $action, $plural = false)
	{
		$actions = [
			'store' => 'created',
			'update' => 'updated',
			'destroy' => 'deleted',
			'upload' => 'uploaded',
			'submit' => 'submitted',
			'notify' => 'notified',
			'restore' => 'restored'
		];

		if ($action === 'reassign') {
			$reassignables = $resource[0]->{$resource[2] ?? Str::of($resource[1])->plural()->camel()}
				->map(function($reassignable) use($resource) {
					$route = route('admin.' . Str::singular($resource[1]) . '.edit', ['id' => $reassignable->id]);
					$reassignableTitle = getTitle($reassignable);

					return "<a href=\"$route\">$reassignableTitle</a>";
				})->join('<br>');

			$groupTitle = getTitle($resource[0]);

			return redirect()->back()->withErrors([
				'' => "There are " . Str::of($resource[1])->replace('-', ' ')->title() . " assigned to \"{$groupTitle}\". Reassign them before deleting it:<br>$reassignables"
			]);
		}

		$wasOrWere = $plural ? 'were' : 'was';

		return redirect()->back()->with([
			'message' => "$resource $wasOrWere $actions[$action] successfully!"
		]);
	}

	/**
	 * Store the base64 signature as a .png image.
	 *
	 * @param string $signature
	 * @return string
	 */
	public function storeSignature($signature)
	{
		$file = Str::of($signature)
			->replace('data:image/png;base64,', '')
			->replace(' ', '+');
		$filename = uniqid() . '.png';
		Storage::disk('local')->put('signatures/' . $filename, base64_decode($file));

		return 'signatures/' . $filename;
	}

}
