<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'bio',
        'avatar_path',
        'tebex_url',
        'github_url',
        'discord_invite_url',
        'youtube_url',
        'website_url',
        'paypal_email',
        'is_verified',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
            'is_verified' => 'boolean',
            'balance' => 'decimal:2',
            'certification_requested_at' => 'datetime',
            'banned_at' => 'datetime',
            'banned_until' => 'datetime',
            'hide_content' => 'boolean',
        ];
    }

    /**
     * @return HasMany<Resource>
     */
    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }

    /**
     * @return HasMany<Service>
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    /**
     * @return HasMany<CreatorRequest>
     */
    public function creatorRequests(): HasMany
    {
        return $this->hasMany(CreatorRequest::class);
    }

    public function isCreator(): bool
    {
        return in_array($this->role, ['creator', 'admin'], true);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function hasRole(string ...$roles): bool
    {
        return $this->role === 'admin' || in_array($this->role, $roles, true);
    }

    public function approvedResourcesCount(): int
    {
        return $this->resources()->approved()->count();
    }

    public function hasRequestedCreatorStatus(): bool
    {
        return $this->certification_requested_at !== null;
    }

    public function meetsCreatorRequestThreshold(): bool
    {
        return $this->approvedResourcesCount() >= 3;
    }

    /**
     * True if the user is currently under an active ban — permanent, or
     * temporary and not yet expired.
     */
    public function isBanned(): bool
    {
        if ($this->banned_at === null) {
            return false;
        }

        return $this->banned_until === null || $this->banned_until->isFuture();
    }

    /**
     * True if the user is banned AND the admin chose to also hide their
     * resources/services from the public site (an admin can ban an account
     * — blocking login — while leaving its published content visible).
     */
    public function hidesContent(): bool
    {
        return $this->isBanned() && $this->hide_content;
    }

    /**
     * Scope: users whose content should currently be shown publicly, i.e.
     * NOT (banned AND hide_content) — the negation of hidesContent(),
     * expressed in SQL for use in resource/service listing queries.
     */
    public function scopeContentVisible(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->whereNull('banned_at')
                ->orWhere(function (Builder $q2) {
                    $q2->whereNotNull('banned_until')->where('banned_until', '<=', now());
                })
                ->orWhere('hide_content', false);
        });
    }
}
