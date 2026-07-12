<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResourceRatingRequest;
use App\Models\Resource;
use App\Models\ResourceRating;
use Illuminate\Http\RedirectResponse;

class ResourceRatingController extends Controller
{
    /**
     * Post a rating, or replace the authenticated user's existing rating for
     * this resource if they've already left one.
     */
    public function store(StoreResourceRatingRequest $request, Resource $resource): RedirectResponse
    {
        $resource->ratings()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $request->validated()
        );

        return back()->with('status', 'Your rating has been posted.');
    }

    /**
     * Remove a rating. Owners can remove their own; admins can moderate any.
     */
    public function destroy(Resource $resource, ResourceRating $rating): RedirectResponse
    {
        $this->authorize('delete', $rating);

        $rating->delete();

        return back()->with('status', 'Rating removed.');
    }
}
