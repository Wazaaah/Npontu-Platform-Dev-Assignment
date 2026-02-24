<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Allows authenticated users to manage their own profile.
 *
 * Users can update their display name, phone number, and department.
 * Email and role are read-only from the user's perspective — only admins
 * can change those via the UserController. Password changes require the
 * current password to be verified first.
 *
 * All changes (profile or password) are recorded in the audit log.
 */
class ProfileController extends Controller
{
    /**
     * Show the profile edit form for the authenticated user.
     *
     * @return View
     */
    public function edit(): View
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    /**
     * Apply profile changes for the authenticated user.
     *
     * Always updates name, phone, and department. If current_password is
     * provided, the password change flow is triggered: the current password
     * is verified before the new one is applied. Both profile and password
     * changes produce separate audit log entries for traceability.
     *
     * @param  Request          $request  Validated fields: name, phone, department,
     *                                    and optionally current_password, password, password_confirmation.
     * @return RedirectResponse           Redirects back with a success or error message.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'phone'      => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
        ]);

        $user->update($validated);

        // Password change is optional — only triggered if current_password is present
        if ($request->filled('current_password')) {
            $request->validate([
                'current_password' => 'required',
                'password'         => ['required', 'confirmed', Password::min(8)],
            ]);

            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }

            $user->update(['password' => $request->password]);

            AuditLog::record('profile.password_changed', 'User', $user->id, "{$user->name} changed their password.");
        }

        AuditLog::record('profile.updated', 'User', $user->id, "{$user->name} updated their profile.");

        return back()->with('success', 'Profile updated successfully.');
    }
}
