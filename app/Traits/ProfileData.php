<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait ProfileData
{
	/**
     * The "booted" method of the trait.
     *
     * @return void
     */
	public static function bootProfileData()
	{
		static::saving(function ($model) {
			if (isset($model->attributes['ssn'])) {
				$model->generateSsn($model);
			}
        });
	}

	/**
	 * Encrypt the SSN and store it correctly.
	 *
	 * @param mixed $profile
	 * @return mixed
	 */
    public function generateSsn($model)
	{
		$ssn = $model->attributes['ssn'];
		unset($model->ssn);

        if (Str::contains($ssn, '*')) return;

		$model->ssn_1 = Crypt::encrypt(Str::substr($ssn, 0, 5));
		$model->ssn_2 = (string) Str::of($ssn)->substr(5, 4);
    }

	/**
	 * Get the user's full name.
	 *
	 * @return string
	 */
	public function getFullNameAttribute()
	{
		return "{$this->first_name} {$this->last_name}";
	}

	/**
	 * Display the date in the correct format.
	 *
	 * @param string $dateOfBirth
	 * @return string
	 */
	public function getDateOfBirthAttribute($dateOfBirth)
	{
		if ($dateOfBirth) {
			return Carbon::parse($dateOfBirth)->format('m/d/Y');
		}
	}

	/**
	 * Display the masked SSN.
	 *
	 * @return string
	 */
	public function getSsnAttribute()
	{
		if ($this->ssn_2) {
			return '*****' . $this->ssn_2;
		}
	}

	/**
	 * Display the base64 signature.
	 *
	 * @param string $signature
	 * @return string
	 */
	public function getSignatureAttribute($signature)
	{
		if (Storage::disk('local')->exists($signature)) {
			return base64_encode(Storage::disk('local')->get($signature));
		}
	}
}
