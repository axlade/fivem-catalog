<x-creator-layout title="Moderation Queue">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-zinc-100">Pending Resources</h2>
            <p class="text-sm text-zinc-500">Review submissions before they appear in the public catalog.</p>
        </div>
        <span class="rounded-full bg-brand-500/10 px-3 py-1 text-sm font-semibold text-brand-400">
            {{ $resources->count() }} pending
        </span>
    </div>

    <div class="space-y-4">
        @forelse ($resources as $resource)
            <div class="flex items-center gap-4 rounded-xl border border-zinc-900 bg-zinc-900/40 p-4">
                <x-thumbnail :path="$resource->thumbnail_path" :alt="$resource->title" class="h-16 w-16 rounded-lg object-cover shrink-0" />

                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-zinc-100 truncate">{{ $resource->title }}</p>
                    <p class="text-xs text-zinc-500">
                        by {{ $resource->user->name }} &middot; {{ ucfirst($resource->category) }} &middot; {{ strtoupper($resource->framework) }}
                        &middot; {{ $resource->isFree() ? 'FREE' : '$'.number_format($resource->price, 2) }}
                    </p>
                    <p class="mt-1 text-sm text-zinc-400 line-clamp-1">{{ \Illuminate\Support\Str::limit(strip_tags($resource->description), 140) }}</p>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    @unless ($resource->user->is_verified)
                        <form action="{{ route('admin.creators.verify', $resource->user) }}" method="POST">
                            @csrf
                            <button type="submit" class="rounded-lg border border-brand-800 bg-brand-500/10 px-4 py-2 text-sm font-semibold text-brand-400 hover:bg-brand-500/20 transition">
                                Verify Creator
                            </button>
                        </form>
                    @endunless

                    <form action="{{ route('admin.moderation.update', $resource) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500 transition">
                            Approve
                        </button>
                    </form>
                    <form action="{{ route('admin.moderation.update', $resource) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500 transition">
                            Reject
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-zinc-900 py-16 text-center text-zinc-500">
                No resources awaiting review.
            </div>
        @endforelse
    </div>
</x-creator-layout>
