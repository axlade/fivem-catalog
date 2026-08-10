<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
            $siteName = config('app.name', 'FiveM-Catalog');
            $metaTitle = $title ?? $siteName;
            $pageTitle = isset($title) ? "{$title} · {$siteName}" : $siteName;
            $metaDescription = $description ?? 'Discover, download, and sell free and premium FiveM scripts, MLOs, EUP, and vehicles — the community marketplace for FiveM server resources.';
            $metaImage = $image ?? asset('logo.svg');
            $metaCanonical = $canonical ?? url()->current();
        @endphp

        <title>{{ $pageTitle }}</title>
        <meta name="description" content="{{ \Illuminate\Support\Str::limit($metaDescription, 160, '') }}">
        <meta name="robots" content="{{ $robots ?? 'index, follow' }}">
        <link rel="canonical" href="{{ $metaCanonical }}">

        {{-- Open Graph --}}
        <meta property="og:type" content="{{ $ogType ?? 'website' }}">
        <meta property="og:site_name" content="{{ $siteName }}">
        <meta property="og:title" content="{{ $metaTitle }}">
        <meta property="og:description" content="{{ \Illuminate\Support\Str::limit($metaDescription, 200, '') }}">
        <meta property="og:url" content="{{ $metaCanonical }}">
        <meta property="og:image" content="{{ $metaImage }}">
        <meta property="og:locale" content="en_US">

        {{-- Twitter Card --}}
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $metaTitle }}">
        <meta name="twitter:description" content="{{ \Illuminate\Support\Str::limit($metaDescription, 200, '') }}">
        <meta name="twitter:image" content="{{ $metaImage }}">

        <link rel="icon" href="{{ asset('logo.svg') }}" type="image/svg+xml">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script type="application/ld+json">{!! json_encode([
            '@@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $siteName,
            'url' => url('/'),
            'description' => 'Marketplace for FiveM scripts, MLOs, EUP, and vehicles for GTA RP servers.',
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => url('/').'?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ]) !!}</script>

        <script type="application/ld+json">{!! json_encode([
            '@@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $siteName,
            'url' => url('/'),
            'logo' => asset('logo.svg'),
            'description' => 'Marketplace for FiveM scripts, MLOs, EUP, and vehicles for GTA RP servers.',
        ]) !!}</script>

        @isset($schema)
            <script type="application/ld+json">{!! $schema !!}</script>
        @endisset
    </head>
    <body class="font-sans antialiased bg-zinc-950 text-zinc-200">
        <div class="min-h-screen flex flex-col">
            <header class="sticky top-0 z-40 border-b border-zinc-900 bg-zinc-950/90 backdrop-blur">
                <nav class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-[auto_1fr_auto] md:grid-cols-[minmax(0,1fr)_300px_minmax(0,1fr)] h-16 items-center gap-4 sm:gap-6">
                        <div class="flex items-center gap-6 min-w-0">
                            <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0 text-xl font-extrabold tracking-tight text-brand-500">
                                <x-logo-icon class="h-7 w-auto" />
                                <span>FIVEM<span class="text-zinc-100">-CATALOG</span></span>
                            </a>

                            <div class="hidden xl:flex items-center gap-5 text-sm font-medium text-zinc-400">
                                <a href="{{ route('home') }}" class="hover:text-zinc-100 transition">Resources</a>
                                <a href="{{ route('services.index') }}" class="hover:text-zinc-100 transition">Services</a>
                            </div>
                        </div>

                        <form action="{{ route('home') }}" method="GET" class="hidden md:block w-full">
                            <label for="q" class="sr-only">Search resources</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-zinc-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
                                    </svg>
                                </div>
                                <input
                                    type="text"
                                    name="q"
                                    id="q"
                                    value="{{ request('q') }}"
                                    placeholder="Search scripts, MLOs, vehicles..."
                                    class="w-full rounded-lg border-zinc-900 bg-zinc-900/60 pl-9 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:border-brand-500 focus:ring-brand-500"
                                >
                            </div>
                        </form>

                        <div class="flex items-center justify-end gap-3 min-w-0">
                            @guest
                                <a href="{{ route('login') }}" class="text-sm font-medium text-zinc-300 hover:text-zinc-100 transition">Login</a>
                                <a href="{{ route('register') }}" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-semibold text-zinc-950 hover:bg-brand-400 transition">Sign In</a>
                            @else
                                <x-user-menu />
                            @endguest
                        </div>
                    </div>

                    {{-- Mobile search (own row, since the 3-column grid hides it above) --}}
                    <form action="{{ route('home') }}" method="GET" class="md:hidden pb-3">
                        <label for="q-mobile" class="sr-only">Search resources</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-zinc-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
                                </svg>
                            </div>
                            <input
                                type="text"
                                name="q"
                                id="q-mobile"
                                value="{{ request('q') }}"
                                placeholder="Search scripts, MLOs, vehicles..."
                                class="w-full rounded-lg border-zinc-900 bg-zinc-900/60 pl-9 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:border-brand-500 focus:ring-brand-500"
                            >
                        </div>
                    </form>
                </nav>
            </header>

            @isset($hero)
                {{ $hero }}
            @endisset

            @isset($header)
                <div class="border-b border-zinc-900 bg-zinc-900/40">
                    <div class="max-w-screen-2xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </div>
            @endisset

            <main class="flex-1 max-w-screen-2xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
                @if (session('status'))
                    <div class="mb-6 rounded-lg border border-emerald-800 bg-emerald-950/50 px-4 py-3 text-sm text-emerald-300">
                        {{ session('status') }}
                    </div>
                @endif

                {{ $slot }}
            </main>

            <footer class="border-t border-zinc-900 py-8 text-center text-sm text-zinc-500 space-y-2">
                <p>&copy; {{ date('Y') }} FiveM-Catalog. Not affiliated with Cfx.re or Rockstar Games.</p>
                <p>
                    <a href="{{ route('legal.tos') }}" class="hover:text-brand-400 transition">Terms of Service</a>
                </p>
            </footer>
        </div>
    </body>
</html>
