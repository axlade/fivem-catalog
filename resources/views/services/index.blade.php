<x-app-layout title="Freelance Services"
    description="Hire verified FiveM developers for custom scripting, mapping, web development, and server optimization. Browse freelance services and contact developers directly on Discord."
    :canonical="route('services.index')">
    <x-slot:header>
        <h1 class="text-xl font-bold text-zinc-100">Hire a Verified Developer</h1>
        <p class="mt-1 text-sm text-zinc-400">Connect with FiveM developers for custom scripting, mapping, web, and optimization work.</p>
    </x-slot:header>

    <div class="grid grid-cols-1 lg:grid-cols-[240px_1fr] gap-8">
        {{-- Sidebar Filters --}}
        <aside class="space-y-6 lg:sticky lg:top-20 lg:self-start">
            <form method="GET" action="{{ route('services.index') }}" id="filters-form" class="space-y-6" x-data
                @change="$el.submit()">
                <div>
                    <label for="q" class="block text-sm font-medium text-zinc-300 mb-1">Search</label>
                    <input type="text" name="q" id="q" value="{{ request('q') }}" placeholder="Search services..."
                        class="w-full rounded-lg border-zinc-700 bg-zinc-900 text-zinc-100 text-sm focus:border-brand-500 focus:ring-brand-500"
                        @keydown.enter.prevent="$el.form.submit()">
                </div>

                <div class="rounded-xl border border-zinc-900 bg-zinc-900/40 p-4">
                    <h3 class="text-sm font-semibold text-zinc-100 mb-3">Category</h3>
                    <div class="space-y-2 text-sm">
                        <label class="flex items-center gap-2 cursor-pointer {{ ! request('category') ? 'text-brand-400 font-medium' : 'text-zinc-400 hover:text-zinc-100' }}">
                            <input type="radio" name="category" value="" class="accent-brand-500" @checked(! request('category'))>
                            All Categories
                        </label>
                        @foreach ($categories as $value => $label)
                            <label class="flex items-center gap-2 cursor-pointer {{ request('category') === $value ? 'text-brand-400 font-medium' : 'text-zinc-400 hover:text-zinc-100' }}">
                                <input type="radio" name="category" value="{{ $value }}" class="accent-brand-500" @checked(request('category') === $value)>
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </form>
        </aside>

        {{-- Services Grid --}}
        <div>
            <div class="flex items-center justify-between mb-6">
                <p class="text-sm text-zinc-500">{{ $services->total() }} services found</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse ($services as $service)
                    <article class="flex flex-col rounded-xl border border-zinc-900 bg-zinc-900/40 overflow-hidden hover:border-brand-700/60 transition p-5">
                        <div class="flex items-center gap-3 mb-4">
                            <x-thumbnail :path="$service->user->avatar_path" :alt="$service->user->name" class="h-10 w-10 rounded-full object-cover shrink-0" />
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-zinc-100 truncate flex items-center gap-1">
                                    {{ $service->user->name }}
                                    @if ($service->is_verified)
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-brand-500 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0112 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 013.498 1.307 4.49 4.49 0 011.307 3.497A4.49 4.49 0 0121.75 12a4.49 4.49 0 01-1.549 3.397 4.49 4.49 0 01-1.307 3.497 4.49 4.49 0 01-3.497 1.307A4.49 4.49 0 0112 21.75a4.49 4.49 0 01-3.397-1.549 4.49 4.49 0 01-3.497-1.307 4.49 4.49 0 01-1.307-3.497A4.49 4.49 0 012.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 011.307-3.497 4.49 4.49 0 013.497-1.307zm7.007 6.387a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                </p>
                                <span class="inline-block rounded-full bg-zinc-800 px-2 py-0.5 text-xs font-medium text-zinc-400 capitalize">{{ $service->category }}</span>
                            </div>
                        </div>

                        <h3 class="font-semibold text-zinc-100 mb-2">{{ $service->title }}</h3>
                        <p class="text-sm text-zinc-400 line-clamp-3 flex-1">{{ \Illuminate\Support\Str::limit(strip_tags($service->description), 140) }}</p>

                        @if ($service->averageRating())
                            <p class="mt-2 inline-flex items-center gap-1 text-xs text-zinc-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-brand-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.368 2.447a1 1 0 00-.363 1.118l1.287 3.957c.3.922-.755 1.688-1.538 1.118l-3.367-2.447a1 1 0 00-1.176 0l-3.367 2.447c-.783.57-1.838-.196-1.539-1.118l1.287-3.957a1 1 0 00-.363-1.118L2.062 9.385c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 00.95-.69l1.286-3.958z" />
                                </svg>
                                <span class="text-zinc-200 font-semibold">{{ $service->averageRating() }}</span>
                                ({{ $service->reviews->count() }})
                            </p>
                        @endif

                        <div class="flex items-center justify-between gap-3 mt-4 pt-4 border-t border-zinc-800">
                            <div>
                                <p class="text-xs text-zinc-500">Starting at</p>
                                <p class="text-lg font-bold text-zinc-100">${{ number_format($service->starting_price, 2) }}</p>
                            </div>
                            <a href="{{ route('services.show', $service) }}"
                                class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-semibold text-zinc-950 hover:bg-brand-400 transition">
                                View Details
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-xl border border-dashed border-zinc-900 py-16 text-center text-zinc-500">
                        No services match your filters yet.
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $services->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
