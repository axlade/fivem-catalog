@php
    $plainDescription = \Illuminate\Support\Str::limit(strip_tags($service->description), 200, '');
    $serviceImage = $service->user->avatar_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($service->user->avatar_path)
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($service->user->avatar_path)
        : null;

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'name' => $service->title,
        'description' => $plainDescription,
        'serviceType' => ucfirst($service->category),
        'url' => route('services.show', $service),
        'provider' => [
            '@type' => 'Person',
            'name' => $service->user->name,
        ],
        'offers' => [
            '@type' => 'Offer',
            'priceCurrency' => 'USD',
            'price' => (string) $service->starting_price,
            'url' => route('services.show', $service),
        ],
    ];

    if ($service->reviews->isNotEmpty()) {
        $schema['aggregateRating'] = [
            '@type' => 'AggregateRating',
            'ratingValue' => (string) $service->averageRating(),
            'reviewCount' => (string) $service->reviews->count(),
            'bestRating' => '5',
            'worstRating' => '1',
        ];
    }
@endphp

<x-app-layout
    :title="$service->title"
    :description="$plainDescription ?: null"
    :image="$serviceImage"
    :canonical="route('services.show', $service)"
    :schema="json_encode($schema)">
    <div class="mb-6">
        <a href="{{ route('services.index') }}" class="inline-flex items-center gap-2 text-sm text-zinc-400 hover:text-brand-400 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Back to Services
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Main content --}}
        <div class="lg:col-span-2 space-y-8">
            <div>
                <span class="inline-block rounded-full bg-zinc-800 px-2.5 py-1 text-xs font-medium text-zinc-400 capitalize mb-3">{{ $service->category }}</span>
                <h1 class="text-3xl font-bold text-zinc-100">{{ $service->title }}</h1>
                @if ($service->averageRating())
                    <p class="mt-2 inline-flex items-center gap-1 text-sm text-zinc-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-brand-400" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.368 2.447a1 1 0 00-.363 1.118l1.287 3.957c.3.922-.755 1.688-1.538 1.118l-3.367-2.447a1 1 0 00-1.176 0l-3.367 2.447c-.783.57-1.838-.196-1.539-1.118l1.287-3.957a1 1 0 00-.363-1.118L2.062 9.385c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 00.95-.69l1.286-3.958z" />
                        </svg>
                        <span class="text-zinc-200 font-semibold">{{ $service->averageRating() }}</span>
                        ({{ $service->reviews->count() }} {{ Str::plural('review', $service->reviews->count()) }})
                    </p>
                @endif
            </div>

            <div class="text-zinc-300 whitespace-pre-line leading-relaxed">{{ $service->description }}</div>

            {{-- Reviews --}}
            <div id="reviews">
                <h2 class="text-lg font-semibold text-zinc-100 mb-4">Reviews</h2>

                @auth
                    @if (auth()->id() !== $service->user_id)
                        @php($myReview = $service->reviews->firstWhere('reviewer_id', auth()->id()))
                        <form method="POST" action="{{ route('services.reviews.store', $service) }}"
                            x-data="{ rating: {{ old('rating', $myReview->rating ?? 0) }} }"
                            class="rounded-xl border border-zinc-900 bg-zinc-900/40 p-5 mb-6">
                            @csrf

                            <p class="text-sm font-medium text-zinc-300 mb-2">{{ $myReview ? 'Update your review' : 'Leave a review' }}</p>

                            <div class="flex items-center gap-1 mb-3">
                                @for ($i = 1; $i <= 5; $i++)
                                    <button type="button" @click="rating = {{ $i }}" class="p-0.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" :class="rating >= {{ $i }} ? 'text-brand-400' : 'text-zinc-700'" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.368 2.447a1 1 0 00-.363 1.118l1.287 3.957c.3.922-.755 1.688-1.538 1.118l-3.367-2.447a1 1 0 00-1.176 0l-3.367 2.447c-.783.57-1.838-.196-1.539-1.118l1.287-3.957a1 1 0 00-.363-1.118L2.062 9.385c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 00.95-.69l1.286-3.958z" />
                                        </svg>
                                    </button>
                                @endfor
                                <input type="hidden" name="rating" :value="rating">
                            </div>
                            @error('rating') <p class="mb-2 text-sm text-red-400">{{ $message }}</p> @enderror

                            <textarea name="comment" rows="3" placeholder="Share your experience working with this developer (optional)"
                                class="w-full rounded-lg border-zinc-700 bg-zinc-950 text-sm text-zinc-100 focus:border-brand-500 focus:ring-brand-500">{{ old('comment', $myReview->comment ?? '') }}</textarea>
                            @error('comment') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror

                            <div class="flex justify-end mt-3">
                                <button type="submit" x-bind:disabled="rating < 1"
                                    class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-semibold text-zinc-950 hover:bg-brand-400 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                    {{ $myReview ? 'Update Review' : 'Submit Review' }}
                                </button>
                            </div>
                        </form>
                    @endif
                @endauth

                <div class="space-y-4">
                    @forelse ($service->reviews as $review)
                        <div class="rounded-xl border border-zinc-900 bg-zinc-900/40 p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-full bg-zinc-800 flex items-center justify-center text-xs font-semibold text-brand-400 overflow-hidden shrink-0">
                                        @if ($review->reviewer->avatar_path)
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($review->reviewer->avatar_path) }}" alt="{{ $review->reviewer->name }}" class="h-full w-full object-cover" loading="lazy" decoding="async">
                                        @else
                                            {{ strtoupper(substr($review->reviewer->name, 0, 1)) }}
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-zinc-100">{{ $review->reviewer->name }}</p>
                                        <div class="flex items-center gap-0.5">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 {{ $review->rating >= $i ? 'text-brand-400' : 'text-zinc-700' }}" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.368 2.447a1 1 0 00-.363 1.118l1.287 3.957c.3.922-.755 1.688-1.538 1.118l-3.367-2.447a1 1 0 00-1.176 0l-3.367 2.447c-.783.57-1.838-.196-1.539-1.118l1.287-3.957a1 1 0 00-.363-1.118L2.062 9.385c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 00.95-.69l1.286-3.958z" />
                                                </svg>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 shrink-0">
                                    <span class="text-xs text-zinc-500">{{ $review->created_at->format('M j, Y') }}</span>
                                    @auth
                                        @if (auth()->id() === $review->reviewer_id || auth()->user()->isAdmin())
                                            <form method="POST" action="{{ route('services.reviews.destroy', [$service, $review]) }}"
                                                onsubmit="return confirm('Delete this review?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs text-zinc-500 hover:text-red-400 transition">Delete</button>
                                            </form>
                                        @endif
                                    @endauth
                                </div>
                            </div>
                            @if ($review->comment)
                                <p class="mt-3 text-sm text-zinc-400">{{ $review->comment }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-zinc-500">Be the first to review this developer.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            <div class="rounded-xl border border-zinc-900 bg-zinc-900/40 p-6">
                <p class="text-xs text-zinc-500 mb-1">Starting at</p>
                <p class="text-2xl font-bold text-zinc-100 mb-4">${{ number_format($service->starting_price, 2) }}</p>
                <a href="{{ route('services.contact', $service) }}"
                    class="block w-full text-center rounded-lg bg-brand-500 px-4 py-3 text-sm font-semibold text-zinc-950 hover:bg-brand-400 transition">
                    Contact on Discord
                </a>
            </div>

            <div class="rounded-xl border border-zinc-900 bg-zinc-900/40 p-6">
                <h3 class="text-sm font-semibold text-zinc-100 mb-3">Developer</h3>
                <a href="{{ route('creators.show', $service->user) }}" class="flex items-center gap-3 hover:opacity-80 transition">
                    <div class="h-10 w-10 rounded-full bg-zinc-800 flex items-center justify-center text-sm font-semibold text-brand-400 overflow-hidden shrink-0">
                        @if ($service->user->avatar_path)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($service->user->avatar_path) }}" alt="{{ $service->user->name }}" class="h-full w-full object-cover" loading="lazy" decoding="async">
                        @else
                            {{ strtoupper(substr($service->user->name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-zinc-100 truncate flex items-center gap-1">
                            {{ $service->user->name }}
                            @if ($service->is_verified)
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-brand-500 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0112 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 013.498 1.307 4.49 4.49 0 011.307 3.497A4.49 4.49 0 0121.75 12a4.49 4.49 0 01-1.549 3.397 4.49 4.49 0 01-1.307 3.497 4.49 4.49 0 01-3.497 1.307A4.49 4.49 0 0112 21.75a4.49 4.49 0 01-3.397-1.549 4.49 4.49 0 01-3.497-1.307 4.49 4.49 0 01-1.307-3.497A4.49 4.49 0 012.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 011.307-3.497 4.49 4.49 0 013.497-1.307zm7.007 6.387a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                                </svg>
                            @endif
                        </p>
                        <p class="text-xs text-zinc-500">View profile</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
