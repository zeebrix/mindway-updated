<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'image',
        'user_type',
        'status',
        'two_factor_enabled',
        'google_2fa_secret',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google_2fa_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'two_factor_enabled' => 'boolean',
        'password' => 'hashed',
    ];

    // --- RELATIONSHIPS ---

    /**
     * Get the customer-specific details associated with the user.
     */
    public function customerDetail(): HasOne
    {
        return $this->hasOne(CustomerDetail::class);
    }
    public function ProgramDetail(): HasOne
    {
        return $this->hasOne(ProgramDetail::class);
    }
    public function programPlan(): HasOne
    {
        return $this->hasOne(ProgramPlan::class);
    }
    public function programDepartment(): HasOne
    {
        return $this->hasOne(ProgramDepartment::class);
    }

    /**
     * Get the user preferences associated with the user.
     */
    public function preference(): HasOne
    {
        return $this->hasOne(UserPreference::class);
    }

    /**
     * Get the Google OAuth token for the user.
     */
    public function googleToken(): HasOne
    {
        return $this->hasOne(GoogleToken::class);
    }

    /**
     * Get the slots offered by the user (if they are a counsellor).
     */
    public function slots(): HasMany
    {
        return $this->hasMany(Slot::class, 'counselor_id');
    }

    /**
     * Get the bookings made by the user.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'user_id');
    }
    public function availabilities(): HasMany
    {
        return $this->hasMany(Availability::class, 'user_id');
    }
    public function scopeCustomers(Builder $query): Builder
    {
        return $query->where('user_type', 'customer');
    }
    public function scopeCounsellors(Builder $query): Builder
    {
        return $query->where('user_type', 'counsellor');
    }
    public function scopeProgramOwners(Builder $query): Builder
    {
        return $query->where('user_type', 'program');
    }
    public function scopeAdmins(Builder $query): Builder
    {
        return $query->where('user_type', 'admin');
    }
    public function scopeCsms(Builder $query): Builder
    {
        return $query->where('user_type', 'csm');
    }
    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'program_customers',
            'customer_id',
            'program_id'
        );
    }

    /**
     * The customers that belong to this user (as a program).
     */
    public function allCustomers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'program_customers',
            'program_id',
            'customer_id'
        );
    }
     public function programDepartments()
    {
        return $this->hasMany(ProgramDepartment::class);
    }
}
