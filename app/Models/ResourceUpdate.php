<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResourceUpdate extends Model
{
    protected $fillable = [
        'resource_id',
        'title',
        'body',
    ];

    /**
     * @return BelongsTo<Resource, ResourceUpdate>
     */
    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }
}
