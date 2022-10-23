<?php

namespace App\Models;

use App\Traits\TermLengthData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OpenApproval extends Model
{
    use HasFactory, SoftDeletes, TermLengthData;

	/**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
		'user_id',
		'loan_product_id',
		'loan_request_id',
		'term_length',
		'term_length_display',
		'total_credit_limit',
		'interest_rate',
		'rate',
		'factor_rate',
		'draw_fee',
		'misc_fees',
		'multiplier',
		'closing_costs',
		'closing_costs_display',
		'cost_of_capital',
		'cost_of_capital_display',
		'maximum_amount',
		'prepayment_discount',
		'payment_frequency',
		'number_of_payments',
		'maximum_advance',
		'notes',
		'approval_expires'
    ];

	/**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'approval_expires' => 'date',
		'prepayment_discount' => 'boolean',
		'total_credit_limit' => 'float',
		'interest_rate' => 'float',
		'rate' => 'float',
		'factor_rate' => 'float',
		'draw_fee' => 'float',
		'misc_fees' => 'float',
		'multiplier' => 'float',
		'closing_costs' => 'float',
		'cost_of_capital' => 'float',
		'cost_of_capital_display' => 'boolean',
		'maximum_amount' => 'float',
		'maximum_advance' => 'float'
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
		'listing_title',
		'term_length_formatted',
		'closing_costs_formatted'
	];

	/**
	 * Custom title for listings.
	 *
	 * @return string
	 */
	public function getListingTitleAttribute()
	{
		$parts = [];

		if ($this->maximum_amount) {
			$maximumAmount = '$' . number_format($this->maximum_amount);
			$parts[] = "$maximumAmount Max Amount";
		}

		if ($this->maximum_advance) {
			$maximumAdvance = '$' . number_format($this->maximum_advance);
			$parts[] = "$maximumAdvance Max Advance";
		}

		if ($this->term_length) {
			$parts[] = $this->term_length_formatted;
		}

		if ($this->interest_rate) {
			$parts[] = "{$this->interest_rate}% Interest Rate";
		}

		if ($this->rate) {
			$parts[] = "{$this->rate}% Rate";
		}

		if ($this->factor_rate) {
			$parts[] = "{$this->factor_rate}% Factor Rate";
		}

		return join( ' - ', $parts );
	}

	/**
	 * Display the date in the correct format.
	 *
	 * @param string $approvalExpires
	 * @return string
	 */
	public function getApprovalExpiresAttribute($approvalExpires)
	{
		if ($approvalExpires) {
			return Carbon::parse($approvalExpires)->format('m/d/Y');
		}
	}

	/**
	 * Display the Closing Costs with the correct format.
	 *
	 * @param string $closingCosts
	 * @return string
	 */
	public function getClosingCostsFormattedAttribute()
	{
		if ( ! $this->closing_costs && ! $this->closing_costs_display ) return null;

		$closingCosts = $this->closing_costs;
		$display = $this->closing_costs_display ? $this->closing_costs_display : 'percentage';

		switch($display) {
			case 'percentage':
				$closingCosts = "$closingCosts%";
				break;
			case 'dollars':
				$closingCosts = '$' . (floor($closingCosts) == $closingCosts ? number_format($closingCosts, 0) : number_format($closingCosts, 2));
				break;
			case 'waived':
				$closingCosts = 'Waived';
				break;
		}

		return $closingCosts;
	}

	/**
     * Format amount to only 2 decimal
     */
    protected function getCostOfCapitalAttribute( $amount )
    {
        if ( $amount ) {
			return number_format( $amount, 2 );
		}
    }

	/**
	 * Retrieve the related Loan Product.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function loanProduct()
	{
		return $this->belongsTo(LoanProduct::class);
	}

	/**
	 * Retrieve the related Loan Request.
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
	 * Retrieve notes, preference will be for $notes
	 */
	public function notes()
	{
		return $this->notes ? $this->notes : $this->loanRequest->open_approval_notes;
	}

	/**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
		static::saving(function ($openApproval) {

			// Autofill Cost of Capital based in a calculation.
			if ($openApproval->rate && $openApproval->term_length) {
				$openApproval->cost_of_capital = $openApproval->rate / $openApproval->term_length;
			}

			// Autofill Expiration Date.
			if (! $openApproval->approval_expires) {
				$openApproval->approval_expires = Carbon::today()->addWeek();
			}
		});
    }
}
