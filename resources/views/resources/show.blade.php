@php
    $categoryLabels = ['scripts' => 'Scripts', 'mlos' => 'MLOs', 'eup' => 'EUP', 'vehicles' => 'Vehicles'];
    $plainDescription = \Illuminate\Support\Str::limit(strip_tags($resource->description), 200, '');
    $resourceImage = $resource->thumbnail_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($resource->thumbnail_path)
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($resource->thumbnail_path)
        : null;

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $resource->title,
        'description' => $plainDescription,
        'category' => $categoryLabels[$resource->category] ?? $resource->category,
        'url' => route('resources.show', $resource),
        'brand' => [
            '@type' => 'Brand',
            'name' => $resource->user->name,
        ],
        'offers' => [
            '@type' => 'Offer',
            'price' => (string) $resource->price,
            'priceCurrency' => 'USD',
            'availability' => 'https://schema.org/InStock',
            'url' => route('resources.show', $resource),
        ],
    ];

    if ($resourceImage) {
        $schema['image'] = $resourceImage;
    }

    if ($resource->ratings->isNotEmpty()) {
        $schema['aggregateRating'] = [
            '@type' => 'AggregateRating',
            'ratingValue' => (string) $resource->averageRating(),
            'reviewCount' => (string) $resource->ratings->count(),
            'bestRating' => '5',
            'worstRating' => '1',
        ];
    }

    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => $categoryLabels[$resource->category] ?? $resource->category, 'item' => route('home', ['category' => $resource->category])],
            ['@type' => 'ListItem', 'position' => 3, 'name' => $resource->title, 'item' => route('resources.show', $resource)],
        ],
    ];
@endphp

<x-app-layout
    :title="$resource->title"
    :description="$plainDescription ?: null"
    :image="$resourceImage"
    :canonical="route('resources.show', $resource)"
    og-type="product"
    :og-price="number_format($resource->price, 2, '.', '')"
    :schema="json_encode([$schema, $breadcrumbSchema])">
    <nav aria-label="Breadcrumb" class="mb-6">
        <ol class="flex flex-wrap items-center gap-1.5 text-sm text-zinc-500">
            <li><a href="{{ route('home') }}" class="hover:text-brand-400 transition">Home</a></li>
            <li aria-hidden="true">/</li>
            <li><a href="{{ route('home', ['category' => $resource->category]) }}" class="hover:text-brand-400 transition capitalize">{{ $categoryLabels[$resource->category] ?? $resource->category }}</a></li>
            <li aria-hidden="true">/</li>
            <li class="text-zinc-300 truncate max-w-[240px]" aria-current="page">{{ $resource->title }}</li>
        </ol>
    </nav>

    @unless ($resource->isApproved())
        <div class="mb-6 rounded-lg border px-4 py-3 text-sm
            {{ $resource->isPending() ? 'border-brand-800 bg-brand-950/30 text-brand-300' : 'border-red-800 bg-red-950/30 text-red-300' }}">
            @if ($resource->isPending())
                This resource is pending moderation and is only visible to you and admins.
            @else
                This resource was rejected and is only visible to you and admins.
            @endif
        </div>
    @endunless

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Main content --}}
        <div class="lg:col-span-2 space-y-8">
            <x-media-carousel :slides="$resource->mediaSlides()" />

            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="rounded-full bg-zinc-800 px-2.5 py-1 text-xs font-medium text-zinc-300 uppercase">{{ $resource->framework }}</span>
                    <span class="rounded-full bg-zinc-800 px-2.5 py-1 text-xs font-medium text-zinc-400 capitalize">{{ $resource->category }}</span>
                </div>
                <h1 class="text-3xl font-bold text-zinc-100">{{ $resource->title }}</h1>
            </div>

            <div class="prose prose-invert prose-zinc max-w-none prose-a:text-brand-400">
                {!! \Illuminate\Support\Str::markdown($resource->description, ['html_input' => 'escape', 'allow_unsafe_links' => false]) !!}
            </div>

            @if ($resource->tags->isNotEmpty())
                <div class="flex flex-wrap gap-2">
                    @foreach ($resource->tags as $tag)
                        <span class="rounded-full bg-zinc-800 px-3 py-1 text-xs text-zinc-300">{{ $tag->name }}</span>
                    @endforeach
                </div>
            @endif

            @if ($resource->updates->isNotEmpty())
                <div>
                    <h2 class="text-lg font-semibold text-zinc-100 mb-4">Changelog</h2>
                    <div class="space-y-4">
                        @foreach ($resource->updates as $update)
                            <div class="rounded-xl border border-zinc-900 bg-zinc-900/40 p-5">
                                <div class="flex items-center justify-between gap-3 mb-2">
                                    <h3 class="font-semibold text-zinc-100">{{ $update->title }}</h3>
                                    <span class="shrink-0 text-xs text-zinc-500">{{ $update->created_at->format('M j, Y') }}</span>
                                </div>
                                <div class="prose prose-invert prose-zinc prose-sm max-w-none prose-a:text-brand-400">
                                    {!! \Illuminate\Support\Str::markdown($update->body, ['html_input' => 'escape', 'allow_unsafe_links' => false]) !!}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Ratings --}}
            <div id="ratings">
                <div class="flex items-center gap-3 mb-4">
                    <h2 class="text-lg font-semibold text-zinc-100">Rating</h2>
                    @if ($resource->averageRating())
                        <span class="inline-flex items-center gap-1 text-sm text-zinc-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-brand-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.368 2.447a1 1 0 00-.363 1.118l1.287 3.957c.3.922-.755 1.688-1.538 1.118l-3.367-2.447a1 1 0 00-1.176 0l-3.367 2.447c-.783.57-1.838-.196-1.539-1.118l1.287-3.957a1 1 0 00-.363-1.118L2.062 9.385c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 00.95-.69l1.286-3.958z" />
                            </svg>
                            <span class="text-zinc-200 font-semibold">{{ $resource->averageRating() }}</span>
                            ({{ $resource->ratings->count() }} {{ Str::plural('rating', $resource->ratings->count()) }})
                        </span>
                    @else
                        <span class="text-sm text-zinc-500">No ratings yet</span>
                    @endif
                </div>

                @auth
                    @if ($resource->isApproved() && auth()->id() !== $resource->user_id)
                        @php($myRating = $resource->ratings->firstWhere('user_id', auth()->id()))
                        <form method="POST" action="{{ route('resources.ratings.store', $resource) }}"
                            x-data="{ rating: {{ old('rating', $myRating->rating ?? 0) }} }"
                            class="rounded-xl border border-zinc-900 bg-zinc-900/40 p-5 mb-6">
                            @csrf

                            <p class="text-sm font-medium text-zinc-300 mb-3">{{ $myRating ? 'Update your rating' : 'Rate this resource' }}</p>

                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <button type="button" @click="rating = {{ $i }}" class="p-0.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" :class="rating >= {{ $i }} ? 'text-brand-400' : 'text-zinc-700'" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.368 2.447a1 1 0 00-.363 1.118l1.287 3.957c.3.922-.755 1.688-1.538 1.118l-3.367-2.447a1 1 0 00-1.176 0l-3.367 2.447c-.783.57-1.838-.196-1.539-1.118l1.287-3.957a1 1 0 00-.363-1.118L2.062 9.385c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 00.95-.69l1.286-3.958z" />
                                            </svg>
                                        </button>
                                    @endfor
                                    <input type="hidden" name="rating" :value="rating">
                                </div>

                                <button type="submit" x-bind:disabled="rating < 1"
                                    class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-semibold text-zinc-950 hover:bg-brand-400 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                    {{ $myRating ? 'Update Rating' : 'Submit Rating' }}
                                </button>
                            </div>
                            @error('rating') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                        </form>
                    @endif
                @endauth
            </div>

            {{-- Comments --}}
            <div id="comments">
                <h2 class="text-lg font-semibold text-zinc-100 mb-4">
                    Comments
                    @if ($resource->comments->isNotEmpty())
                        <span class="text-zinc-500 font-normal">({{ $resource->comments->count() }})</span>
                    @endif
                </h2>

                @auth
                    @if ($resource->isApproved())
                        <form method="POST" action="{{ route('resources.comments.store', $resource) }}" class="rounded-xl border border-zinc-900 bg-zinc-900/40 p-5 mb-6">
                            @csrf

                            <textarea name="body" rows="3" placeholder="Ask a question or share feedback about this resource"
                                class="w-full rounded-lg border-zinc-700 bg-zinc-950 text-sm text-zinc-100 focus:border-brand-500 focus:ring-brand-500">{{ old('body') }}</textarea>
                            @error('body') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror

                            <div class="flex justify-end mt-3">
                                <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-semibold text-zinc-950 hover:bg-brand-400 transition">
                                    Post Comment
                                </button>
                            </div>
                        </form>
                    @endif
                @endauth

                <div class="space-y-4">
                    @forelse ($resource->comments as $comment)
                        <div class="rounded-xl border border-zinc-900 bg-zinc-900/40 p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-full bg-zinc-800 flex items-center justify-center text-xs font-semibold text-brand-400 overflow-hidden shrink-0">
                                        @if ($comment->user->avatar_path)
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($comment->user->avatar_path) }}" alt="{{ $comment->user->name }}" class="h-full w-full object-cover" loading="lazy" decoding="async">
                                        @else
                                            {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-zinc-100">
                                            {{ $comment->user->name }}
                                            @if ($comment->user_id === $resource->user_id)
                                                <span class="ml-1 rounded-full bg-brand-500/10 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-brand-400">Author</span>
                                            @endif
                                        </p>
                                        <span class="text-xs text-zinc-500">{{ $comment->created_at->format('M j, Y') }}</span>
                                    </div>
                                </div>
                                @auth
                                    @if (auth()->id() === $comment->user_id || auth()->user()->isAdmin())
                                        <form method="POST" action="{{ route('resources.comments.destroy', [$resource, $comment]) }}"
                                            onsubmit="return confirm('Delete this comment?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-zinc-500 hover:text-red-400 transition shrink-0">Delete</button>
                                        </form>
                                    @endif
                                @endauth
                            </div>
                            <p class="mt-3 text-sm text-zinc-400">{{ $comment->body }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-zinc-500">Be the first to comment on this resource.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            <div class="rounded-xl border border-zinc-900 bg-zinc-900/40 p-6">
                @if ($resource->isFree())
                    <a href="{{ route('resources.download', $resource) }}" target="_blank" rel="noopener"
                        class="block w-full text-center rounded-lg bg-emerald-600 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-500 transition">
                        {{ $resource->hasDownloadFile() ? 'Download' : 'Download (GitHub)' }}
                    </a>
                @else
                    <p class="text-2xl font-bold text-zinc-100 mb-3">${{ number_format($resource->price, 2) }}</p>
                    <a href="{{ route('resources.download', $resource) }}" target="_blank" rel="noopener"
                        class="block w-full text-center rounded-lg bg-brand-500 px-4 py-3 text-sm font-semibold text-zinc-950 hover:bg-brand-400 transition">
                        Purchase on Tebex
                    </a>
                @endif
            </div>

            <div class="rounded-xl border border-zinc-900 bg-zinc-900/40 p-6">
                <h3 class="text-sm font-semibold text-zinc-100 mb-3">Creator</h3>
                <a href="{{ route('creators.show', $resource->user) }}" class="flex items-center gap-3 hover:opacity-80 transition">
                    <div class="h-10 w-10 rounded-full bg-zinc-800 flex items-center justify-center text-sm font-semibold text-brand-400 overflow-hidden shrink-0">
                        @if ($resource->user->avatar_path)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($resource->user->avatar_path) }}" alt="{{ $resource->user->name }}" class="h-full w-full object-cover" loading="lazy" decoding="async">
                        @else
                            {{ strtoupper(substr($resource->user->name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-zinc-100 truncate">{{ $resource->user->name }}</p>
                        <p class="text-xs text-zinc-500">View profile</p>
                    </div>
                </a>
            </div>

            <div class="rounded-xl border border-zinc-900 bg-zinc-900/40 p-6 text-sm text-zinc-400 space-y-3">
                <div class="flex justify-between">
                    <span>Category</span>
                    <span class="text-zinc-200 capitalize">{{ $resource->category }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Framework</span>
                    <span class="text-zinc-200 uppercase">{{ $resource->framework }}</span>
                </div>
                @if ($resource->isFree())
                    <div class="flex justify-between">
                        <span>Downloads</span>
                        <span class="text-zinc-200">{{ number_format($resource->downloads_count) }}</span>
                    </div>
                @endif
                <div class="flex justify-between">
                    <span>Views</span>
                    <span class="text-zinc-200">{{ number_format($resource->views_count) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Published</span>
                    <span class="text-zinc-200">{{ $resource->created_at->format('M Y') }}</span>
                </div>
            </div>

            <div class="rounded-xl border border-zinc-900 bg-zinc-900/40 p-6">
                <h3 class="text-sm font-semibold text-zinc-100 mb-2">Need help customizing this?</h3>
                <p class="text-sm text-zinc-400 mb-4">Hire a verified developer to configure, extend, or debug this resource for your server.</p>
                <a href="{{ route('services.index') }}"
                    class="block w-full text-center rounded-lg border border-brand-700/60 px-4 py-2.5 text-sm font-semibold text-brand-400 hover:bg-brand-500/10 transition">
                    Hire a Verified Developer
                </a>
            </div>
        </div>
    </div>

    @if ($resource->isApproved())
        <script>
            (function () {
                var startedAt = Date.now();
                var sent = false;

                function sendDuration() {
                    if (sent) return;
                    var seconds = Math.round((Date.now() - startedAt) / 1000);
                    if (seconds < 1) return;
                    sent = true;

                    var data = new FormData();
                    data.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                    data.append('duration', seconds);

                    navigator.sendBeacon(@json(route('resources.trackTime', $resource)), data);
                }

                document.addEventListener('visibilitychange', function () {
                    if (document.visibilityState === 'hidden') sendDuration();
                });
                window.addEventListener('pagehide', sendDuration);
            })();
        </script>
    @endif
</x-app-layout>
