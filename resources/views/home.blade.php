<x-app-layout
    title="FiveM Scripts & GTA RP Resources"
    description="Download free and premium FiveM scripts, MLOs, EUP, and vehicles for your GTA RP server. ESX, QBCore, OX & Standalone supported — the FiveM-Catalog marketplace."
    :canonical="route('home')">
    <x-slot:hero>
        <div class="relative overflow-hidden border-b border-zinc-900 bg-zinc-950">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-brand-900/40 via-zinc-950 to-zinc-950"></div>
            <div class="absolute inset-0 opacity-20 bg-[linear-gradient(to_right,#18181b_1px,transparent_1px),linear-gradient(to_bottom,#18181b_1px,transparent_1px)] bg-[size:32px_32px]"></div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
                <span class="inline-block rounded-full border border-brand-800 bg-brand-950/40 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-brand-400">
                    Scripts &middot; MLOs &middot; EUP &middot; Vehicles
                </span>
                <h1 class="mt-6 text-4xl sm:text-5xl font-extrabold tracking-tight text-zinc-50">
                    THE GO-TO MARKETPLACE FOR <span class="text-brand-500">FIVEM SCRIPTS</span>
                </h1>
                <p class="mt-4 max-w-2xl mx-auto text-lg text-zinc-400">
                    Discover, download, and sell FiveM scripts, MLOs, EUP, and vehicles for your GTA RP server &mdash; free and premium, all in one catalog.
                </p>
            </div>
        </div>
    </x-slot:hero>

    <div class="grid grid-cols-1 lg:grid-cols-[240px_1fr] gap-8">
        {{-- Sidebar Filters --}}
        <aside class="space-y-6 lg:sticky lg:top-20 lg:self-start">
            <form method="GET" action="{{ route('home') }}" id="filters-form" class="space-y-6" x-data
                @change="$el.submit()">
                <input type="hidden" name="q" value="{{ request('q') }}">

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

                <div class="rounded-xl border border-zinc-900 bg-zinc-900/40 p-4">
                    <h3 class="text-sm font-semibold text-zinc-100 mb-3">Framework</h3>
                    <div class="space-y-2 text-sm">
                        <label class="flex items-center gap-2 cursor-pointer {{ ! request('framework') ? 'text-brand-400 font-medium' : 'text-zinc-400 hover:text-zinc-100' }}">
                            <input type="radio" name="framework" value="" class="accent-brand-500" @checked(! request('framework'))>
                            All Frameworks
                        </label>
                        @foreach ($frameworks as $value => $label)
                            <label class="flex items-center gap-2 cursor-pointer {{ request('framework') === $value ? 'text-brand-400 font-medium' : 'text-zinc-400 hover:text-zinc-100' }}">
                                <input type="radio" name="framework" value="{{ $value }}" class="accent-brand-500" @checked(request('framework') === $value)>
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-xl border border-zinc-900 bg-zinc-900/40 p-4">
                    <h3 class="text-sm font-semibold text-zinc-100 mb-3">Price</h3>
                    <div class="space-y-2 text-sm">
                        <label class="flex items-center gap-2 cursor-pointer {{ ! request('price') ? 'text-brand-400 font-medium' : 'text-zinc-400 hover:text-zinc-100' }}">
                            <input type="radio" name="price" value="" class="accent-brand-500" @checked(! request('price'))>
                            All
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer {{ request('price') === 'free' ? 'text-emerald-400 font-medium' : 'text-zinc-400 hover:text-zinc-100' }}">
                            <input type="radio" name="price" value="free" class="accent-emerald-500" @checked(request('price') === 'free')>
                            Free
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer {{ request('price') === 'paid' ? 'text-brand-400 font-medium' : 'text-zinc-400 hover:text-zinc-100' }}">
                            <input type="radio" name="price" value="paid" class="accent-brand-500" @checked(request('price') === 'paid')>
                            Paid
                        </label>
                    </div>
                </div>
            </form>
        </aside>

        {{-- Resource Grid --}}
        <div>
            <div class="flex items-center justify-between mb-6">
                <p class="text-sm text-zinc-500">{{ $resources->total() }} resources found</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse ($resources as $resource)
                    <x-resource-card :resource="$resource" />
                @empty
                    <div class="col-span-full rounded-xl border border-dashed border-zinc-900 py-16 text-center text-zinc-500">
                        No resources match your filters yet.
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $resources->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
