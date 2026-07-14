<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\ResourceEvent;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CatalogController extends Controller
{
    /**
     * Display the public catalog grid with sidebar filtering.
     */
    public function home(Request $request): View
    {
        $resources = Resource::query()
            ->approved()
            ->whereHas('user', fn ($q) => $q->contentVisible())
            ->with(['user', 'tags'])
            ->ofCategory($request->string('category')->toString() ?: null)
            ->ofFramework($request->string('framework')->toString() ?: null)
            ->priceFilter($request->string('price')->toString() ?: null)
            ->search($request->string('q')->toString() ?: null)
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('home', [
            'resources' => $resources,
            'categories' => ['scripts' => 'Scripts', 'mlos' => 'MLOs', 'eup' => 'EUP', 'vehicles' => 'Vehicles'],
            'frameworks' => ['esx' => 'ESX', 'qb-core' => 'QB-Core', 'standalone' => 'Standalone', 'ox' => 'OX'],
        ]);
    }

    /**
     * Display a creator's public storefront: bio, external links, and their approved resources.
     */
    public function creatorProfile(User $user): View
    {
        abort_if($user->hidesContent(), 404);

        return view('creator.profile', [
            'creator' => $user,
            'resources' => $user->resources()->approved()->latest()->paginate(12),
        ]);
    }

    /**
     * Display the full detail page for a single resource. Approved resources
     * are public; pending/rejected ones are only visible to their owner or an admin.
     */
    public function show(Request $request, Resource $resource): View
    {
        $viewer = $request->user();
        $canPreview = $viewer && ($viewer->id === $resource->user_id || $viewer->isAdmin());
        $isPubliclyVisible = $resource->isApproved() && ! $resource->user->hidesContent();

        abort_unless($isPubliclyVisible || $canPreview, 404);

        $isOwnResource = $viewer && $viewer->id === $resource->user_id;

        if ($resource->isApproved() && ! $isOwnResource) {
            $viewCooldownKey = 'resource-view:'.$resource->id.':'.($viewer ? "user:{$viewer->id}" : 'ip:'.$request->ip());

            if (! Cache::has($viewCooldownKey)) {
                Cache::put($viewCooldownKey, true, now()->addHours(4));
                $resource->increment('views_count');
                ResourceEvent::create(['resource_id' => $resource->id, 'type' => 'view']);
            }
        }

        $resource->load(['user', 'tags', 'images', 'updates', 'ratings.user', 'comments.user']);

        return view('resources.show', [
            'resource' => $resource,
        ]);
    }

    /**
     * Track a download/purchase click, then either stream the hosted ZIP
     * file directly or redirect to the external GitHub/Tebex destination.
     */
    public function download(Request $request, Resource $resource): RedirectResponse|StreamedResponse
    {
        $viewer = $request->user();
        $canPreview = $viewer && ($viewer->id === $resource->user_id || $viewer->isAdmin());
        $isPubliclyVisible = $resource->isApproved() && ! $resource->user->hidesContent();

        abort_unless($isPubliclyVisible || $canPreview, 404);

        // Only free resources are an actual download we can confirm. A click
        // through to an external Tebex store isn't a verified download/sale,
        // so paid resources aren't counted here.
        if ($resource->isApproved() && $resource->isFree()) {
            $resource->increment('downloads_count');
            ResourceEvent::create(['resource_id' => $resource->id, 'type' => 'download']);
        }

        if ($resource->isFree()) {
            if ($resource->hasDownloadFile()) {
                return Storage::disk('public')->download($resource->download_file_path, $resource->slug.'.zip');
            }

            return redirect()->away($resource->download_url);
        }

        return redirect()->away($resource->tebex_url);
    }

    /**
     * Record how long a visitor spent on a resource's page. Called via
     * navigator.sendBeacon() as the visitor leaves, so it must stay
     * lightweight and never fail loudly.
     */
    public function trackTime(Request $request, Resource $resource): Response
    {
        $validated = $request->validate([
            'duration' => ['required', 'integer', 'min:1', 'max:3600'],
        ]);

        if ($resource->isApproved()) {
            $resource->increment('total_view_duration_seconds', $validated['duration']);
            $resource->increment('view_duration_samples');
        }

        return response()->noContent();
    }
}

