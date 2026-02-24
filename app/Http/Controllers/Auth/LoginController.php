<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Handles user authentication — login and logout.
 *
 * Wraps Laravel's Auth::attempt() with additional checks for account
 * activation and writes an audit log entry on every successful login
 * and logout event.
 */
class LoginController extends Controller
{
    /**
     * Display the login form.
     *
     * @return View
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Authenticate the user with the provided credentials.
     *
     * Validates email and password, attempts authentication via Laravel's
     * Auth guard, then verifies the account is active before granting access.
     * A successful login is recorded in the audit log and the session is
     * regenerated to prevent session fixation attacks.
     *
     * @param  Request           $request
     * @return RedirectResponse  Redirects to the intended route on success,
     *                           or back with errors on failure.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Reject deactivated accounts even if credentials are valid
            if (!Auth::user()->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account has been deactivated. Contact your administrator.']);
            }

            AuditLog::record('login', 'User', Auth::id(), Auth::user()->name . ' logged in.');

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Log the authenticated user out and invalidate their session.
     *
     * Records a logout audit entry before destroying the session, then
     * regenerates the CSRF token to invalidate any outstanding forms.
     *
     * @param  Request          $request
     * @return RedirectResponse Redirects to the login page.
     */
    public function logout(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            AuditLog::record('logout', 'User', Auth::id(), Auth::user()->name . ' logged out.');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
