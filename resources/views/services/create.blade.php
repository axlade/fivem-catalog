<x-creator-layout title="Post a Service">
    <div class="max-w-3xl mx-auto rounded-xl border border-zinc-900 bg-zinc-900/40 p-8">
        <h2 class="text-xl font-bold text-zinc-100 mb-1">Post a Freelance Service</h2>
        <p class="text-sm text-zinc-500 mb-8">Showcase what you offer to server owners. Interested clients will contact you directly on Discord.</p>

        <form method="POST" action="{{ route('creator.services.store') }}" class="space-y-6">
            @csrf

            <div>
                <label for="title" class="block text-sm font-medium text-zinc-300 mb-1">Service Title</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                    placeholder="e.g. Custom ESX Job Script Development"
                    class="w-full rounded-lg border-zinc-700 bg-zinc-950 text-zinc-100 focus:border-brand-500 focus:ring-brand-500">
                @error('title') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="category" class="block text-sm font-medium text-zinc-300 mb-1">Category</label>
                    <select name="category" id="category" required
                        class="w-full rounded-lg border-zinc-700 bg-zinc-950 text-zinc-100 focus:border-brand-500 focus:ring-brand-500">
                        <option value="">Select a category</option>
                        @foreach ($categories as $value => $label)
                            <option value="{{ $value }}" @selected(old('category') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('category') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="starting_price" class="block text-sm font-medium text-zinc-300 mb-1">Starting Price ($)</label>
                    <input type="number" name="starting_price" id="starting_price" value="{{ old('starting_price') }}" min="0" step="0.01" required
                        class="w-full rounded-lg border-zinc-700 bg-zinc-950 text-zinc-100 focus:border-brand-500 focus:ring-brand-500">
                    @error('starting_price') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-zinc-300 mb-1">Description</label>
                <textarea name="description" id="description" rows="6" required
                    placeholder="Describe what's included, your experience, and typical turnaround time."
                    class="w-full rounded-lg border-zinc-700 bg-zinc-950 text-zinc-100 focus:border-brand-500 focus:ring-brand-500">{{ old('description') }}</textarea>
                @error('description') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="rounded-lg border border-zinc-800 bg-zinc-950 p-4 text-sm text-zinc-400">
                Clients will reach you through the Discord invite link on your
                <a href="{{ route('profile.edit') }}" class="text-brand-400 hover:underline">profile settings</a>.
                Make sure it's up to date before publishing.
            </div>

            <div class="flex justify-end pt-4 border-t border-zinc-800">
                <button type="submit" class="bg-[#FF9100] hover:bg-[#e07f00] text-black font-bold py-3 px-6 rounded-lg transition">
                    Publish Service
                </button>
            </div>
        </form>
    </div>
</x-creator-layout>
