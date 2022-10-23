<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

	/**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'file',
		'filename',
		'document_group_id',
		'document_type_id',
		'user_id'
    ];

	/**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
		'documentGroup',
		'documentType'
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
		$title = $this->filename;

		if ($this->documentGroup) {
			$title .= " - {$this->documentGroup->title}";
		}

		if ($this->documentType) {
			$title .= " - {$this->documentType->title}";
		}

		return $title;
	}

	/**
	 * Retrieve the document's user.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user()
	{
		return $this->belongsTo(User::class);
	}

	/**
	 * Retrieve the document's group.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function documentGroup()
	{
		return $this->belongsTo(DocumentGroup::class);
	}

	/**
	 * Retrieve the document's type.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function documentType()
	{
		return $this->belongsTo(DocumentType::class);
	}
}
