<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Admin-only CRUD controller for user account management.
 *
 * Provides full lifecycle management for staff and admin accounts — creation,
 * editing, and deactivation. Protected by the 'admin' middleware; only
 * administrators can access these routes.
 *
 * Users are never hard-deleted: setting is_active to false deactivates the
 * account and prevents login while preserving all associated data (activities,
 * incidents, audit logs).
 */
class UserController extends Controller
{
    /**
     * List all users, newest first, paginated.
     *
     * @return View
     */
    public function index(): View
    {
        $users = User::latest()->paginate(15);

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user account.
     *
     * @return View
     */
    public function create(): View
    {
        return view('users.create');
    }

    /**
     * Persist a new user account to the database.
     *
     * The password is hashed via Laravel's Hash facade before storage.
     * New accounts are active by default.
     *
     * @param  Request          $request  Validated fields: name, email, password, role,
     *                                    shift (nullable for admins), phone, department.
     * @return RedirectResponse           Redirects to the user list with a success message.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|min:8|confirmed',
            'role'       => 'required|in:admin,staff',
            'shift'      => 'nullable|in:morning,night',
            'phone'      => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
        ]);

        User::create(array_merge($validated, [
            'password'  => Hash::make($validated['password']),
            'is_active' => true,
        ]));

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show the edit form for an existing user account.
     *
     * @param  User $user Route-model-bound user to edit.
     * @return View
     */
    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Apply validated changes to an existing user account.
     *
     * Password is only updated when explicitly provided in the request.
     * The email uniqueness rule ignores the current user's own record to
     * allow saving without changing the email address.
     *
     * @param  Request          $request  Validated fields: name, email, role, shift, phone,
     *                                    department, is_active, password (optional).
     * @param  User             $user     Route-model-bound user to update.
     * @return RedirectResponse           Redirects to the user list.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'role'       => 'required|in:admin,staff',
            'shift'      => 'nullable|in:morning,night',
            'phone'      => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
            'is_active'  => 'boolean',
        ]);

        // Only hash and update the password if a new one was provided
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Deactivate a user account, preventing future logins.
     *
     * The account is soft-deactivated (is_active → false) rather than
     * deleted, so all historical data tied to this user remains intact.
     *
     * @param  User             $user Route-model-bound user to deactivate.
     * @return RedirectResponse       Redirects to the user list.
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->update(['is_active' => false]);

        return redirect()->route('users.index')
            ->with('success', 'User deactivated successfully.');
    }
}
