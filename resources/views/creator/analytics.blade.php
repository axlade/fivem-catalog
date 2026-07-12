<x-creator-layout title="Analytics">
    @php
        $formatDuration = function (?int $seconds): string {
            if ($seconds === null) {
                return '—';
            }
            if ($seconds < 60) {
                return $seconds.'s';
            }

            return intdiv($seconds, 60).'m '.($seconds % 60).'s';
        };
    @endphp

    {{-- Totals --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        <div class="rounded-xl border border-zinc-900 bg-zinc-900/40 p-5">
            <p class="text-sm text-zinc-500">Total Views</p>
            <p class="mt-2 text-3xl font-bold text-zinc-100">{{ number_format($totals['views']) }}</p>
        </div>
        <div class="rounded-xl border border-zinc-900 bg-zinc-900/40 p-5">
            <p class="text-sm text-zinc-500">Total Downloads</p>
            <p class="mt-2 text-3xl font-bold text-emerald-400">{{ number_format($totals['downloads']) }}</p>
            <p class="mt-1 text-xs text-zinc-600">Free resources only</p>
        </div>
        <div class="rounded-xl border border-zinc-900 bg-zinc-900/40 p-5">
            <p class="text-sm text-zinc-500">Avg. Time on Page</p>
            <p class="mt-2 text-3xl font-bold text-brand-400">{{ $formatDuration($totals['avgDurationSeconds']) }}</p>
        </div>
    </div>

    {{-- Per-resource breakdown --}}
    <h2 class="text-lg font-semibold text-zinc-100 mb-4">By Resource</h2>

    <div class="rounded-xl border border-zinc-900 bg-zinc-900/40 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-zinc-900 text-left text-xs uppercase tracking-wider text-zinc-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Title</th>
                    <th class="px-4 py-3 font-medium">Views</th>
                    <th class="px-4 py-3 font-medium">Downloads</th>
                    <th class="px-4 py-3 font-medium">Avg. Time on Page</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800">
                @forelse ($resources as $resource)
                    <tr>
                        <td class="px-4 py-3 font-medium text-zinc-100">
                            <a href="{{ route('resources.show', $resource) }}" class="hover:text-brand-400 transition">{{ $resource->title }}</a>
                        </td>
                        <td class="px-4 py-3 text-zinc-400">{{ number_format($resource->views_count) }}</td>
                        <td class="px-4 py-3 text-zinc-400">{{ $resource->isFree() ? number_format($resource->downloads_count) : '—' }}</td>
                        <td class="px-4 py-3 text-zinc-400">{{ $formatDuration($resource->averageViewDurationSeconds()) }}</td>
                        <td class="px-4 py-3">
                            @if ($resource->isApproved())
                                <span class="rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-400">Approved</span>
                            @elseif ($resource->isRejected())
                                <span class="rounded-full bg-red-500/10 px-2.5 py-1 text-xs font-semibold text-red-400">Rejected</span>
                            @else
                                <span class="rounded-full bg-brand-500/10 px-2.5 py-1 text-xs font-semibold text-brand-400">Pending</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-16 text-center text-zinc-500">
                            You haven't posted any scripts yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-creator-layout>
