@props(['name' => 'images', 'max' => 8])

<div x-data="{
        files: [],
        max: {{ $max }},
        addFiles(fileList) {
            for (const file of fileList) {
                if (this.files.length >= this.max) break;
                this.files.push({ file, url: URL.createObjectURL(file) });
            }
            this.syncInput();
        },
        removeFile(index) {
            URL.revokeObjectURL(this.files[index].url);
            this.files.splice(index, 1);
            this.syncInput();
        },
        syncInput() {
            const dt = new DataTransfer();
            this.files.forEach(item => dt.items.add(item.file));
            this.$refs.input.files = dt.files;
        },
    }">
    <div class="flex flex-wrap gap-3 mb-3" x-show="files.length > 0" x-cloak>
        <template x-for="(item, index) in files" :key="index">
            <div class="relative h-16 w-16 shrink-0">
                <img :src="item.url" alt="" class="h-16 w-16 rounded-lg object-cover border border-zinc-700">
                <button type="button" @click="removeFile(index)"
                    class="absolute -top-1.5 -right-1.5 flex items-center justify-center h-5 w-5 rounded-full bg-red-600 text-white hover:bg-red-500 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </template>
    </div>

    {{-- The native file input's own "Choose Files" button is rendered by the
         browser in the OS/browser's language, which we can't control or
         translate via CSS. So the input stays hidden and this custom label
         is the only visible, fully English trigger. --}}
    <label for="{{ $attributes->get('id', $name) }}"
        class="flex flex-col items-center justify-center gap-2 rounded-lg border-2 border-dashed border-zinc-700 bg-zinc-950 px-6 py-6 text-center cursor-pointer hover:border-brand-500 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
        </svg>
        <span class="text-sm text-zinc-400">
            <template x-if="files.length === 0">
                <span>Click to select images</span>
            </template>
            <template x-if="files.length > 0">
                <span><span x-text="files.length"></span>/<span x-text="max"></span> selected &mdash; click to add more</span>
            </template>
        </span>
        <input type="file" name="{{ $name }}[]" {{ $attributes->merge(['class' => 'hidden']) }}
            accept="image/*" multiple x-ref="input" @change="addFiles($event.target.files)">
    </label>
</div>
