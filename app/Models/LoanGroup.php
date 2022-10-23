<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanGroup extends Model
{
    use HasFactory;

	/**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
		'title',
    ];

	/**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
		'loanProducts'
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
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

	/**
	 * Retrieve the Loan Group's products.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
    public function loanProducts()
	{
        return $this->hasMany(LoanProduct::class);
    }
}
