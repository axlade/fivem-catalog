<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResourceComment extends Model
{
    protected $fillable = [
        'resource_id',
        'user_id',
        'body',
    ];

    /**
     * @return BelongsTo<Resource, ResourceComment>
     */
    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }

    /**
     * @return BelongsTo<User, ResourceComment>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
