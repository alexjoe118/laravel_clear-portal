<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    use HasFactory;

	/**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
		'document_set_id'
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
	 * Retrieve the Document Type's documents.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function documents()
	{
		return $this->hasMany(Document::class);
	}

	/**
	 * Retrieve the Loan Products with this Document Type as required.
	 *
	 * @return mixed
	 */
	public function getLoanProductsAttribute()
	{
		return LoanProduct::whereJsonContains('required_document_types', (string) $this->id)->get();
	}
}
