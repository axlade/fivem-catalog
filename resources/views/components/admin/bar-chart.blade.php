@props(['title', 'series', 'color' => 'bg-brand-500'])

@php
    $max = max(1, collect($series)->max('count'));
    $total = collect($series)->sum('count');
    $labelEvery = max(1, (int) ceil(count($series) / 10));
@endphp

<div class="rounded-xl border border-zinc-900 bg-zinc-900/40 p-6">
    <div class="flex items-center justify-between mb-5">
        <h3 class="text-sm font-semibold text-zinc-100">{{ $title }}</h3>
        <span class="text-xs text-zinc-500">{{ number_format($total) }} total</span>
    </div>

    <div class="flex items-end gap-px h-40" x-data="{ hovered: null }">
        @foreach ($series as $i => $point)
            @php($heightPct = $point['count'] > 0 ? max(2, round(($point['count'] / $max) * 100)) : 1)
            <div class="relative flex-1 h-full flex items-end group" @mouseenter="hovered = {{ $i }}" @mouseleave="hovered = null">
                <div x-show="hovered === {{ $i }}" x-cloak
                    class="absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md bg-zinc-800 border border-zinc-700 px-2 py-1 text-[11px] text-zinc-100 shadow-lg z-10">
                    {{ $point['tooltip'] ?? $point['label'] }}: {{ number_format($point['count']) }}
                </div>
                <div class="w-full rounded-t {{ $color }} group-hover:opacity-80 transition-opacity" style="height: {{ $heightPct }}%"></div>
            </div>
        @endforeach
    </div>

    <div class="flex mt-2 border-t border-zinc-900 pt-1.5">
        @foreach ($series as $i => $point)
            <div class="flex-1 text-center text-[10px] text-zinc-600">{{ $i % $labelEvery === 0 ? $point['label'] : '' }}</div>
        @endforeach
    </div>

    <details class="mt-3">
        <summary class="text-xs text-zinc-500 cursor-pointer hover:text-zinc-300 select-none">View as table</summary>
        <div class="mt-2 max-h-48 overflow-y-auto rounded-lg border border-zinc-800">
            <table class="w-full text-xs">
                <thead class="bg-zinc-900 text-zinc-500 text-left sticky top-0">
                    <tr>
                        <th class="px-3 py-1.5 font-medium">Period</th>
                        <th class="px-3 py-1.5 font-medium text-right">Count</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    @foreach ($series as $point)
                        <tr>
                            <td class="px-3 py-1 text-zinc-400">{{ $point['tooltip'] ?? $point['label'] }}</td>
                            <td class="px-3 py-1 text-right text-zinc-200">{{ number_format($point['count']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </details>
</div>
