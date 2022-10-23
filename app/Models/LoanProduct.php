<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanProduct extends Model
{
    use HasFactory;

	/**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
		'loan_group_id',
		'title',
		'article',
		'slug',
		'description',
		'props',
		'learn_more',
		'required_document_types',
		'required_document_sets',
		'order'
    ];

	/**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'props' => 'array',
		'required_document_types' => 'array',
		'required_document_sets' => 'array'
    ];

	/**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
		'required_document_types_data',
		'required_document_types_all',
		'required_document_sets_data'
	];

 	/**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
	public $timestamps = false;

	/**
	 * Retrieve the Loan Product's required Document Types.
	 *
	 * @return array
	 */
	public function getRequiredDocumentTypesAttribute($requiredDocuments)
	{
		if ($requiredDocuments) {
			return array_map('intval', json_decode($requiredDocuments, true));
		}
	}

	/**
	 * Retrieve the Loan Product's required Document Types, including the ones from Document Sets.
	 *
	 * @return array
	 */
	public function getRequiredDocumentTypesAllAttribute()
	{
		return array_merge(
			$this->required_document_types ?? [],
			collect($this->required_document_sets ?? [])
				->map(function ($documentSet) {
					return DocumentType::where('document_set_id', $documentSet)->get()->pluck('id');
				})
				->flatten()
				->all()
		);
	}

	/**
	 * Retrieve the Loan Product's required documents data.
	 *
	 * @return array
	 */
	public function getRequiredDocumentTypesDataAttribute()
	{
		if ($this->required_document_types) {
			return array_map(function ($documentTypeId) {
				return DocumentType::find($documentTypeId);
			}, $this->required_document_types);
		}
	}

	/**
	 * Retrieve the Loan Product's required documents sets.
	 *
	 * @return array
	 */
	public function getRequiredDocumentSetsAttribute($requiredDocuments)
	{
		if ($requiredDocuments) {
			return array_map('intval', json_decode($requiredDocuments, true));
		}
	}

	/**
	 * Retrieve the Loan Product's required documents sets data.
	 *
	 * @return array
	 */
	public function getRequiredDocumentSetsDataAttribute()
	{
		if ($this->required_document_sets) {
			return array_map(function ($documentSet) {
				return DocumentSet::find($documentSet);
			}, $this->required_document_sets);
		}
	}

	/**
	 * Retrieve the related Loan Group.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function loanGroup()
	{
		return $this->belongsTo(LoanGroup::class);
	}

	/**
	 * Retrieve the related Open Approvals.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
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
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('order');
        });
    }
}
