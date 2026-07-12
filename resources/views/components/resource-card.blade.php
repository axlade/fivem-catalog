@props(['resource'])

<article {{ $attributes->merge(['class' => 'flex flex-col rounded-xl border border-zinc-900 bg-zinc-900/40 overflow-hidden hover:border-brand-700/60 transition']) }}>
    <a href="{{ route('resources.show', $resource) }}" class="aspect-video bg-zinc-900 block">
        <x-thumbnail :path="$resource->thumbnail_path" :alt="$resource->title" class="h-full w-full object-cover" />
    </a>

    <div class="flex-1 flex flex-col p-4">
        <div class="flex items-center gap-2 mb-2">
            <span class="rounded-full bg-zinc-800 px-2 py-0.5 text-xs font-medium text-zinc-300 uppercase">{{ $resource->framework }}</span>
            <span class="rounded-full bg-zinc-800 px-2 py-0.5 text-xs font-medium text-zinc-400 capitalize">{{ $resource->category }}</span>
        </div>

        <h3 class="font-semibold text-zinc-100 truncate">
            <a href="{{ route('resources.show', $resource) }}" class="hover:text-brand-400 transition">{{ $resource->title }}</a>
        </h3>
        <a href="{{ route('creators.show', $resource->user) }}" class="text-xs text-zinc-500 hover:text-brand-400 transition mb-2">
            by {{ $resource->user->name }}
        </a>
        <p class="text-sm text-zinc-400 line-clamp-2 flex-1">{{ \Illuminate\Support\Str::limit(strip_tags($resource->description), 100) }}</p>

        <div class="flex items-center gap-3 mt-3 text-xs text-zinc-500">
            @if ($resource->isFree())
                <span class="inline-flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    {{ number_format($resource->downloads_count) }}
                </span>
            @endif
            <span class="inline-flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                {{ number_format($resource->views_count) }}
            </span>
        </div>

        <div class="mt-4">
            @if ($resource->isFree())
                <a href="{{ route('resources.download', $resource) }}" target="_blank" rel="noopener"
                    class="block w-full text-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500 transition">
                    {{ $resource->hasDownloadFile() ? 'Download' : 'Download (GitHub)' }}
                </a>
            @else
                <div class="flex items-center justify-between gap-3">
                    <span class="text-lg font-bold text-zinc-100">${{ number_format($resource->price, 2) }}</span>
                    <a href="{{ route('resources.download', $resource) }}" target="_blank" rel="noopener"
                        class="flex-1 text-center rounded-lg bg-brand-500 px-4 py-2 text-sm font-semibold text-zinc-950 hover:bg-brand-400 transition">
                        Purchase on Tebex
                    </a>
                </div>
            @endif
        </div>
    </div>
</article>
