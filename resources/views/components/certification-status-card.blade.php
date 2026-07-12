@props(['user'])

@php
    $approvedCount = $user->approvedResourcesCount();
    $threshold = 3;
    $remaining = max(0, $threshold - $approvedCount);
@endphp

<div class="rounded-xl border border-zinc-900 bg-zinc-900/40 p-6">
    <h3 class="text-sm font-semibold text-zinc-100 mb-2">Certification Status</h3>

    @if ($user->hasRequestedCreatorStatus())
        <p class="text-sm text-zinc-400">Your request is under review.</p>
    @elseif ($remaining > 0)
        <p class="text-sm text-zinc-400">
            You need <span class="font-semibold text-zinc-100">{{ $remaining }}</span> more approved
            {{ Str::plural('resource', $remaining) }} to apply for Creator status.
        </p>
        <div class="mt-3 h-2 rounded-full bg-zinc-800 overflow-hidden">
            <div class="h-full bg-brand-500" style="width: {{ min(100, ($approvedCount / $threshold) * 100) }}%"></div>
        </div>
        <p class="mt-1 text-xs text-zinc-500">{{ $approvedCount }} / {{ $threshold }} approved resources</p>
    @else
        <p class="text-sm text-zinc-400 mb-4">You've met the requirements to become a verified Creator.</p>
        <form method="POST" action="{{ route('creator-request.store') }}">
            @csrf
            <button type="submit" class="w-full rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-semibold text-zinc-950 hover:bg-brand-400 transition">
                Request Creator Status
            </button>
        </form>
    @endif
</div>
