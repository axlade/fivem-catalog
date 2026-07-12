@props(['path', 'alt' => ''])

@if ($path && \Illuminate\Support\Facades\Storage::disk('public')->exists($path))
    <img src="{{ \Illuminate\Support\Facades\Storage::url($path) }}" alt="{{ $alt }}" {{ $attributes->merge(['loading' => 'lazy', 'decoding' => 'async']) }}>
@else
    <div {{ $attributes->merge(['class' => 'flex items-center justify-center bg-zinc-800 text-zinc-600']) }}>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-1/3 w-1/3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3 16.5V6a1.5 1.5 0 011.5-1.5h15A1.5 1.5 0 0121 6v12a1.5 1.5 0 01-1.5 1.5h-15A1.5 1.5 0 013 18v-1.5zm10.5-8.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
        </svg>
    </div>
@endif
