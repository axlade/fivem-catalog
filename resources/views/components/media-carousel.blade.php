@props(['slides'])

@if (empty($slides))
    <div class="aspect-video rounded-xl overflow-hidden border border-zinc-900 bg-zinc-900 flex flex-col items-center justify-center gap-2 text-zinc-600">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3 16.5V6a1.5 1.5 0 011.5-1.5h15A1.5 1.5 0 0121 6v12a1.5 1.5 0 01-1.5 1.5h-15A1.5 1.5 0 013 18v-1.5zm10.5-8.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
        </svg>
        <span class="text-sm">No media available</span>
    </div>
@else
    <div x-data="{ active: 0, total: {{ count($slides) }} }" class="rounded-xl overflow-hidden border border-zinc-900 bg-zinc-900">
    <div class="relative aspect-video bg-black">
        @foreach ($slides as $i => $slide)
            <div x-show="active === {{ $i }}" @if ($i > 0) x-cloak @endif class="absolute inset-0">
                @if ($slide['type'] === 'image')
                    @if ($i === 0)
                        <img src="{{ $slide['src'] }}" alt="" class="h-full w-full object-cover" loading="eager" fetchpriority="high">
                    @else
                        <img src="{{ $slide['src'] }}" alt="" class="h-full w-full object-cover" loading="lazy" decoding="async">
                    @endif
                @elseif ($slide['type'] === 'video')
                    <iframe src="{{ $slide['src'] }}" class="h-full w-full" loading="lazy" allowfullscreen
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                @else
                    <a href="{{ $slide['src'] }}" target="_blank" rel="noopener nofollow ugc"
                        class="flex items-center justify-center h-full w-full text-sm text-zinc-400 hover:text-brand-400 transition">
                        Watch video externally &#8599;
                    </a>
                @endif
            </div>
        @endforeach

        @if (count($slides) > 1)
            <button type="button" @click="active = (active - 1 + total) % total" aria-label="Previous"
                class="absolute left-3 top-1/2 -translate-y-1/2 flex items-center justify-center h-9 w-9 rounded-full bg-zinc-950/70 text-zinc-100 hover:bg-zinc-950 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button type="button" @click="active = (active + 1) % total" aria-label="Next"
                class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center justify-center h-9 w-9 rounded-full bg-zinc-950/70 text-zinc-100 hover:bg-zinc-950 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex items-center gap-1.5">
                @foreach ($slides as $i => $slide)
                    <button type="button" @click="active = {{ $i }}" aria-label="Go to slide {{ $i + 1 }}"
                        class="h-1.5 rounded-full transition-all"
                        :class="active === {{ $i }} ? 'w-6 bg-brand-500' : 'w-1.5 bg-zinc-600 hover:bg-zinc-400'"></button>
                @endforeach
            </div>
        @endif
    </div>

    @if (count($slides) > 1)
        <div class="flex gap-2 p-3 overflow-x-auto">
            @foreach ($slides as $i => $slide)
                <button type="button" @click="active = {{ $i }}"
                    class="shrink-0 h-14 w-20 rounded-lg overflow-hidden border-2 transition"
                    :class="active === {{ $i }} ? 'border-brand-500' : 'border-transparent opacity-60 hover:opacity-100'">
                    @if ($slide['type'] === 'image')
                        <img src="{{ $slide['src'] }}" alt="" class="h-full w-full object-cover" loading="lazy" decoding="async">
                    @else
                        <div class="h-full w-full bg-zinc-800 flex items-center justify-center text-zinc-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.5 5.653c0-1.426 1.529-2.33 2.779-1.643l11.54 6.348c1.295.712 1.295 2.573 0 3.285L7.28 19.991c-1.25.687-2.779-.217-2.779-1.643V5.653z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    @endif
                </button>
            @endforeach
        </div>
    @endif
    </div>
@endif
