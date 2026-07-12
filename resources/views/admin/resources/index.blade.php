<x-creator-layout title="Resource Management">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-zinc-100">Resource Management</h2>
            <p class="text-sm text-zinc-500">{{ $resources->total() }} resources across every status.</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.resources.index') }}" class="flex flex-wrap gap-3 mb-6">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search title or description..."
            class="flex-1 min-w-[220px] rounded-lg border-zinc-700 bg-zinc-950 text-sm text-zinc-100 focus:border-brand-500 focus:ring-brand-500">
        <select name="status" onchange="this.form.submit()"
            class="rounded-lg border-zinc-700 bg-zinc-950 text-sm text-zinc-100 focus:border-brand-500 focus:ring-brand-500">
            <option value="">All statuses</option>
            <option value="approved" @selected(request('status') === 'approved')>Approved</option>
            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
            <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
        </select>
        <select name="category" onchange="this.form.submit()"
            class="rounded-lg border-zinc-700 bg-zinc-950 text-sm text-zinc-100 focus:border-brand-500 focus:ring-brand-500">
            <option value="">All categories</option>
            @foreach ($categories as $value => $label)
                <option value="{{ $value }}" @selected(request('category') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-semibold text-zinc-950 hover:bg-brand-400 transition">
            Search
        </button>
    </form>

    <div class="rounded-xl border border-zinc-900 bg-zinc-900/40 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-zinc-900 text-left text-xs uppercase tracking-wider text-zinc-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Resource</th>
                    <th class="px-4 py-3 font-medium">Owner</th>
                    <th class="px-4 py-3 font-medium">Category</th>
                    <th class="px-4 py-3 font-medium">Views</th>
                    <th class="px-4 py-3 font-medium">Downloads</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800">
                @forelse ($resources as $resource)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <x-thumbnail :path="$resource->thumbnail_path" :alt="$resource->title" class="h-9 w-9 rounded-lg object-cover shrink-0" />
                                <span class="font-medium text-zinc-100">{{ $resource->title }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-zinc-400">{{ $resource->user->name }}</td>
                        <td class="px-4 py-3 text-zinc-400 capitalize">{{ $resource->category }}</td>
                        <td class="px-4 py-3 text-zinc-400">{{ number_format($resource->views_count) }}</td>
                        <td class="px-4 py-3 text-zinc-400">{{ $resource->isFree() ? number_format($resource->downloads_count) : '—' }}</td>
                        <td class="px-4 py-3">
                            @if ($resource->isApproved())
                                <span class="rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-400">Approved</span>
                            @elseif ($resource->isRejected())
                                <span class="rounded-full bg-red-500/10 px-2.5 py-1 text-xs font-semibold text-red-400">Rejected</span>
                            @else
                                <span class="rounded-full bg-brand-500/10 px-2.5 py-1 text-xs font-semibold text-brand-400">Pending</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="{{ route('resources.show', $resource) }}" class="text-zinc-400 hover:text-brand-400 transition">View</a>
                            <a href="{{ route('creator.resources.edit', $resource) }}" class="text-zinc-400 hover:text-brand-400 transition">Edit</a>
                            <form action="{{ route('creator.resources.destroy', $resource) }}" method="POST" class="inline"
                                onsubmit="return confirm('Delete this resource? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-zinc-400 hover:text-red-400 transition">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-16 text-center text-zinc-500">No resources match your filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $resources->links() }}
    </div>
</x-creator-layout>
