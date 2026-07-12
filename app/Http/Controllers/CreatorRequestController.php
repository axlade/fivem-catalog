<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CreatorRequestController extends Controller
{
    /**
     * Let an eligible member apply for creator status.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_if($user->isCreator(), 403);
        abort_if($user->hasRequestedCreatorStatus(), 403);
        abort_unless($user->meetsCreatorRequestThreshold(), 403);

        $user->creatorRequests()->create(['status' => 'pending']);
        $user->forceFill(['certification_requested_at' => now()])->save();

        return back()->with('status', 'Your creator status request has been submitted for review.');
    }
}
