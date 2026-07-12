<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'video_urls',
        'category',
        'framework',
        'price',
        'download_url',
        'download_file_path',
        'tebex_url',
        'distribution_type',
        'thumbnail_path',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'video_urls' => 'array',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::creating(function (Resource $resource) {
            if (empty($resource->slug)) {
                $resource->slug = static::uniqueSlug($resource->title);
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
     * @return BelongsTo<User, Resource>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsToMany<Tag>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * @return HasMany<ResourceRating>
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(ResourceRating::class)->latest();
    }

    /**
     * @return HasMany<ResourceComment>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(ResourceComment::class)->latest();
    }

    /**
     * @return HasMany<ResourceUpdate>
     */
    public function updates(): HasMany
    {
        return $this->hasMany(ResourceUpdate::class)->latest();
    }

    /**
     * Additional showcase images beyond the cover thumbnail, in display order.
     *
     * @return HasMany<ResourceImage>
     */
    public function images(): HasMany
    {
        return $this->hasMany(ResourceImage::class)->orderBy('position')->orderBy('id');
    }

    /**
     * The full ordered media set for the detail-page carousel: the cover
     * thumbnail, then gallery images, then showcase videos. Assumes `images`
     * is already eager-loaded by the caller.
     *
     * @return array<int, array{type: string, src: string}>
     */
    public function mediaSlides(): array
    {
        $slides = [];

        if ($this->thumbnail_path && Storage::disk('public')->exists($this->thumbnail_path)) {
            $slides[] = ['type' => 'image', 'src' => Storage::disk('public')->url($this->thumbnail_path)];
        }

        foreach ($this->images as $image) {
            if (Storage::disk('public')->exists($image->path)) {
                $slides[] = ['type' => 'image', 'src' => Storage::disk('public')->url($image->path)];
            }
        }

        foreach ($this->video_urls ?? [] as $videoUrl) {
            $embed = $this->embedUrlFor($videoUrl);

            $slides[] = $embed
                ? ['type' => 'video', 'src' => $embed]
                : ['type' => 'video-external', 'src' => $videoUrl];
        }

        return $slides;
    }

    /**
     * Average star rating for this resource, rounded to one decimal. Null
     * when there are no ratings yet. Assumes `ratings` is already
     * eager-loaded by the caller.
     */
    public function averageRating(): ?float
    {
        return $this->ratings->isNotEmpty()
            ? round($this->ratings->avg('rating'), 1)
            : null;
    }

    public function isFree(): bool
    {
        return (float) $this->price === 0.0;
    }

    public function hasDownloadFile(): bool
    {
        return ! empty($this->download_file_path);
    }

    public function downloadLink(): ?string
    {
        if ($this->hasDownloadFile()) {
            return Storage::disk('public')->url($this->download_file_path);
        }

        return $this->download_url;
    }

    public function hasVideos(): bool
    {
        return ! empty($this->video_urls);
    }

    /**
     * Convert a stored showcase video URL into an embeddable player URL.
     * Returns null if the URL isn't a recognized YouTube link (caller can
     * fall back to a plain "watch externally" link in that case).
     */
    public function embedUrlFor(string $url): ?string
    {
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/', $url, $matches)) {
            return 'https://www.youtube.com/embed/'.$matches[1];
        }

        return null;
    }

    /**
     * Average number of seconds visitors spend on this resource's page,
     * based on samples reported by the browser when a visit ends. Null
     * when there isn't at least one sample yet.
     */
    public function averageViewDurationSeconds(): ?int
    {
        if ($this->view_duration_samples < 1) {
            return null;
        }

        return intdiv($this->total_view_duration_seconds, $this->view_duration_samples);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeOfCategory(Builder $query, ?string $category): Builder
    {
        return $category ? $query->where('category', $category) : $query;
    }

    public function scopeOfFramework(Builder $query, ?string $framework): Builder
    {
        return $framework ? $query->where('framework', $framework) : $query;
    }

    public function scopePriceFilter(Builder $query, ?string $price): Builder
    {
        return match ($price) {
            'free' => $query->where('price', 0),
            'paid' => $query->where('price', '>', 0),
            default => $query,
        };
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
