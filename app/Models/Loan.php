<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Traits\TermLengthData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
    use HasFactory, SoftDeletes, TermLengthData;

	/**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
		'user_id',
		'loan_request_id',
		'open_approval_id',
		'loan_product_id',
		'lender_id',
		'loan_amount',
		'credit_limit',
		'payback_amount',
		'payment_amount',
		'payment_frequency',
		'payment_day',
		'number_of_payments',
		'term_length',
		'term_length_display',
		'payoff_date',
		'estimated_renewal_date',
		'estimated_payoff_date',
		'funded',
		'funded_date',
		'default',
		'contract_documents'
    ];

	/**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
		'loanProduct',
		'user',
		'lender',
		'openApproval'
	];

	/**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'payoff_date' => 'date',
		'estimated_renewal_date' => 'date',
		'estimated_payoff_date' => 'date',
		'funded' => 'boolean',
		'funded_date' => 'date',
		'default' => 'boolean',
		'contract_documents' => 'array'
    ];

	/**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
		'listing_title',
		'application_status',
		'remaining_balance',
		'term_length_formatted'
	];

	/**
	 * Custom title for the listing.
	 *
	 * @return string
	 */
	public function getListingTitleAttribute()
	{
		$title = '';

		if ($this->funded) {
			$title = 'Funded - ';
		}

		$title .= $this->loanProduct->title;

		if ($this->term_length) {
			$title .= " - {$this->term_length_formatted}";
		}

		return $title;
	}

	/**
	 * Display formatted amount.
	 *
	 * @param string $creditLimit
	 * @return string
	 */
	public function getCreditLimitAttribute($creditLimit)
	{
		if ($creditLimit) {
			return number_format($creditLimit);
		}
	}

	/**
	 * Display formatted amount.
	 *
	 * @param string $loanAmount
	 * @return string
	 */
	public function getLoanAmountAttribute($loanAmount)
	{
		if ($loanAmount) {
			return number_format($loanAmount);
		}
	}

	/**
	 * Display formatted amount.
	 *
	 * @param string $paybackAmount
	 * @return string
	 */
	public function getPaybackAmountAttribute($paybackAmount)
	{
		if ($paybackAmount) {
			return number_format($paybackAmount);
		}
	}

	/**
	 * The Loan remaining balance.
	 *
	 * @return float
	 */
	public function getRemainingBalanceAttribute()
	{
		if (! $this->funded_date || ! $this->payment_frequency) return;

		// Payments always start on the upcoming month.
		$nextMonth = Carbon::parse($this->funded_date)->addMonth()->day(1);

		if ($this->payment_frequency === 'daily') {

			// Calculate how many daily payments happened already.
			$numberOfPaymentsMade = $nextMonth->diffInWeekdays(Carbon::today());
		} else {

			// Calculate how many weekly/semi-monthly/monthly payments happened already.
			switch ($this->payment_frequency) {
				case 'weekly':
					$firstPaymentDate = $nextMonth->next(Str::ucfirst($this->payment_day));
					$dateIncrement = function($date) {
						return $date->addWeek();
					};
					break;

				case 'semi-monthly':
					$firstPaymentDate = $nextMonth->day($this->payment_day);
					$dateIncrement = function($date) {
						return $date->addWeeks(2);
					};
					break;

				case 'monthly':
					$firstPaymentDate = $this->payment_day === 'last'
						? $nextMonth->endOfMonth()
						: $nextMonth->day($this->payment_day);
					$dateIncrement = function($date) {
						return $date->addMonth();
					};
					break;
			}

			$numberOfPaymentsMade = 0;
			for ($date = $firstPaymentDate; $date->lte(Carbon::today()); $dateIncrement($date)) {
				$numberOfPaymentsMade++;
			}
		}

		// Retrieve the remaining balance.
		// The payback amount minus how much has been paid already.
		$remainingBalance = $numberOfPaymentsMade !== $this->number_of_payments
			? $this->original['payback_amount'] - $numberOfPaymentsMade * $this->original['payment_amount']
			: 0;

		return number_format($remainingBalance);
	}

	/**
	 * Display the date in the correct format.
	 *
	 * @param string $payoffDate
	 * @return string
	 */
	public function getPayoffDateAttribute($payoffDate)
	{
		if ($payoffDate) {
			return Carbon::parse($payoffDate)->format('m/d/Y');
		}
	}

	/**
	 * Display the date in the correct format.
	 *
	 * @param string $estimatedPayoffDate
	 * @return string
	 */
	public function getEstimatedPayoffDateAttribute($estimatedPayoffDate)
	{
		if ($estimatedPayoffDate) {
			return Carbon::parse($estimatedPayoffDate)->format('m/d/Y');
		}
	}

	/**
	 * Display the date in the correct format.
	 *
	 * @param string $estimatedRenewalDate
	 * @return string
	 */
	public function getEstimatedRenewalDateAttribute($estimatedRenewalDate)
	{
		if ($estimatedRenewalDate) {
			return Carbon::parse($estimatedRenewalDate)->format('m/d/Y');
		}
	}

	/**
	 * Display the date in the correct format.
	 *
	 * @param string $fundedDate
	 * @return string
	 */
	public function getFundedDateAttribute($fundedDate)
	{
		if ($fundedDate) {
			return Carbon::parse($fundedDate)->format('m/d/Y');
		}
	}

	public function getApplicationStatusAttribute()
	{
		// Return if loan was paid already.
		if ($this->payoff_date || $this->default) return false;

		if (
			in_array($this->loan_product_id, [1, 2]) &&
			(strtotime('now') >= strtotime($this->estimated_renewal_date))
		) {
			return 'Renewal';
		}

		return 'Additional Financing';
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
	 * Retrieve the related Request.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function loanRequest()
	{
		return $this->belongsTo(LoanRequest::class);
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
	 * Retrieve the related Lender.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function lender()
	{
		return $this->belongsTo(Lender::class);
	}

	/**
	 * Retrieve the related Open Approval.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function openApproval()
	{
		return $this->belongsTo(OpenApproval::class);
	}
}
