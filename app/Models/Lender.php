<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lender extends Model
{
    use HasFactory, SoftDeletes;

	/**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
		'name',
		'email',
		'phone_number'
    ];

	/**
	 * Retrieve the related Loans.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function loans()
	{
		return $this->hasMany(Loan::class);
	}
}
