<x-creator-layout title="My Freelance Services">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-semibold text-zinc-100">My Freelance Services</h2>
        <a href="{{ route('creator.services.create') }}" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-semibold text-zinc-950 hover:bg-brand-400 transition">
            Post a Service
        </a>
    </div>

    <div class="rounded-xl border border-zinc-900 bg-zinc-900/40 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-zinc-900 text-left text-xs uppercase tracking-wider text-zinc-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Title</th>
                    <th class="px-4 py-3 font-medium">Category</th>
                    <th class="px-4 py-3 font-medium">Starting Price</th>
                    <th class="px-4 py-3 font-medium">Verified</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800">
                @forelse ($services as $service)
                    <tr>
                        <td class="px-4 py-3 font-medium text-zinc-100">{{ $service->title }}</td>
                        <td class="px-4 py-3 text-zinc-400 capitalize">{{ $service->category }}</td>
                        <td class="px-4 py-3 text-zinc-100">${{ number_format($service->starting_price, 2) }}</td>
                        <td class="px-4 py-3">
                            @if ($service->is_verified)
                                <span class="rounded-full bg-brand-500/10 px-2.5 py-1 text-xs font-semibold text-brand-400">Verified</span>
                            @else
                                <span class="text-zinc-500">&mdash;</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if ($service->is_active)
                                <span class="rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-400">Active</span>
                            @else
                                <span class="rounded-full bg-zinc-700/40 px-2.5 py-1 text-xs font-semibold text-zinc-400">Inactive</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-16 text-center text-zinc-500">
                            You haven't posted any freelance services yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-creator-layout>
