<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentGroup extends Model
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
	 * Retrieve the Document Group's documents.
	 */
	public function documents()
	{
		return $this->hasMany(Document::class);
	}
}
