<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResourceCommentRequest;
use App\Models\Resource;
use App\Models\ResourceComment;
use Illuminate\Http\RedirectResponse;

class ResourceCommentController extends Controller
{
    /**
     * Post a new comment. Unlike ratings, a user can post multiple comments.
     */
    public function store(StoreResourceCommentRequest $request, Resource $resource): RedirectResponse
    {
        $resource->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $request->validated('body'),
        ]);

        return back()->with('status', 'Your comment has been posted.');
    }

    /**
     * Remove a comment. Owners can remove their own; admins can moderate any.
     */
    public function destroy(Resource $resource, ResourceComment $comment): RedirectResponse
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return back()->with('status', 'Comment removed.');
    }
}
