<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    use HasFactory, SoftDeletes;

	/**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
		'name',
		'dba',
		'address_line_1',
		'address_line_2',
		'city',
		'state',
		'zip_code',
		'phone_number',
		'federal_tax_id',
		'start_date',
		'website',
		'type_of_entity',
		'industry',
		'gross_annual_sales',
		'monthly_sales_volume'
    ];

	/**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
		'start_date' => 'date'
    ];

	/**
	 * Display the date in the correct format.
	 *
	 * @return string
	 */
	public function getStartDateAttribute($startDate)
	{
		return Carbon::parse($startDate)->format('m/d/Y');
	}
}
