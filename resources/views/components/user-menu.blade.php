<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    <button @click="open = !open" type="button" class="flex items-center gap-2 rounded-lg px-2 py-1.5 hover:bg-zinc-900 transition">
        <div class="h-8 w-8 rounded-full bg-zinc-800 flex items-center justify-center text-xs font-semibold text-brand-400 overflow-hidden">
            @if (auth()->user()->avatar_path)
                <img src="{{ \Illuminate\Support\Facades\Storage::url(auth()->user()->avatar_path) }}" alt="{{ auth()->user()->name }}" class="h-full w-full object-cover">
            @else
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            @endif
        </div>
        <span class="hidden sm:block text-sm font-medium text-zinc-300">{{ auth()->user()->name }}</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div x-show="open" x-cloak x-transition
        class="absolute right-0 mt-2 w-56 rounded-xl border border-zinc-900 bg-zinc-900/95 backdrop-blur shadow-xl py-1 text-sm">
        <a href="{{ route('home') }}" class="block px-4 py-2 text-zinc-300 hover:bg-zinc-800 hover:text-zinc-100 transition">
            Browse Catalog
        </a>

        <a href="{{ route('creators.show', auth()->user()) }}" class="block px-4 py-2 text-zinc-300 hover:bg-zinc-800 hover:text-zinc-100 transition">
            My Public Profile
        </a>

        <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-zinc-300 hover:bg-zinc-800 hover:text-zinc-100 transition">
            Dashboard
        </a>

        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-zinc-300 hover:bg-zinc-800 hover:text-zinc-100 transition">
            My Profile &amp; Account Settings
        </a>

        @if (auth()->user()->isAdmin())
            <a href="{{ route('admin.moderation.index') }}" class="block px-4 py-2 text-zinc-300 hover:bg-zinc-800 hover:text-zinc-100 transition">
                Moderation Queue
            </a>
        @endif

        <div class="my-1 border-t border-zinc-800"></div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-2 text-zinc-300 hover:bg-zinc-800 hover:text-zinc-100 transition">
                Log Out
            </button>
        </form>
    </div>
</div>
