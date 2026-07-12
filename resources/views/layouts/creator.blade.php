<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Creator Dashboard' }} &middot; {{ config('app.name', 'FiveM-Catalog') }}</title>
        <meta name="robots" content="noindex, nofollow">

        <link rel="icon" href="{{ asset('logo.svg') }}" type="image/svg+xml">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-zinc-950 text-zinc-200">
        <div x-data="{ mobileSidebarOpen: false }">
            {{-- Mobile backdrop overlay --}}
            <div x-show="mobileSidebarOpen" x-cloak x-transition.opacity
                @click="mobileSidebarOpen = false"
                class="fixed inset-0 z-40 bg-zinc-950/60 backdrop-blur-sm lg:hidden"></div>

            {{-- Sidebar (fixed drawer on mobile, fixed rail on desktop) --}}
            <aside
                :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                class="fixed inset-y-0 left-0 z-50 w-64 flex flex-col bg-zinc-900 border-r border-zinc-800 transform transition-transform duration-300 ease-in-out lg:translate-x-0">
                <div class="h-16 flex items-center justify-between px-6 border-b border-zinc-900 shrink-0">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 text-lg font-extrabold tracking-tight text-brand-500">
                        <x-logo-icon class="h-6 w-auto" />
                        <span>FIVEM<span class="text-zinc-100">-CATALOG</span></span>
                    </a>
                    <button @click="mobileSidebarOpen = false" type="button" class="lg:hidden text-zinc-500 hover:text-zinc-200" aria-label="Close menu">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <nav class="flex-1 px-3 py-6 space-y-1 text-sm font-medium overflow-y-auto">
                    <a href="{{ route('dashboard') }}" @click="mobileSidebarOpen = false"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 transition {{ request()->routeIs('dashboard') ? 'bg-brand-500/10 text-brand-400' : 'text-zinc-400 hover:bg-zinc-800 hover:text-zinc-100' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        </svg>
                        Overview
                    </a>
                    <a href="{{ route('dashboard') }}#my-resources" @click="mobileSidebarOpen = false"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 transition text-zinc-400 hover:bg-zinc-800 hover:text-zinc-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                        My Resources
                    </a>
                    <a href="{{ route('creator.resources.create') }}" @click="mobileSidebarOpen = false"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 transition {{ request()->routeIs('creator.resources.create') ? 'bg-brand-500/10 text-brand-400' : 'text-zinc-400 hover:bg-zinc-800 hover:text-zinc-100' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Submit a Script
                    </a>
                    <a href="{{ route('creator.analytics') }}" @click="mobileSidebarOpen = false"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 transition {{ request()->routeIs('creator.analytics') ? 'bg-brand-500/10 text-brand-400' : 'text-zinc-400 hover:bg-zinc-800 hover:text-zinc-100' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />
                        </svg>
                        Analytics
                    </a>
                    @if (auth()->user()?->isCreator())
                        <a href="{{ route('creator.services.index') }}" @click="mobileSidebarOpen = false"
                            class="flex items-center gap-3 rounded-lg px-3 py-2.5 transition {{ request()->routeIs('creator.services.*') ? 'bg-brand-500/10 text-brand-400' : 'text-zinc-400 hover:bg-zinc-800 hover:text-zinc-100' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 .621-.504 1.125-1.125 1.125h-15.25a1.125 1.125 0 01-1.125-1.125v-4.25m17.5 0a2.25 2.25 0 00-.659-1.591l-5.223-5.223a2.25 2.25 0 00-1.591-.659h-2.254a2.25 2.25 0 00-1.591.659l-5.223 5.223a2.25 2.25 0 00-.659 1.591m17.5 0h-3.86a2.25 2.25 0 00-2.25 2.25c0 .414-.336.75-.75.75h-3.5a.75.75 0 01-.75-.75 2.25 2.25 0 00-2.25-2.25H2.75" />
                            </svg>
                            My Freelance Services
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" @click="mobileSidebarOpen = false"
                            title="Available to certified developers only — request Creator status from your dashboard"
                            class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-zinc-600 hover:bg-zinc-800/50 hover:text-zinc-500 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                            <span class="flex-1">My Freelance Services</span>
                            <span class="shrink-0 rounded-full bg-zinc-800 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-zinc-500">Certified</span>
                        </a>
                    @endif

                    @if (auth()->user()?->isAdmin())
                        <p class="px-3 pt-4 pb-1 text-[10px] font-semibold uppercase tracking-wider text-zinc-600">Admin</p>

                        <a href="{{ route('admin.statistics.index') }}" @click="mobileSidebarOpen = false"
                            class="flex items-center gap-3 rounded-lg px-3 py-2.5 transition {{ request()->routeIs('admin.statistics.*') ? 'bg-brand-500/10 text-brand-400' : 'text-zinc-400 hover:bg-zinc-800 hover:text-zinc-100' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.5l3-3 4 4 6-7 4 4M3 20.25h18" />
                            </svg>
                            Statistics
                        </a>
                        <a href="{{ route('admin.moderation.index') }}" @click="mobileSidebarOpen = false"
                            class="flex items-center gap-3 rounded-lg px-3 py-2.5 transition {{ request()->routeIs('admin.moderation.*') ? 'bg-brand-500/10 text-brand-400' : 'text-zinc-400 hover:bg-zinc-800 hover:text-zinc-100' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.623 0-1.31-.21-2.571-.598-3.75h-.152c-3.196 0-6.1-1.248-8.25-3.286z" />
                            </svg>
                            Moderation Queue
                        </a>
                        <a href="{{ route('admin.resources.index') }}" @click="mobileSidebarOpen = false"
                            class="flex items-center gap-3 rounded-lg px-3 py-2.5 transition {{ request()->routeIs('admin.resources.*') ? 'bg-brand-500/10 text-brand-400' : 'text-zinc-400 hover:bg-zinc-800 hover:text-zinc-100' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                            </svg>
                            All Scripts
                        </a>
                        <a href="{{ route('admin.users.index') }}" @click="mobileSidebarOpen = false"
                            class="flex items-center gap-3 rounded-lg px-3 py-2.5 transition {{ request()->routeIs('admin.users.*') ? 'bg-brand-500/10 text-brand-400' : 'text-zinc-400 hover:bg-zinc-800 hover:text-zinc-100' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                            Users
                        </a>
                        <a href="{{ route('admin.creator-requests.index') }}" @click="mobileSidebarOpen = false"
                            class="flex items-center gap-3 rounded-lg px-3 py-2.5 transition {{ request()->routeIs('admin.creator-requests.*') ? 'bg-brand-500/10 text-brand-400' : 'text-zinc-400 hover:bg-zinc-800 hover:text-zinc-100' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM3 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 019.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                            </svg>
                            Creator Requests
                        </a>
                    @endif
                </nav>

                <div class="px-3 py-4 border-t border-zinc-900 shrink-0 space-y-1">
                    <a href="{{ route('profile.edit') }}" @click="mobileSidebarOpen = false"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('profile.edit') ? 'bg-brand-500/10 text-brand-400' : 'text-zinc-400 hover:bg-zinc-800 hover:text-zinc-100' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        My Profile
                    </a>
                    <a href="{{ route('home') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-zinc-400 hover:bg-zinc-800 hover:text-zinc-100 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        Back to Catalog
                    </a>
                </div>
            </aside>

            {{-- Main --}}
            <div class="flex flex-col min-w-0 pl-0 lg:pl-64">
                <header class="h-16 flex items-center justify-between px-4 sm:px-6 border-b border-zinc-900 bg-zinc-950/90 backdrop-blur sticky top-0 z-20">
                    <div class="flex items-center gap-3 min-w-0">
                        <button @click="mobileSidebarOpen = true" type="button" class="lg:hidden text-[#FF9100] p-1 -ml-1 shrink-0" aria-label="Open menu">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <h1 class="text-lg font-semibold text-zinc-100 truncate">{{ $title ?? 'Dashboard' }}</h1>
                    </div>

                    <x-user-menu />
                </header>

                <main class="flex-1 p-4 sm:p-6">
                    @if (session('status'))
                        <div class="mb-6 rounded-lg border border-emerald-800 bg-emerald-950/50 px-4 py-3 text-sm text-emerald-300">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
