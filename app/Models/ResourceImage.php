<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResourceImage extends Model
{
    protected $fillable = [
        'resource_id',
        'path',
        'position',
    ];

    /**
     * @return BelongsTo<Resource, ResourceImage>
     */
    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }
}
