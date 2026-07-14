@php
    $creatorImage = $creator->avatar_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($creator->avatar_path)
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($creator->avatar_path)
        : null;
    $creatorDescription = $creator->bio
        ? \Illuminate\Support\Str::limit(strip_tags($creator->bio), 200, '')
        : "{$creator->name}'s FiveM resources on FiveM-Catalog — {$resources->total()} published scripts, MLOs, EUP, and vehicles.";
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'ProfilePage',
        'mainEntity' => [
            '@type' => 'Person',
            'name' => $creator->name,
            'alternateName' => $creator->username,
            'url' => route('creators.show', $creator),
            'description' => $creatorDescription,
        ],
    ];

    if ($creatorImage) {
        $schema['mainEntity']['image'] = $creatorImage;
    }
@endphp

<x-app-layout
    :title="$creator->name"
    :description="$creatorDescription"
    :image="$creatorImage"
    :canonical="route('creators.show', $creator)"
    :schema="json_encode($schema)">
    <x-slot:hero>
        <div class="relative border-b border-zinc-900 py-12 overflow-hidden">
            @if ($creator->banner_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($creator->banner_path))
                <div class="absolute inset-0">
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($creator->banner_path) }}" alt="" class="h-full w-full object-cover">
                    <div class="absolute inset-0 bg-zinc-950/70"></div>
                </div>
            @else
                <div class="absolute inset-0 bg-zinc-900/30"></div>
            @endif

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                @if (auth()->user()?->isAdmin() && auth()->id() !== $creator->id)
                    <div class="flex justify-end mb-6">
                        <div class="inline-flex items-center gap-3 rounded-lg border border-zinc-800 bg-zinc-900/60 px-4 py-2">
                            <span class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Admin</span>
                            <x-admin.ban-control :user="$creator" />
                        </div>
                    </div>
                @endif

                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-8">
                    {{-- Left: identity --}}
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 text-center sm:text-left">
                        <div class="h-24 w-24 shrink-0 rounded-full bg-zinc-800 flex items-center justify-center text-2xl font-bold text-brand-400 overflow-hidden">
                            @if ($creator->avatar_path)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($creator->avatar_path) }}" alt="{{ $creator->name }}" class="h-full w-full object-cover">
                            @else
                                {{ strtoupper(substr($creator->name, 0, 2)) }}
                            @endif
                        </div>

                        <div class="min-w-0">
                            <div class="flex items-center justify-center sm:justify-start gap-2">
                                <h1 class="text-2xl font-bold text-zinc-100">{{ $creator->username }}</h1>
                                @if ($creator->is_verified)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-brand-500/10 px-2.5 py-0.5 text-xs font-semibold text-brand-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Verified Creator
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm text-zinc-500 mt-1">Joined {{ $creator->created_at->format('F Y') }} &middot; {{ $resources->total() }} resources published</p>

                            @if ($creator->bio)
                                <p class="mt-4 max-w-xl text-zinc-300">{{ $creator->bio }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Right: stacked action links --}}
                    @if ($creator->tebex_url || $creator->github_url || $creator->discord_invite_url || $creator->youtube_url || $creator->website_url)
                        <div class="flex flex-col gap-2 w-full lg:w-56 shrink-0">
                            @if ($creator->tebex_url)
                                <a href="{{ $creator->tebex_url }}" target="_blank" rel="noopener"
                                    class="flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-semibold text-zinc-950 hover:bg-brand-400 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 7.5h-9v9h9v-9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8.25V6a2.25 2.25 0 012.25-2.25h2.25M3 15.75V18a2.25 2.25 0 002.25 2.25h2.25m9-16.5H18A2.25 2.25 0 0120.25 6v2.25m0 7.5V18A2.25 2.25 0 0118 20.25h-2.25" />
                                    </svg>
                                    Visit Tebex Store
                                </a>
                            @endif

                            @if ($creator->github_url)
                                <a href="{{ $creator->github_url }}" target="_blank" rel="noopener"
                                    class="flex items-center gap-2 rounded-lg border border-zinc-800 bg-zinc-900 px-4 py-2.5 text-sm font-semibold text-zinc-200 hover:bg-zinc-800 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.221-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.026 2.747-1.026.546 1.378.203 2.397.1 2.65.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.919.678 1.852 0 1.336-.012 2.415-.012 2.743 0 .268.18.58.688.482A10.02 10.02 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                                    </svg>
                                    GitHub Profile
                                </a>
                            @endif

                            @if ($creator->discord_invite_url)
                                <a href="{{ $creator->discord_invite_url }}" target="_blank" rel="noopener"
                                    class="flex items-center gap-2 rounded-lg border border-indigo-500/30 bg-indigo-500/10 px-4 py-2.5 text-sm font-semibold text-indigo-300 hover:bg-indigo-500/20 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                                    </svg>
                                    Join Discord
                                </a>
                            @endif

                            @if ($creator->youtube_url)
                                <a href="{{ $creator->youtube_url }}" target="_blank" rel="noopener"
                                    class="flex items-center gap-2 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-2.5 text-sm font-semibold text-red-300 hover:bg-red-500/20 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z" />
                                    </svg>
                                    Watch Videos
                                </a>
                            @endif

                            @if ($creator->website_url)
                                <a href="{{ $creator->website_url }}" target="_blank" rel="noopener"
                                    class="flex items-center gap-2 rounded-lg border border-zinc-800 bg-zinc-900 px-4 py-2.5 text-sm font-semibold text-zinc-200 hover:bg-zinc-800 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 100-18 9 9 0 000 18z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.6 9h16.8M3.6 15h16.8M11.5 3a17 17 0 000 18M12.5 3a17 17 0 010 18" />
                                    </svg>
                                    Website
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </x-slot:hero>

    <h2 class="text-lg font-semibold text-zinc-100 mb-4">Published Resources</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse ($resources as $resource)
            <x-resource-card :resource="$resource" />
        @empty
            <div class="col-span-full rounded-xl border border-dashed border-zinc-900 py-16 text-center text-zinc-500">
                {{ $creator->name }} hasn't published any approved resources yet.
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $resources->links() }}
    </div>
</x-app-layout>
