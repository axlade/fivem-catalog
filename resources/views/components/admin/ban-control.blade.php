@props(['user'])

@if ($user->id !== auth()->id())
    @if ($user->isBanned())
        <form method="POST" action="{{ route('admin.users.unban', $user) }}" class="inline">
            @csrf
            <button type="submit" class="text-sm font-medium text-zinc-400 hover:text-emerald-400 transition">
                Unban User
            </button>
        </form>
    @else
        <div x-data="{ open: false }" class="inline-block">
            <button @click="open = true" type="button" class="text-sm font-medium text-zinc-400 hover:text-red-400 transition">
                Ban User
            </button>

            {{-- Teleported to <body> so this modal is never clipped by a
                 table's or card's overflow-hidden ancestor. --}}
            <template x-teleport="body">
                <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="open = false">
                    <div x-show="open" x-transition.opacity @click="open = false" class="absolute inset-0 bg-zinc-950/70 backdrop-blur-sm"></div>

                    <div x-show="open" x-transition class="relative w-full max-w-sm rounded-xl border border-zinc-800 bg-zinc-900 p-5 shadow-xl">
                        <h3 class="text-sm font-semibold text-zinc-100 mb-4">Ban {{ $user->name }}</h3>

                        <form method="POST" action="{{ route('admin.users.ban', $user) }}" class="space-y-3">
                            @csrf

                            <div>
                                <label class="block text-xs font-medium text-zinc-400 mb-1">Duration</label>
                                <select name="duration"
                                    class="w-full rounded-lg border-zinc-700 bg-zinc-950 text-sm text-zinc-100 focus:border-brand-500 focus:ring-brand-500">
                                    <option value="permanent">Permanent</option>
                                    <option value="1">1 day</option>
                                    <option value="3">3 days</option>
                                    <option value="7">7 days</option>
                                    <option value="30">30 days</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-zinc-400 mb-1">Reason (optional)</label>
                                <input type="text" name="reason" maxlength="255"
                                    class="w-full rounded-lg border-zinc-700 bg-zinc-950 text-sm text-zinc-100 focus:border-brand-500 focus:ring-brand-500">
                            </div>

                            <label class="flex items-start gap-2 text-xs text-zinc-400 cursor-pointer">
                                <input type="checkbox" name="hide_content" value="1" checked
                                    class="mt-0.5 rounded border-zinc-700 bg-zinc-950 text-brand-500 focus:ring-brand-500">
                                <span>Hide their resources &amp; services from the public site</span>
                            </label>

                            <div class="flex gap-2 pt-2">
                                <button type="button" @click="open = false"
                                    class="flex-1 rounded-lg border border-zinc-700 px-3 py-2 text-sm font-medium text-zinc-300 hover:bg-zinc-800 transition">
                                    Cancel
                                </button>
                                <button type="submit" class="flex-1 rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-500 transition">
                                    Confirm Ban
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>
        </div>
    @endif
@endif
