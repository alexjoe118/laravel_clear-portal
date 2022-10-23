<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentSet extends Model
{
    use HasFactory;

	/**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
	protected $fillable = [
		'title'
	];

	/**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
	protected $appends = [
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
	 * Retrieve the related Documents.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function documents()
	{
		return $this->hasMany(Document::class);
	}

	/**
	 * Retrieve the related Document Types.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function documentTypes()
	{
		return $this->hasMany(DocumentType::class);
	}

	/**
	 * Retrieve the related Loan Products.
	 *
	 * @return array
	 */
	public function getLoanProductsAttribute()
	{
		return LoanProduct::whereJsonContains('required_document_sets', (string) $this->id)->get();
	}
}
