<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'FiveM-Catalog') }}</title>
        <meta name="robots" content="noindex, nofollow">

        <link rel="icon" href="{{ asset('logo.svg') }}" type="image/svg+xml">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-zinc-950 text-zinc-200">
        <div class="min-h-screen flex flex-col items-center justify-center px-4 py-10">
            <a href="/" class="flex items-center gap-2 text-2xl font-black tracking-tight">
                <x-logo-icon class="h-9 w-auto" />
                <span><span class="text-[#FF9100] font-black">FIVEM</span>-CATALOG</span>
            </a>

            <div class="w-full sm:max-w-md mt-6 px-6 py-6 bg-zinc-900/50 border border-zinc-900 rounded-xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
