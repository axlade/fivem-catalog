<x-creator-layout title="Statistics">
    <div class="flex items-center justify-between mb-6 flex-wrap gap-4">
        <div>
            <h2 class="text-lg font-semibold text-zinc-100">Platform Statistics</h2>
            <p class="text-sm text-zinc-500">Traffic and download activity across all resources.</p>
        </div>

        <form method="GET" action="{{ route('admin.statistics.index') }}" id="period-form" x-data @change="$el.submit()"
            class="flex rounded-lg border border-zinc-800 bg-zinc-950 p-1 text-sm">
            @foreach (['7d' => '7 days', '30d' => '30 days', '90d' => '90 days', 'all' => 'All time'] as $value => $label)
                <label class="cursor-pointer rounded-md px-3 py-1.5 transition {{ $period === $value ? 'bg-brand-500 text-zinc-950 font-semibold' : 'text-zinc-400 hover:text-zinc-100' }}">
                    <input type="radio" name="period" value="{{ $value }}" class="sr-only" @checked($period === $value)>
                    {{ $label }}
                </label>
            @endforeach
        </form>
    </div>

    {{-- Summary tiles --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="rounded-xl border border-zinc-900 bg-zinc-900/40 p-5">
            <p class="text-xs text-zinc-500 uppercase tracking-wide">Views</p>
            <p class="mt-1 text-2xl font-bold text-zinc-100">{{ number_format($totals['views']) }}</p>
        </div>
        <div class="rounded-xl border border-zinc-900 bg-zinc-900/40 p-5">
            <p class="text-xs text-zinc-500 uppercase tracking-wide">Downloads</p>
            <p class="mt-1 text-2xl font-bold text-zinc-100">{{ number_format($totals['downloads']) }}</p>
        </div>
        <div class="rounded-xl border border-zinc-900 bg-zinc-900/40 p-5">
            <p class="text-xs text-zinc-500 uppercase tracking-wide">New Resources</p>
            <p class="mt-1 text-2xl font-bold text-zinc-100">{{ number_format($totals['newResources']) }}</p>
        </div>
        <div class="rounded-xl border border-zinc-900 bg-zinc-900/40 p-5">
            <p class="text-xs text-zinc-500 uppercase tracking-wide">New Users</p>
            <p class="mt-1 text-2xl font-bold text-zinc-100">{{ number_format($totals['newUsers']) }}</p>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <x-admin.bar-chart title="Traffic (Views)" :series="$viewsSeries" color="bg-brand-500" />
        <x-admin.bar-chart title="Downloads" :series="$downloadsSeries" color="bg-emerald-500" />
    </div>

    {{-- Top lists --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-xl border border-zinc-900 bg-zinc-900/40 overflow-hidden">
            <div class="px-5 py-4 border-b border-zinc-900">
                <h3 class="text-sm font-semibold text-zinc-100">Most Downloaded</h3>
            </div>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-zinc-800">
                    @forelse ($topDownloaded as $row)
                        <tr>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <x-thumbnail :path="$row->resource->thumbnail_path" :alt="$row->resource->title" class="h-8 w-8 rounded-lg object-cover shrink-0" />
                                    <a href="{{ route('resources.show', $row->resource) }}" class="text-zinc-100 hover:text-brand-400 transition truncate">{{ $row->resource->title }}</a>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-right font-semibold text-zinc-100 shrink-0">{{ number_format($row->total) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="px-5 py-10 text-center text-zinc-500">No downloads in this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="rounded-xl border border-zinc-900 bg-zinc-900/40 overflow-hidden">
            <div class="px-5 py-4 border-b border-zinc-900">
                <h3 class="text-sm font-semibold text-zinc-100">Most Viewed</h3>
            </div>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-zinc-800">
                    @forelse ($topViewed as $row)
                        <tr>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <x-thumbnail :path="$row->resource->thumbnail_path" :alt="$row->resource->title" class="h-8 w-8 rounded-lg object-cover shrink-0" />
                                    <a href="{{ route('resources.show', $row->resource) }}" class="text-zinc-100 hover:text-brand-400 transition truncate">{{ $row->resource->title }}</a>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-right font-semibold text-zinc-100 shrink-0">{{ number_format($row->total) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="px-5 py-10 text-center text-zinc-500">No views in this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-creator-layout>
