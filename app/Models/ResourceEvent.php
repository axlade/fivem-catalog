<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResourceEvent extends Model
{
    protected $fillable = [
        'resource_id',
        'type',
    ];

    /**
     * @return BelongsTo<Resource, ResourceEvent>
     */
    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }

    public function scopeViews(Builder $query): Builder
    {
        return $query->where('type', 'view');
    }

    public function scopeDownloads(Builder $query): Builder
    {
        return $query->where('type', 'download');
    }

    public function scopeBetween(Builder $query, \DateTimeInterface $from, \DateTimeInterface $to): Builder
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }
}
