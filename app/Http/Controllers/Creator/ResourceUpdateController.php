<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResourceUpdateRequest;
use App\Models\Resource;
use App\Models\ResourceUpdate;
use Illuminate\Http\RedirectResponse;

class ResourceUpdateController extends Controller
{
    /**
     * Publish a changelog entry for one of the owner's own resources.
     */
    public function store(StoreResourceUpdateRequest $request, Resource $resource): RedirectResponse
    {
        $resource->updates()->create($request->validated());

        return redirect()
            ->route('creator.resources.edit', $resource)
            ->with('status', 'Update published.');
    }

    /**
     * Remove a previously published changelog entry.
     */
    public function destroy(Resource $resource, ResourceUpdate $update): RedirectResponse
    {
        $this->authorize('update', $resource);
        abort_unless($update->resource_id === $resource->id, 404);

        $update->delete();

        return redirect()
            ->route('creator.resources.edit', $resource)
            ->with('status', 'Update removed.');
    }
}
