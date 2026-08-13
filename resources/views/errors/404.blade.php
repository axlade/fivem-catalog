<x-app-layout
    title="Page Not Found"
    robots="noindex, nofollow">
    <div class="max-w-2xl mx-auto py-16 text-center">
        <p class="text-sm font-semibold uppercase tracking-wider text-brand-500">404</p>
        <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-zinc-50 sm:text-4xl">Page not found</h1>
        <p class="mt-4 text-zinc-400">
            The page you're looking for doesn't exist, or the resource may have been removed by its creator.
        </p>

        <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ route('home') }}"
                class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-semibold text-zinc-950 hover:bg-brand-400 transition">
                Browse FiveM Scripts
            </a>
            <a href="{{ route('services.index') }}"
                class="rounded-lg border border-zinc-800 bg-zinc-900 px-5 py-2.5 text-sm font-semibold text-zinc-200 hover:bg-zinc-800 transition">
                Hire a Developer
            </a>
        </div>
    </div>
</x-app-layout>
