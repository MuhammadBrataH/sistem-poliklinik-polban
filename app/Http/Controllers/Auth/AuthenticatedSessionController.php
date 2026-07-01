<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $role = $request->user()->role;
        $intendedUrl = $request->session()->pull('url.intended');

        if ($role === 'pasien') {
            // Jika ada intended URL dan itu mengarah ke halaman pasien, redirect ke sana
            if ($intendedUrl && \Illuminate\Support\Str::contains($intendedUrl, '/pasien')) {
                return redirect()->to($intendedUrl);
            }
            return redirect()->route('pasien.dashboard');
        }

        // Untuk Admin / Dokter
        // Jika ada intended URL dan itu mengarah ke halaman admin, redirect ke sana
        if ($intendedUrl && \Illuminate\Support\Str::contains($intendedUrl, '/admin')) {
            return redirect()->to($intendedUrl);
        }
        return redirect()->route('admin.dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
