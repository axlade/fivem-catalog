<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreatorRequest;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminModerationController extends Controller
{
    /**
     * List all resources pending review.
     */
    public function index(): View
    {
        return view('admin.moderation.index', [
            'resources' => Resource::pending()->with('user')->oldest()->get(),
        ]);
    }

    /**
     * Approve or reject a pending resource.
     */
    public function updateStatus(Request $request, Resource $resource): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected'])],
        ]);

        $resource->update(['status' => $validated['status']]);

        return redirect()
            ->route('admin.moderation.index')
            ->with('status', "Resource \"{$resource->title}\" was {$validated['status']}.");
    }

    /**
     * Instantly mark a creator's account as verified.
     */
    public function verifyCreator(User $user): RedirectResponse
    {
        $user->update(['is_verified' => true]);

        return redirect()
            ->route('admin.moderation.index')
            ->with('status', "{$user->name} is now a verified creator.");
    }

    /**
     * List all pending creator status requests.
     */
    public function creatorRequests(): View
    {
        return view('admin.creators.requests', [
            'requests' => CreatorRequest::query()->pending()->with('user')->oldest()->get(),
        ]);
    }

    /**
     * Approve or reject a creator status request. Approving promotes the
     * requesting user to the 'creator' role.
     */
    public function updateCreatorRequestStatus(Request $request, CreatorRequest $creatorRequest): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected'])],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $creatorRequest->update($validated);

        if ($validated['status'] === 'approved') {
            $creatorRequest->user->update(['role' => 'creator']);
        }

        return redirect()
            ->route('admin.creator-requests.index')
            ->with('status', "Request from {$creatorRequest->user->name} was {$validated['status']}.");
    }
}
