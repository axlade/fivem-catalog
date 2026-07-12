<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-zinc-400 mb-1">Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                class="w-full rounded-lg bg-zinc-950 border border-zinc-800 text-white px-3 py-2 focus:outline-none focus:ring-1 focus:border-[#FF9100] focus:ring-[#FF9100]">
            @error('name')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Username -->
        <div class="mt-4">
            <label for="username" class="block text-sm font-medium text-zinc-400 mb-1">Username</label>
            <input id="username" type="text" name="username" value="{{ old('username') }}" required autocomplete="username"
                class="w-full rounded-lg bg-zinc-950 border border-zinc-800 text-white px-3 py-2 focus:outline-none focus:ring-1 focus:border-[#FF9100] focus:ring-[#FF9100]">
            @error('username')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <label for="email" class="block text-sm font-medium text-zinc-400 mb-1">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                class="w-full rounded-lg bg-zinc-950 border border-zinc-800 text-white px-3 py-2 focus:outline-none focus:ring-1 focus:border-[#FF9100] focus:ring-[#FF9100]">
            @error('email')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mt-4">
            <label for="password" class="block text-sm font-medium text-zinc-400 mb-1">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                class="w-full rounded-lg bg-zinc-950 border border-zinc-800 text-white px-3 py-2 focus:outline-none focus:ring-1 focus:border-[#FF9100] focus:ring-[#FF9100]">
            @error('password')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <label for="password_confirmation" class="block text-sm font-medium text-zinc-400 mb-1">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                class="w-full rounded-lg bg-zinc-950 border border-zinc-800 text-white px-3 py-2 focus:outline-none focus:ring-1 focus:border-[#FF9100] focus:ring-[#FF9100]">
            @error('password_confirmation')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Terms of Service -->
        <div class="mt-4">
            <label class="flex items-start gap-2">
                <input type="checkbox" name="terms" value="1" required
                    class="mt-0.5 rounded border-zinc-800 bg-zinc-950 text-[#FF9100] focus:ring-[#FF9100] focus:ring-offset-zinc-950">
                <span class="text-sm text-zinc-400">
                    I agree to the
                    <a href="{{ route('legal.tos') }}" target="_blank" rel="noopener" class="underline hover:text-[#FF9100] transition-colors">Terms of Service</a>
                </span>
            </label>
            @error('terms')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between mt-6">
            <a class="text-sm text-zinc-400 hover:text-[#FF9100] transition-colors" href="{{ route('login') }}">
                Already registered?
            </a>

            <button type="submit"
                class="bg-[#FF9100] hover:bg-[#e07f00] text-black font-bold uppercase tracking-wider py-2 px-4 rounded-lg transition-colors">
                Register
            </button>
        </div>
    </form>
</x-guest-layout>
