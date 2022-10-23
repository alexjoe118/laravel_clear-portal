<?php

namespace App\Models;

use App\Traits\ProfileData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Partner extends Model
{
    use HasFactory, ProfileData, SoftDeletes;

	/**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
		'first_name',
		'last_name',
		'title',
		'address_line_1',
		'address_line_2',
		'city',
		'state',
		'zip_code',
		'phone_number',
		'date_of_birth',
		'ssn',
		'ssn_1',
		'ssn_2',
		'approximate_credit_score',
		'signature',
		'business_id',
		'business_ownership'
    ];

	/**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
		'date_of_birth' => 'date'
    ];

	/**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
		'business'
	];

	/**
	* The accessors to append to the model's array form.
	*
	* @var array
	*/
	protected $appends = [
		'full_name',
		'ssn'
	];

	/**
	 * Retrieve the related Business.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function business()
	{
		return $this->belongsTo(Business::class);
	}

	/**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::deleting(function ($partner) {
            if ($partner->attributes['signature']) {
				Storage::disk('local')->delete($partner->attributes['signature']);
			}
        });
    }
}
