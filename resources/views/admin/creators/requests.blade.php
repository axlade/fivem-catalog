<x-creator-layout title="Creator Requests">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-zinc-100">Creator Status Requests</h2>
            <p class="text-sm text-zinc-500">Members who qualified (3+ approved resources) and applied for creator status.</p>
        </div>
        <span class="rounded-full bg-brand-500/10 px-3 py-1 text-sm font-semibold text-brand-400">
            {{ $requests->count() }} pending
        </span>
    </div>

    <div class="space-y-4">
        @forelse ($requests as $creatorRequest)
            <div class="flex items-center gap-4 rounded-xl border border-zinc-900 bg-zinc-900/40 p-4">
                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-zinc-100 truncate">{{ $creatorRequest->user->name }}</p>
                    <p class="text-xs text-zinc-500">
                        {{ $creatorRequest->user->email }} &middot;
                        {{ $creatorRequest->user->approvedResourcesCount() }} approved resources &middot;
                        applied {{ $creatorRequest->created_at->diffForHumans() }}
                    </p>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <form action="{{ route('admin.creator-requests.update', $creatorRequest) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500 transition">
                            Approve
                        </button>
                    </form>
                    <form action="{{ route('admin.creator-requests.update', $creatorRequest) }}" method="POST">
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
                No pending creator requests.
            </div>
        @endforelse
    </div>
</x-creator-layout>
