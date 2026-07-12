<x-creator-layout title="Dashboard">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2 rounded-xl border border-zinc-900 bg-zinc-900/40 p-6">
            <h1 class="text-xl font-bold text-zinc-100">Welcome back, {{ auth()->user()->name }}!</h1>
            <p class="mt-2 text-zinc-400">
                Browse the catalog, or submit your own scripts, MLOs, EUP, and vehicles for the community.
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('home') }}"
                    class="rounded-lg border border-zinc-700 px-5 py-2.5 text-sm font-semibold text-zinc-200 hover:bg-zinc-800 transition">
                    Browse Catalog
                </a>
                <a href="{{ route('creator.resources.create') }}"
                    class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-semibold text-zinc-950 hover:bg-brand-400 transition">
                    Submit a Resource
                </a>
            </div>
        </div>

        <x-certification-status-card :user="auth()->user()" />
    </div>

    <div id="my-resources" class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-zinc-100">My Resources</h2>
    </div>

    <div class="rounded-xl border border-zinc-900 bg-zinc-900/40 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-zinc-900 text-left text-xs uppercase tracking-wider text-zinc-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Thumbnail</th>
                    <th class="px-4 py-3 font-medium">Title</th>
                    <th class="px-4 py-3 font-medium">Category</th>
                    <th class="px-4 py-3 font-medium">Price</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800">
                @forelse ($resources as $resource)
                    <tr>
                        <td class="px-4 py-3">
                            <x-thumbnail :path="$resource->thumbnail_path" :alt="$resource->title" class="h-10 w-10 rounded-lg object-cover" />
                        </td>
                        <td class="px-4 py-3 font-medium text-zinc-100">{{ $resource->title }}</td>
                        <td class="px-4 py-3 text-zinc-400 capitalize">{{ $resource->category }}</td>
                        <td class="px-4 py-3">
                            @if ($resource->isFree())
                                <span class="font-semibold text-emerald-400">FREE</span>
                            @else
                                <span class="font-semibold text-zinc-100">${{ number_format($resource->price, 2) }}</span>
                            @endif
                        </td>
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
                        <td colspan="6" class="px-4 py-16 text-center text-zinc-500">
                            You haven't submitted any resources yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-creator-layout>
