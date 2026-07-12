<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResourceRequest;
use App\Http\Requests\UpdateResourceRequest;
use App\Models\Resource;
use App\Models\User;
use App\Notifications\ResourcePendingReview;
use App\Services\ContentPolicyService;
use App\Services\ResourceValidationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CreatorDashboardController extends Controller
{
    public function __construct(
        protected ResourceValidationService $resourceValidationService,
        protected ContentPolicyService $contentPolicyService,
    ) {
        //
    }

    /**
     * Show the dashboard: the creator overview for creators/admins, or a
     * simple landing page (with their own submissions and certification
     * status) for plain members who aren't creators yet.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $resources = $user->resources()->latest()->get();

        if (! $user->isCreator()) {
            return view('dashboard', [
                'resources' => $resources,
            ]);
        }

        return view('creator.dashboard', [
            'resources' => $resources,
            'stats' => [
                'total' => $resources->count(),
                'approved' => $resources->where('status', 'approved')->count(),
                'pending' => $resources->where('status', 'pending')->count(),
            ],
        ]);
    }

    /**
     * Show the "Submit a Resource" form.
     */
    public function create(): View
    {
        $this->authorize('create', Resource::class);

        return view('creator.resources.create', [
            'categories' => ['scripts' => 'Scripts', 'mlos' => 'MLOs', 'eup' => 'EUP', 'vehicles' => 'Vehicles'],
            'frameworks' => ['esx' => 'ESX', 'qb-core' => 'QB-Core', 'standalone' => 'Standalone', 'ox' => 'OX'],
        ]);
    }

    /**
     * Persist a newly submitted resource. Creators (and admins) are trusted
     * and go live immediately; plain members go to the moderation queue and
     * admins are notified.
     */
    public function store(StoreResourceRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();
        $isFileMethod = ($data['download_method'] ?? 'link') === 'file' && $request->hasFile('download_file');

        $this->contentPolicyService->assertNoCheatContent($data['title'], $data['description']);

        if ($isFileMethod) {
            $this->resourceValidationService->validateResourcePackage($request->file('download_file'));
        }

        $data['thumbnail_path'] = $request->file('thumbnail')->store('thumbnails', 'public');
        $data['status'] = $user->isCreator() ? 'approved' : 'pending';

        if ($isFileMethod) {
            $data['download_file_path'] = $request->file('download_file')->store('downloads', 'public');
            $data['download_url'] = null;
        }

        $images = $data['images'] ?? [];
        unset($data['thumbnail'], $data['download_file'], $data['download_method'], $data['images']);

        $resource = $user->resources()->create($data);

        foreach ($images as $position => $image) {
            $resource->images()->create([
                'path' => $image->store('resources/gallery', 'public'),
                'position' => $position,
            ]);
        }

        if ($resource->isPending()) {
            Log::info("New resource pending approval: {$resource->id} ({$resource->title})");
            Notification::send(User::where('role', 'admin')->get(), new ResourcePendingReview($resource));
        }

        return redirect()
            ->route('dashboard')
            ->with('status', $resource->isApproved()
                ? 'Your resource has been published.'
                : 'Your resource was submitted and is pending review.');
    }

    /**
     * Show the edit form for one of the creator's own resources.
     */
    public function edit(Resource $resource): View
    {
        $this->authorize('update', $resource);

        $resource->load(['updates', 'images']);

        return view('creator.resources.edit', [
            'resource' => $resource,
            'categories' => ['scripts' => 'Scripts', 'mlos' => 'MLOs', 'eup' => 'EUP', 'vehicles' => 'Vehicles'],
            'frameworks' => ['esx' => 'ESX', 'qb-core' => 'QB-Core', 'standalone' => 'Standalone', 'ox' => 'OX'],
        ]);
    }

    /**
     * Update one of the creator's own resources. Trusted roles keep their
     * approved status; plain members are sent back for re-review.
     */
    public function update(UpdateResourceRequest $request, Resource $resource): RedirectResponse
    {
        $data = $request->validated();
        $isFileMethod = ($data['download_method'] ?? 'link') === 'file';

        $this->contentPolicyService->assertNoCheatContent($data['title'], $data['description']);

        if ($isFileMethod && $request->hasFile('download_file')) {
            $this->resourceValidationService->validateResourcePackage($request->file('download_file'));
        }

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail_path'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        if ($isFileMethod) {
            if ($request->hasFile('download_file')) {
                if ($resource->download_file_path) {
                    Storage::disk('public')->delete($resource->download_file_path);
                }

                $data['download_file_path'] = $request->file('download_file')->store('downloads', 'public');
            }

            $data['download_url'] = null;
        } else {
            if ($resource->download_file_path) {
                Storage::disk('public')->delete($resource->download_file_path);
            }

            $data['download_file_path'] = null;
        }

        $data['status'] = $request->user()->isCreator() ? 'approved' : 'pending';
        $newImages = $data['images'] ?? [];
        $removeImageIds = $data['remove_images'] ?? [];
        unset($data['thumbnail'], $data['download_file'], $data['download_method'], $data['images'], $data['remove_images']);

        $resource->update($data);

        if (! empty($removeImageIds)) {
            $resource->images()
                ->whereIn('id', $removeImageIds)
                ->get()
                ->each(function ($image) {
                    Storage::disk('public')->delete($image->path);
                    $image->delete();
                });
        }

        if (! empty($newImages)) {
            $nextPosition = (int) $resource->images()->max('position') + 1;

            foreach ($newImages as $offset => $image) {
                $resource->images()->create([
                    'path' => $image->store('resources/gallery', 'public'),
                    'position' => $nextPosition + $offset,
                ]);
            }
        }

        if ($resource->isPending()) {
            Log::info("Resource resubmitted for review: {$resource->id} ({$resource->title})");
            Notification::send(User::where('role', 'admin')->get(), new ResourcePendingReview($resource));
        }

        return redirect()
            ->route('dashboard')
            ->with('status', $resource->isApproved()
                ? 'Resource updated and published.'
                : 'Resource updated and resubmitted for review.');
    }

    /**
     * Delete one of the creator's own resources.
     */
    public function destroy(Resource $resource): RedirectResponse
    {
        $this->authorize('delete', $resource);

        $resource->delete();

        return redirect()
            ->route('dashboard')
            ->with('status', 'Resource deleted.');
    }

    /**
     * Show the creator's analytics: views, downloads, and average time
     * visitors spend on each of their resource pages.
     */
    public function analytics(Request $request): View
    {
        $resources = $request->user()->resources()->latest()->get();

        $totalDurationSeconds = $resources->sum('total_view_duration_seconds');
        $totalDurationSamples = $resources->sum('view_duration_samples');

        return view('creator.analytics', [
            'resources' => $resources,
            'totals' => [
                'views' => $resources->sum('views_count'),
                'downloads' => $resources->where('price', 0)->sum('downloads_count'),
                'avgDurationSeconds' => $totalDurationSamples > 0
                    ? intdiv($totalDurationSeconds, $totalDurationSamples)
                    : null,
            ],
        ]);
    }
}
