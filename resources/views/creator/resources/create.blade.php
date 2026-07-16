<x-creator-layout title="Submit a Script">
    <div class="max-w-3xl mx-auto rounded-xl border border-zinc-900 bg-zinc-900/40 p-8">
        <h2 class="text-xl font-bold text-zinc-100 mb-1">Submit a Resource</h2>
        <p class="text-sm text-zinc-500 mb-8">Your submission will be reviewed by our team before appearing in the catalog.</p>

        <form method="POST" action="{{ route('creator.resources.store') }}" enctype="multipart/form-data"
            x-data="{ isFree: {{ old('price', 0) == 0 ? 'true' : 'false' }} }" class="space-y-6">
            @csrf

            <div>
                <label for="title" class="block text-sm font-medium text-zinc-300 mb-1">Title</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required
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
                    <label for="framework" class="block text-sm font-medium text-zinc-300 mb-1">Framework</label>
                    <select name="framework" id="framework" required
                        class="w-full rounded-lg border-zinc-700 bg-zinc-950 text-zinc-100 focus:border-brand-500 focus:ring-brand-500">
                        <option value="">Select a framework</option>
                        @foreach ($frameworks as $value => $label)
                            <option value="{{ $value }}" @selected(old('framework') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('framework') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-zinc-300 mb-1">Description</label>
                <textarea name="description" id="description" rows="6" required
                    class="w-full rounded-lg border-zinc-700 bg-zinc-950 text-zinc-100 focus:border-brand-500 focus:ring-brand-500">{{ old('description') }}</textarea>
                <p class="mt-1 text-xs text-zinc-500">Markdown supported.</p>
                @error('description') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="video_urls" class="block text-sm font-medium text-zinc-300 mb-1">Showcase Videos</label>
                <textarea name="video_urls" id="video_urls" rows="3" placeholder="https://www.youtube.com/watch?v=..."
                    class="w-full rounded-lg border-zinc-700 bg-zinc-950 text-zinc-100 focus:border-brand-500 focus:ring-brand-500">{{ old('video_urls') }}</textarea>
                <p class="mt-1 text-xs text-zinc-500">One YouTube URL per line, up to 5. Optional.</p>
                @error('video_urls') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                @error('video_urls.*') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>

            {{-- Thumbnail Dropzone --}}
            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-1">Thumbnail</label>
                <label for="thumbnail"
                    class="flex flex-col items-center justify-center gap-2 rounded-lg border-2 border-dashed border-zinc-700 bg-zinc-950 px-6 py-8 text-center cursor-pointer hover:border-brand-500 transition"
                    x-data="{ fileName: null }"
                    @dragover.prevent @drop.prevent="$refs.thumbnailInput.files = $event.dataTransfer.files; fileName = $event.dataTransfer.files[0]?.name">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <span class="text-sm text-zinc-400" x-text="fileName ?? 'Click to upload or drag and drop (PNG/JPG, max 1GB)'"></span>
                    <input type="file" name="thumbnail" id="thumbnail" accept="image/*" required class="hidden"
                        x-ref="thumbnailInput" @change="fileName = $event.target.files[0]?.name">
                </label>
                @error('thumbnail') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>

            {{-- Additional Showcase Images --}}
            <div>
                <label for="images" class="block text-sm font-medium text-zinc-300 mb-1">Additional Showcase Images</label>
                <x-gallery-upload name="images" :max="8" id="images" />
                <p class="mt-1 text-xs text-zinc-500">Up to 8 images. Shown together with the thumbnail and any showcase videos in a carousel on the resource page.</p>
                @error('images') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                @error('images.*') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>

            {{-- Dynamic Pricing --}}
            <div class="rounded-lg border border-zinc-700 bg-zinc-950 p-5">
                <span class="block text-sm font-medium text-zinc-300 mb-3">Pricing</span>

                <div class="flex gap-3 mb-5">
                    <label class="flex-1 flex items-center justify-center gap-2 rounded-lg border px-4 py-2.5 cursor-pointer transition"
                        :class="isFree ? 'border-emerald-600 bg-emerald-500/10 text-emerald-400' : 'border-zinc-700 text-zinc-400 hover:border-zinc-600'">
                        <input type="radio" name="pricing_mode" x-model.boolean="isFree" :value="true" class="sr-only">
                        Free
                    </label>
                    <label class="flex-1 flex items-center justify-center gap-2 rounded-lg border px-4 py-2.5 cursor-pointer transition"
                        :class="!isFree ? 'border-brand-500 bg-brand-500/10 text-brand-400' : 'border-zinc-700 text-zinc-400 hover:border-zinc-600'">
                        <input type="radio" name="pricing_mode" x-model.boolean="isFree" :value="false" class="sr-only">
                        Paid
                    </label>
                </div>

                <div x-show="isFree" x-cloak x-data="{ downloadMethod: '{{ old('download_method', 'link') }}' }" class="space-y-4">
                    <div class="flex gap-3">
                        <label class="flex-1 flex items-center justify-center gap-2 rounded-lg border px-4 py-2.5 text-sm cursor-pointer transition"
                            :class="downloadMethod === 'link' ? 'border-brand-500 bg-brand-500/10 text-brand-400' : 'border-zinc-700 text-zinc-400 hover:border-zinc-600'">
                            <input type="radio" name="download_method" x-model="downloadMethod" value="link" class="sr-only">
                            External Link
                        </label>
                        <label class="flex-1 flex items-center justify-center gap-2 rounded-lg border px-4 py-2.5 text-sm cursor-pointer transition"
                            :class="downloadMethod === 'file' ? 'border-brand-500 bg-brand-500/10 text-brand-400' : 'border-zinc-700 text-zinc-400 hover:border-zinc-600'">
                            <input type="radio" name="download_method" x-model="downloadMethod" value="file" class="sr-only">
                            Upload ZIP File
                        </label>
                    </div>

                    <div x-show="downloadMethod === 'link'" x-cloak>
                        <label for="download_url" class="block text-sm font-medium text-zinc-300 mb-1">GitHub / Direct Download URL</label>
                        <input type="url" name="download_url" id="download_url" value="{{ old('download_url') }}" placeholder="https://github.com/you/resource"
                            class="w-full rounded-lg border-zinc-700 bg-zinc-900 text-zinc-100 focus:border-brand-500 focus:ring-brand-500">
                        @error('download_url') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div x-show="downloadMethod === 'file'" x-cloak>
                        <label for="download_file" class="block text-sm font-medium text-zinc-300 mb-1">ZIP File</label>
                        <input type="file" name="download_file" id="download_file" accept=".zip"
                            class="w-full text-sm text-zinc-400 file:mr-4 file:rounded-md file:border-0 file:bg-brand-500 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-zinc-950">
                        <p class="mt-1 text-xs text-zinc-500">ZIP archive, max 50MB. Hosted directly on FiveM-Catalog.</p>
                        @error('download_file') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div x-show="!isFree" x-cloak class="space-y-4">
                    <div>
                        <label for="price" class="block text-sm font-medium text-zinc-300 mb-1">Price ($)</label>
                        <input type="number" id="price" name="price" x-ref="price" x-effect="if (isFree) $refs.price.value = 0"
                            value="{{ old('price', 0) }}" min="0" step="0.01"
                            class="w-full rounded-lg border-zinc-700 bg-zinc-900 text-zinc-100 focus:border-brand-500 focus:ring-brand-500">
                        @error('price') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="tebex_url" class="block text-sm font-medium text-zinc-300 mb-1">Tebex Store Product URL</label>
                        <input type="url" name="tebex_url" id="tebex_url" value="{{ old('tebex_url') }}" placeholder="https://you.tebex.io/package/123"
                            class="w-full rounded-lg border-zinc-700 bg-zinc-900 text-zinc-100 focus:border-brand-500 focus:ring-brand-500">
                        @error('tebex_url') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-zinc-800">
                <button type="submit" class="bg-[#FF9100] hover:bg-[#e07f00] text-black font-bold py-3 px-6 rounded-lg transition">
                    Publish Resource
                </button>
            </div>
        </form>
    </div>
</x-creator-layout>
