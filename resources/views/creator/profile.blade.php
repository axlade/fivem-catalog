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
                                <a href="{{ $creator->tebex_url }}" target="_blank" rel="noopener nofollow ugc"
                                    class="flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-semibold text-zinc-950 hover:bg-brand-400 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 21.25 50">
                                        <path d="M11.9031 12.7052C13.8816 9.5737 17.4647 8.66646 17.4647 8.66646C17.4647 8.66646 10.6263 6.93188 10.6263 0C10.6263 6.93188 3.78397 8.66646 3.78397 8.66646C3.78397 8.66646 7.36838 9.5737 9.34826 12.7052H0V24.7107L2.12526 20.9928H6.37447V41.7124L14.8742 50V24.6308C12.7066 23.6565 9.59277 21.1797 8.47727 19.2904C10.3818 19.851 12.9366 20.5882 14.9258 20.9941H21.25V12.7052H11.9031Z" />
                                    </svg>
                                    Visit Tebex Store
                                </a>
                            @endif

                            @if ($creator->github_url)
                                <a href="{{ $creator->github_url }}" target="_blank" rel="noopener nofollow ugc"
                                    class="flex items-center gap-2 rounded-lg border border-zinc-800 bg-zinc-900 px-4 py-2.5 text-sm font-semibold text-zinc-200 hover:bg-zinc-800 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12" />
                                    </svg>
                                    GitHub Profile
                                </a>
                            @endif

                            @if ($creator->discord_invite_url)
                                <a href="{{ $creator->discord_invite_url }}" target="_blank" rel="noopener nofollow ugc"
                                    class="flex items-center gap-2 rounded-lg border border-indigo-500/30 bg-indigo-500/10 px-4 py-2.5 text-sm font-semibold text-indigo-300 hover:bg-indigo-500/20 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M20.317 4.3698a19.7913 19.7913 0 00-4.8851-1.5152.0741.0741 0 00-.0785.0371c-.211.3753-.4447.8648-.6083 1.2495-1.8447-.2762-3.68-.2762-5.4868 0-.1636-.3933-.4058-.8742-.6177-1.2495a.077.077 0 00-.0785-.037 19.7363 19.7363 0 00-4.8852 1.515.0699.0699 0 00-.0321.0277C.5334 9.0458-.319 13.5799.0992 18.0578a.0824.0824 0 00.0312.0561c2.0528 1.5076 4.0413 2.4228 5.9929 3.0294a.0777.0777 0 00.0842-.0276c.4616-.6304.8731-1.2952 1.226-1.9942a.076.076 0 00-.0416-.1057c-.6528-.2476-1.2743-.5495-1.8722-.8923a.077.077 0 01-.0076-.1277c.1258-.0943.2517-.1923.3718-.2914a.0743.0743 0 01.0776-.0105c3.9278 1.7933 8.18 1.7933 12.0614 0a.0739.0739 0 01.0785.0095c.1202.099.246.1981.3728.2924a.077.077 0 01-.0066.1276 12.2986 12.2986 0 01-1.873.8914.0766.0766 0 00-.0407.1067c.3604.698.7719 1.3628 1.225 1.9932a.076.076 0 00.0842.0286c1.961-.6067 3.9495-1.5219 6.0023-3.0294a.077.077 0 00.0313-.0552c.5004-5.177-.8382-9.6739-3.5485-13.6604a.061.061 0 00-.0312-.0286zM8.02 15.3312c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9555-2.4189 2.157-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419 0 1.3332-.9555 2.4189-2.1569 2.4189zm7.9748 0c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9554-2.4189 2.1569-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419 0 1.3332-.946 2.4189-2.1568 2.4189Z" />
                                    </svg>
                                    Join Discord
                                </a>
                            @endif

                            @if ($creator->youtube_url)
                                <a href="{{ $creator->youtube_url }}" target="_blank" rel="noopener nofollow ugc"
                                    class="flex items-center gap-2 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-2.5 text-sm font-semibold text-red-300 hover:bg-red-500/20 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                                    </svg>
                                    YouTube
                                </a>
                            @endif

                            @if ($creator->website_url)
                                <a href="{{ $creator->website_url }}" target="_blank" rel="noopener nofollow ugc"
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
