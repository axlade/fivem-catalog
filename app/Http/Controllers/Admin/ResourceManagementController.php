<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResourceManagementController extends Controller
{
    /**
     * List every resource on the platform (any status), searchable and
     * filterable — the moderation queue only ever shows pending ones.
     */
    public function index(Request $request): View
    {
        $resources = Resource::query()
            ->with('user')
            ->when($request->string('status')->toString(), fn ($query, $status) => $query->where('status', $status))
            ->when($request->string('category')->toString(), fn ($query, $category) => $query->where('category', $category))
            ->search($request->string('q')->toString() ?: null)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.resources.index', [
            'resources' => $resources,
            'categories' => ['scripts' => 'Scripts', 'mlos' => 'MLOs', 'eup' => 'EUP', 'vehicles' => 'Vehicles'],
        ]);
    }
}
