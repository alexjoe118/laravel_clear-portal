<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'key',
		'value',
		'type'
	];

	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'settings';

 	/**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
	public $timestamps = false;

	/**
	 * Add file url when needed.
	 *
	 * @return string
	 */
	public function getValueAttribute($value)
	{
		if ($this->type === 'array') {
			return json_decode($value);
		} else if ($this->type === 'string') {
			return $value;
		} else {
			return asset("storage/$value");
		}

		return $value;
	}
}
