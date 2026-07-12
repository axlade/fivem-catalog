<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequest;
use App\Models\Service;
use App\Services\ContentPolicyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function __construct(protected ContentPolicyService $contentPolicyService)
    {
        //
    }

    /**
     * Public directory of active freelance services.
     */
    public function index(Request $request): View
    {
        $services = Service::query()
            ->active()
            ->whereHas('user', fn ($q) => $q->contentVisible())
            ->with(['user', 'reviews'])
            ->ofCategory($request->string('category')->toString() ?: null)
            ->search($request->string('q')->toString() ?: null)
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('services.index', [
            'services' => $services,
            'categories' => $this->categories(),
        ]);
    }

    /**
     * Show the full detail page for a single active service: description,
     * developer info, and reviews.
     */
    public function show(Service $service): View
    {
        abort_unless($service->is_active, 404);
        abort_if($service->user->hidesContent(), 404);

        $service->load(['user', 'reviews.reviewer']);

        return view('services.show', [
            'service' => $service,
        ]);
    }

    /**
     * The authenticated creator's own posted services.
     */
    public function myServices(Request $request): View
    {
        return view('creator.services.index', [
            'services' => $request->user()->services()->latest()->get(),
        ]);
    }

    /**
     * Show the "Post a Service" form.
     */
    public function create(): View
    {
        return view('services.create', [
            'categories' => $this->categories(),
        ]);
    }

    /**
     * Persist a newly posted service.
     */
    public function store(StoreServiceRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $this->contentPolicyService->assertNoCheatContent($data['title'], $data['description']);

        $request->user()->services()->create($data + ['is_active' => true]);

        return redirect()
            ->route('creator.services.index')
            ->with('status', 'Your service has been published.');
    }

    /**
     * Redirect an interested visitor to the developer's Discord to inquire
     * about a service. There is no payment gateway or in-app messaging in
     * this first version — Discord is the point of contact.
     */
    public function contact(Service $service): RedirectResponse
    {
        abort_if($service->user->hidesContent(), 404);

        $discordUrl = $service->user->discord_invite_url;

        if (! $discordUrl) {
            return back()->with('error', 'This developer has not added a Discord link yet. Please try again later.');
        }

        return redirect()->away($discordUrl);
    }

    /**
     * @return array<string, string>
     */
    protected function categories(): array
    {
        return [
            'scripting' => 'Scripting',
            'mapping' => 'Mapping',
            'web' => 'Web',
            'optimization' => 'Optimization',
        ];
    }
}
