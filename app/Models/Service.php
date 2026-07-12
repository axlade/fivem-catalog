<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'starting_price',
        'category',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'starting_price' => 'decimal:2',
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::creating(function (Service $service) {
            if (empty($service->slug)) {
                $service->slug = static::uniqueSlug($service->title);
            }
        });
    }

    protected static function uniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = "{$original}-{$count}";
            $count++;
        }

        return $slug;
    }

    /**
     * @return BelongsTo<User, Service>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<ServiceReview>
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(ServiceReview::class)->latest();
    }

    /**
     * Average star rating from this service's reviews, rounded to one
     * decimal. Null when there are no reviews yet. Assumes `reviews` is
     * already eager-loaded by the caller.
     */
    public function averageRating(): ?float
    {
        return $this->reviews->isNotEmpty()
            ? round($this->reviews->avg('rating'), 1)
            : null;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOfCategory(Builder $query, ?string $category): Builder
    {
        return $category ? $query->where('category', $category) : $query;
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $term
            ? $query->where(function (Builder $q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
            })
            : $query;
    }
}
