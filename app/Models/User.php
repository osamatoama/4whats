<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'password',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function scopeCanAccessDashboard(Builder $query): Builder
    {
        return $query->role([UserRole::ADMIN, UserRole::MERCHANT, UserRole::EMPLOYEE]);
    }

    public function children(): HasMany
    {
        return $this->hasMany(related: User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(related: User::class, foreignKey: 'user_id');
    }

    protected function isAdmin(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes): bool => $this->hasRole(roles: UserRole::ADMIN),
        );
    }

    protected function isMerchant(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes): bool => $this->hasRole(roles: UserRole::MERCHANT),
        );
    }

    protected function isEmployee(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes): bool => $this->hasRole(roles: UserRole::EMPLOYEE),
        );
    }

    protected function isParent(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes): bool => $attributes['user_id'] === null,
        );
    }

    protected function isChild(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes): bool => $attributes['user_id'] !== null,
        );
    }
}
