<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Notifications\LoanRequestCreated;
use Illuminate\Support\Facades\Notification;
use App\Notifications\LoanRequestCreatedAdmin;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanRequest extends Model
{
    use HasFactory, SoftDeletes;

	/**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
		'user_id',
		'loan_product_id',
		'requested_amount',
		'funds_needed_estimate',
		'funds_usage',
		'communication_channel',
		'documents',
		'interest_in_working_capital_options',
		'open_approval_notes'
    ];

	/**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'documents' => 'array',
		'interest_in_working_capital_options' => 'boolean'
    ];

	/**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
	protected $dates = [
		'deleted_at'
	];

	/**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
		'loanProduct',
		'user'
	];

	/**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
		'listing_title'
	];

	/**
	 * Custom title for listings.
	 *
	 * @return string
	 */
	public function getListingTitleAttribute()
	{
		$title = '';

		$fundedLoan = $this->loans->first(function($loan) {
			return $loan->funded;
		});

		if ($fundedLoan) {
			$title .= 'Funded - ';
		} else {
			$title .= Carbon::parse($this->created_at)->format('m/d/Y') . ' - ';
		}

		$title .= "{$this->loanProduct->title} of \${$this->requested_amount}";

		if ($openApprovals = $this->openApprovals->count()) {
			$title .= " - {$openApprovals} Open Approval(s)";
		}

		return $title;
	}

	/**
	 * Display a formatted amount.
	 *
	 * @return string
	 */
	public function getRequestedAmountAttribute($requestedAmount)
	{
		return number_format($requestedAmount);
	}

	/**
	 * Retrieve the Documents information.
	 */
	public function getDocumentsAttribute($documents)
	{
		if ($documents) {
			return collect(json_decode($documents, true))->map(function($document) {
				return Document::find($document);
			});
		}
	}

	/**
	 * Retrieve the related Loans.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function loans()
	{
		return $this->hasMany(Loan::class);
	}

	/**
	 * Retrieve the related Product.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function loanProduct()
	{
		return $this->belongsTo(LoanProduct::class);
	}

	/**
	 * Retrieve the related User.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user()
	{
		return $this->belongsTo(User::class);
	}

	/**
	 * Retrieve the related Open Approvals.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\hasMany
	 */
	public function openApprovals()
	{
		return $this->hasMany(OpenApproval::class);
	}

	/**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
		static::created(function ($loanRequest) {

			// Notify user via email.
			$user = User::find($loanRequest->user_id);
			$user->notify(new LoanRequestCreated($loanRequest));

			// Notify admins by email.
			$admins = User::where('role', 'manager')
				->orWhere('id', $user->advisor_id)
				->get();
			Notification::send($admins, new LoanRequestCreatedAdmin($loanRequest));
		});
    }
}
