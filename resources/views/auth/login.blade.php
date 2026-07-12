<x-guest-layout>
    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-emerald-400">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-zinc-400 mb-1">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                class="w-full rounded-lg bg-zinc-950 border border-zinc-800 text-white px-3 py-2 focus:outline-none focus:ring-1 focus:border-[#FF9100] focus:ring-[#FF9100]">
            @error('email')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mt-4">
            <label for="password" class="block text-sm font-medium text-zinc-400 mb-1">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="w-full rounded-lg bg-zinc-950 border border-zinc-800 text-white px-3 py-2 focus:outline-none focus:ring-1 focus:border-[#FF9100] focus:ring-[#FF9100]">
            @error('password')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" name="remember"
                    class="rounded border-zinc-800 bg-zinc-950 text-[#FF9100] focus:ring-[#FF9100] focus:ring-offset-zinc-950">
                <span class="ms-2 text-sm text-zinc-400">Remember me</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-6">
            @if (Route::has('password.request'))
                <a class="text-sm text-zinc-400 hover:text-[#FF9100] transition-colors" href="{{ route('password.request') }}">
                    Forgot your password?
                </a>
            @endif

            <button type="submit"
                class="bg-[#FF9100] hover:bg-[#e07f00] text-black font-bold uppercase tracking-wider py-2 px-4 rounded-lg transition-colors">
                Log in
            </button>
        </div>
    </form>
</x-guest-layout>
