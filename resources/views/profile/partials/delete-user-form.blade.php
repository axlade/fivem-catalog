<section class="space-y-4">
    <header>
        <h2 class="text-lg font-bold text-zinc-100">Delete Account</h2>
        <p class="mt-1 text-sm text-zinc-500">
            Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.
        </p>
    </header>

    <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="rounded-lg bg-red-600 hover:bg-red-500 text-white font-semibold py-2.5 px-6 transition-colors">
        Delete Account
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-zinc-100">Are you sure you want to delete your account?</h2>

            <p class="mt-1 text-sm text-zinc-400">
                Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.
            </p>

            <div class="mt-6">
                <label for="password" class="sr-only">Password</label>
                <input id="password" name="password" type="password" placeholder="Password"
                    class="w-3/4 rounded-lg border-zinc-700 bg-zinc-950 text-zinc-100 focus:border-brand-500 focus:ring-brand-500">
                @error('password', 'userDeletion') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')"
                    class="rounded-lg border border-zinc-700 bg-zinc-900 px-4 py-2 text-sm font-semibold text-zinc-200 hover:bg-zinc-800 transition">
                    Cancel
                </button>
                <button type="submit" class="rounded-lg bg-red-600 hover:bg-red-500 text-white font-semibold px-4 py-2 transition-colors">
                    Delete Account
                </button>
            </div>
        </form>
    </x-modal>
</section>
