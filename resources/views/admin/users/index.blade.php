<x-creator-layout title="User Management">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-zinc-100">User Management</h2>
            <p class="text-sm text-zinc-500">{{ $users->total() }} registered users.</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap gap-3 mb-6">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name, email, username..."
            class="flex-1 min-w-[220px] rounded-lg border-zinc-700 bg-zinc-950 text-sm text-zinc-100 focus:border-brand-500 focus:ring-brand-500">
        <select name="role" onchange="this.form.submit()"
            class="rounded-lg border-zinc-700 bg-zinc-950 text-sm text-zinc-100 focus:border-brand-500 focus:ring-brand-500">
            <option value="">All roles</option>
            <option value="user" @selected(request('role') === 'user')>User</option>
            <option value="creator" @selected(request('role') === 'creator')>Creator</option>
            <option value="admin" @selected(request('role') === 'admin')>Admin</option>
        </select>
        <select name="status" onchange="this.form.submit()"
            class="rounded-lg border-zinc-700 bg-zinc-950 text-sm text-zinc-100 focus:border-brand-500 focus:ring-brand-500">
            <option value="">All statuses</option>
            <option value="active" @selected(request('status') === 'active')>Active</option>
            <option value="banned" @selected(request('status') === 'banned')>Banned</option>
        </select>
        <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-semibold text-zinc-950 hover:bg-brand-400 transition">
            Search
        </button>
    </form>

    <div class="rounded-xl border border-zinc-900 bg-zinc-900/40 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-zinc-900 text-left text-xs uppercase tracking-wider text-zinc-500">
                <tr>
                    <th class="px-4 py-3 font-medium">User</th>
                    <th class="px-4 py-3 font-medium">Role</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium">Resources</th>
                    <th class="px-4 py-3 font-medium">Joined</th>
                    <th class="px-4 py-3 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800">
                @forelse ($users as $user)
                    <tr>
                        <td class="px-4 py-3">
                            <p class="font-medium text-zinc-100">{{ $user->name }}</p>
                            <p class="text-xs text-zinc-500">{{ $user->email }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('admin.users.updateRole', $user) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <select name="role" onchange="this.form.submit()" {{ $user->id === auth()->id() ? 'disabled' : '' }}
                                    class="rounded-lg border-zinc-700 bg-zinc-950 text-xs text-zinc-100 focus:border-brand-500 focus:ring-brand-500 disabled:opacity-50">
                                    <option value="user" @selected($user->role === 'user')>User</option>
                                    <option value="creator" @selected($user->role === 'creator')>Creator</option>
                                    <option value="admin" @selected($user->role === 'admin')>Admin</option>
                                </select>
                            </form>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap items-center gap-1.5">
                                @if ($user->isBanned())
                                    <span class="rounded-full bg-red-500/10 px-2.5 py-1 text-xs font-semibold text-red-400"
                                        title="{{ $user->ban_reason }}">
                                        Banned{{ $user->banned_until ? ' until '.$user->banned_until->format('M j, Y') : ' (permanent)' }}
                                    </span>
                                    @if ($user->hide_content)
                                        <span class="rounded-full bg-zinc-800 px-2 py-1 text-[10px] font-semibold uppercase tracking-wide text-zinc-400">Content hidden</span>
                                    @endif
                                @endif
                                @if ($user->is_verified)
                                    <span class="rounded-full bg-brand-500/10 px-2.5 py-1 text-xs font-semibold text-brand-400">Verified</span>
                                @endif
                                @if (! $user->isBanned() && ! $user->is_verified)
                                    <span class="text-zinc-600">&mdash;</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-zinc-400">{{ $user->resources_count }}</td>
                        <td class="px-4 py-3 text-zinc-400">{{ $user->created_at->format('M j, Y') }}</td>
                        <td class="px-4 py-3 text-right space-x-3 whitespace-nowrap">
                            <x-admin.ban-control :user="$user" />
                            @unless ($user->id === auth()->id())
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Delete this user account? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-medium text-zinc-400 hover:text-red-400 transition">Delete</button>
                                </form>
                            @endunless
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-16 text-center text-zinc-500">No users match your search.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $users->links() }}
    </div>
</x-creator-layout>
