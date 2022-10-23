<?php

namespace App\Models;

use App\Traits\ProfileData;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, ProfileData, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
		'customer_id',
		'first_name',
		'last_name',
		'email',
		'new_email',
		'title',
		'password',
		'address_line_1',
		'address_line_2',
		'city',
		'state',
		'zip_code',
		'phone_number',
		'cell_phone_number',
		'date_of_birth',
		'ssn',
		'ssn_1',
		'ssn_2',
		'approximate_credit_score',
		'signature',
		'photo',
		'business_id',
		'business_ownership',
		'advisor_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
		'date_of_birth' => 'date'
    ];

	/**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
		'business',
		'partners',
		'documents'
	];

	/**
	* The accessors to append to the model's array form.
	*
	* @var array
	*/
	protected $appends = [
		'listing_title',
		'full_name',
		'full_name_id',
		'ssn'
	];

	/**
	 * Custom title for listings.
	 *
	 * @return string
	 */
	public function getListingTitleAttribute()
	{
		if ($this->role === 'user' && $this->business) {
			return "{$this->business->name} #{$this->customer_id}";
		} else {
			return $this->full_name_id;
		}
	}

	/**
	 * Retrieve the related full name and customer ID.
	 *
	 * @return string
	 */
	public function getFullNameIdAttribute()
	{
		$fullNameId = $this->full_name;

		if ($this->customer_id) {
			$fullNameId .= " #{$this->customer_id}";
		}

		return $fullNameId;
	}

	/**
	 * Add fallback image if the user does not have a photo uploaded.
	 *
	 * @return string
	 */
	public function getPhotoAttribute($photo)
	{
		return $photo ? asset('storage/' . $photo) : asset('images/dashboard/user-placeholder.png');
	}

	/**
	 * Retrieve the related Advisor.
	 *
	 * @return App\Models\User
	 */
	public function getAdvisorAttribute()
	{
		if ($this->advisor_id) {
			return User::find($this->advisor_id);
		}
	}

	/**
	 * Retrieve the related Users for Advisors.
	 */
	public function getAdvisedUsersAttribute()
	{
		if ($this->role === 'advisor') {
			return User::where('advisor_id', $this->id)->get();
		}
	}

	/**
	 * Remove symbols from phone number.
	 */
	public function setPhoneNumberAttribute($phoneNumber)
	{
		$this->attributes['phone_number'] = Str::of($phoneNumber)
			->replace('(', '')
			->replace(')', '')
			->replace('-', '')
			->replace(' ', '');
	}

	/**
	 * If the user is not admin.
	 *
	 * @return boolean
	 */
	public function isNotAdmin()
	{
		return $this->role === 'user';
	}

	/**
	 * If the user is admin.
	 *
	 * @return boolean
	 */
	public function isAdmin()
	{
		return in_array($this->role, ['advisor', 'manager']);
	}

	/**
	 * If the user is admin with advisor privileges.
	 *
	 * @return boolean
	 */
	public function isAdvisor()
	{
		return $this->role === 'advisor';
	}

	/**
	 * If the user is admin with manager privileges.
	 *
	 * @return boolean
	 */
	public function isManager()
	{
		return $this->role === 'manager';
	}

	/**
	 * Retrieve the related Notifications.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function notifications()
	{
		return $this->hasMany(Notification::class);
	}

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
	 * Retrieve the related Business.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function business()
	{
		return $this->belongsTo(Business::class);
	}

	/**
	 * Retrieve the related Partners.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function partners()
	{
		return $this->hasMany(Partner::class, 'business_id', 'business_id');
	}

	/**
	 * Retrieve the related Open Approvals.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function openApprovals()
	{
		return $this->hasMany(OpenApproval::class)->where('approval_expires', '>=', now());
	}

	/**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
		static::addGlobalScope('order', function (Builder $builder) {
			$builder->orderBy(Business::select('name')->whereColumn('businesses.id', 'users.business_id'));
		});
    }
}
