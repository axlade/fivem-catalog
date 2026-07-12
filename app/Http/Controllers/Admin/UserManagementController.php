<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    /**
     * List all users with search and role filtering.
     */
    public function index(Request $request): View
    {
        $users = User::query()
            ->withCount('resources')
            ->when($request->string('role')->toString(), fn ($query, $role) => $query->where('role', $role))
            ->when($request->string('status')->toString(), function ($query, $status) {
                match ($status) {
                    'banned' => $query->whereNotNull('banned_at')
                        ->where(fn ($q) => $q->whereNull('banned_until')->orWhere('banned_until', '>', now())),
                    'active' => $query->where(fn ($q) => $q->whereNull('banned_at')
                        ->orWhere(fn ($q2) => $q2->whereNotNull('banned_until')->where('banned_until', '<=', now()))),
                    default => $query,
                };
            })
            ->when($request->string('q')->toString(), function ($query, $term) {
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('username', 'like', "%{$term}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
        ]);
    }

    /**
     * Change a user's role (user / creator / admin).
     */
    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in(['user', 'creator', 'admin'])],
        ]);

        $user->update(['role' => $validated['role']]);

        return back()->with('status', "{$user->name}'s role is now {$validated['role']}.");
    }

    /**
     * Delete a user account. Admins cannot delete their own account here.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_if($request->user()->id === $user->id, 403, "You can't delete your own account from here.");

        $user->delete();

        return back()->with('status', 'User account deleted.');
    }

    /**
     * Ban a user, permanently or for a set number of days. Always blocks
     * login and force-logs-out any active session; separately, the admin
     * chooses whether to also hide the user's resources/services from the
     * public site (an account can be locked out without unpublishing its work).
     */
    public function ban(Request $request, User $user): RedirectResponse
    {
        abort_if($request->user()->id === $user->id, 403, "You can't ban your own account.");

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
            'duration' => ['nullable', 'string', Rule::in(['permanent', '1', '3', '7', '30'])],
        ]);

        $duration = $validated['duration'] ?? 'permanent';

        $user->forceFill([
            'banned_at' => now(),
            'banned_until' => $duration === 'permanent' ? null : now()->addDays((int) $duration),
            'ban_reason' => $validated['reason'] ?? null,
            'hide_content' => $request->boolean('hide_content'),
        ])->save();

        $message = $duration === 'permanent'
            ? "{$user->name} has been permanently banned."
            : "{$user->name} has been banned for {$duration} day(s).";

        return back()->with('status', $message);
    }

    /**
     * Lift a ban and restore normal access.
     */
    public function unban(User $user): RedirectResponse
    {
        $user->forceFill([
            'banned_at' => null,
            'banned_until' => null,
            'ban_reason' => null,
            'hide_content' => false,
        ])->save();

        return back()->with('status', "{$user->name} has been unbanned.");
    }
}
