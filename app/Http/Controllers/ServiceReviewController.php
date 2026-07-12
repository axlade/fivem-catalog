<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceReviewRequest;
use App\Models\Service;
use App\Models\ServiceReview;
use Illuminate\Http\RedirectResponse;

class ServiceReviewController extends Controller
{
    /**
     * Post a review, or replace the authenticated user's existing review for
     * this service if they've already left one.
     */
    public function store(StoreServiceReviewRequest $request, Service $service): RedirectResponse
    {
        $service->reviews()->updateOrCreate(
            ['reviewer_id' => $request->user()->id],
            $request->validated()
        );

        return back()->with('status', 'Your review has been posted.');
    }

    /**
     * Remove a review. Owners can remove their own; admins can moderate any.
     */
    public function destroy(Service $service, ServiceReview $review): RedirectResponse
    {
        $this->authorize('delete', $review);

        $review->delete();

        return back()->with('status', 'Review removed.');
    }
}
