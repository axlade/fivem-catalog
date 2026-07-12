<x-creator-layout title="My Profile">
    <div class="max-w-3xl mx-auto space-y-8">
        @if (session('status') === 'profile-updated')
            <div class="rounded-lg border border-emerald-800 bg-emerald-950/50 px-4 py-3 text-sm text-emerald-300">
                Profile updated.
            </div>
        @endif

        <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('patch')

            {{-- Profile Branding --}}
            <div class="rounded-xl border border-zinc-900 bg-zinc-900/40 p-8">
                <h2 class="text-lg font-bold text-zinc-100 mb-1">Profile Branding</h2>
                <p class="text-sm text-zinc-500 mb-6">This is how you'll appear across the catalog and on your public profile.</p>

                <div class="flex items-center gap-6" x-data="{ preview: null }">
                    <div class="h-20 w-20 shrink-0 rounded-full bg-zinc-800 flex items-center justify-center text-2xl font-bold text-brand-400 overflow-hidden">
                        <template x-if="preview">
                            <img :src="preview" alt="Avatar preview" class="h-full w-full object-cover">
                        </template>
                        <template x-if="!preview">
                            <span>
                                @if ($user->avatar_path)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($user->avatar_path) }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                                @else
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                @endif
                            </span>
                        </template>
                    </div>

                    <div class="flex-1">
                        <label for="avatar" class="block text-sm font-medium text-zinc-300 mb-1">Avatar</label>
                        <input type="file" id="avatar" name="avatar" accept="image/*"
                            @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
                            class="w-full text-sm text-zinc-400 file:mr-4 file:rounded-md file:border-0 file:bg-brand-500 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-zinc-950">
                        <p class="mt-1 text-xs text-zinc-500">PNG or JPG, max 2MB.</p>
                        @error('avatar') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Account Information --}}
            <div class="rounded-xl border border-zinc-900 bg-zinc-900/40 p-8">
                <h2 class="text-lg font-bold text-zinc-100 mb-1">Account Information</h2>
                <p class="text-sm text-zinc-500 mb-6">Update your account's name and email address.</p>

                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-zinc-300 mb-1">Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autocomplete="name"
                            class="w-full rounded-lg border-zinc-700 bg-zinc-950 text-zinc-100 focus:border-brand-500 focus:ring-brand-500">
                        @error('name') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-zinc-300 mb-1">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                            class="w-full rounded-lg border-zinc-700 bg-zinc-950 text-zinc-100 focus:border-brand-500 focus:ring-brand-500">
                        @error('email') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror

                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <div class="mt-2">
                                <p class="text-sm text-zinc-400">
                                    Your email address is unverified.
                                    <button form="send-verification" class="underline text-zinc-300 hover:text-brand-400 transition-colors">
                                        Click here to re-send the verification email.
                                    </button>
                                </p>

                                @if (session('status') === 'verification-link-sent')
                                    <p class="mt-2 font-medium text-sm text-emerald-400">
                                        A new verification link has been sent to your email address.
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- About Me --}}
            <div class="rounded-xl border border-zinc-900 bg-zinc-900/40 p-8">
                <h2 class="text-lg font-bold text-zinc-100 mb-1">About Me</h2>
                <p class="text-sm text-zinc-500 mb-6">A short description shown on your public profile.</p>

                <label for="bio" class="block text-sm font-medium text-zinc-300 mb-1">Bio</label>
                <textarea name="bio" id="bio" rows="5" maxlength="1000"
                    class="w-full rounded-lg border-zinc-700 bg-zinc-950 text-zinc-100 focus:border-brand-500 focus:ring-brand-500">{{ old('bio', $user->bio) }}</textarea>
                <p class="mt-1 text-xs text-zinc-500">Max 1000 characters.</p>
                @error('bio') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>

            {{-- Stores & Social Links --}}
            <div class="rounded-xl border border-zinc-900 bg-zinc-900/40 p-8">
                <h2 class="text-lg font-bold text-zinc-100 mb-1">Stores &amp; Social Links</h2>
                <p class="text-sm text-zinc-500 mb-6">Displayed as action buttons on your public profile.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="tebex_url" class="block text-sm font-medium text-zinc-300 mb-1">Tebex Webstore URL</label>
                        <input type="url" name="tebex_url" id="tebex_url" value="{{ old('tebex_url', $user->tebex_url) }}" placeholder="https://you.tebex.io"
                            class="w-full rounded-lg border-zinc-700 bg-zinc-950 text-zinc-100 focus:border-brand-500 focus:ring-brand-500">
                        @error('tebex_url') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="github_url" class="block text-sm font-medium text-zinc-300 mb-1">GitHub Profile URL</label>
                        <input type="url" name="github_url" id="github_url" value="{{ old('github_url', $user->github_url) }}" placeholder="https://github.com/you"
                            class="w-full rounded-lg border-zinc-700 bg-zinc-950 text-zinc-100 focus:border-brand-500 focus:ring-brand-500">
                        @error('github_url') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="discord_invite_url" class="block text-sm font-medium text-zinc-300 mb-1">Discord Invite / Contact</label>
                        <input type="url" name="discord_invite_url" id="discord_invite_url" value="{{ old('discord_invite_url', $user->discord_invite_url) }}" placeholder="https://discord.gg/invite"
                            class="w-full rounded-lg border-zinc-700 bg-zinc-950 text-zinc-100 focus:border-brand-500 focus:ring-brand-500">
                        @error('discord_invite_url') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="youtube_url" class="block text-sm font-medium text-zinc-300 mb-1">YouTube / Twitch Channel</label>
                        <input type="url" name="youtube_url" id="youtube_url" value="{{ old('youtube_url', $user->youtube_url) }}" placeholder="https://youtube.com/@you"
                            class="w-full rounded-lg border-zinc-700 bg-zinc-950 text-zinc-100 focus:border-brand-500 focus:ring-brand-500">
                        @error('youtube_url') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <label for="website_url" class="block text-sm font-medium text-zinc-300 mb-1">Personal Website</label>
                    <input type="url" name="website_url" id="website_url" value="{{ old('website_url', $user->website_url) }}" placeholder="https://yourdomain.com"
                        class="w-full rounded-lg border-zinc-700 bg-zinc-950 text-zinc-100 focus:border-brand-500 focus:ring-brand-500">
                    @error('website_url') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="bg-[#FF9100] hover:bg-[#e07f00] text-black font-bold uppercase py-2.5 px-6 rounded-lg transition-colors">
                    Save Changes
                </button>
            </div>
        </form>

        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>

        {{-- Update Password --}}
        <div class="rounded-xl border border-zinc-900 bg-zinc-900/40 p-8">
            @include('profile.partials.update-password-form')
        </div>

        {{-- Delete Account --}}
        <div class="rounded-xl border border-red-900/40 bg-zinc-900/40 p-8">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-creator-layout>
