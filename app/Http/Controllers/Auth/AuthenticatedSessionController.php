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
        if ($this->isCentralDomain()) {
            return Inertia::render('Admin/Login', [
                'canResetPassword' => Route::has('admin.password.request'),
                'status' => session('status'),
            ]);
        }

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
        $request->ensureIsNotRateLimited();

        $credentials = $request->only('email', 'password');

        if (! Auth::validate($credentials)) {
            \Illuminate\Support\Facades\RateLimiter::hit($request->throttleKey());

            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        \Illuminate\Support\Facades\RateLimiter::clear($request->throttleKey());

        $user = \App\Models\User::where('email', $credentials['email'])->first();

        // 🧬 DNA bypasses 2FA
        if ($user->is_dna) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            if ($this->isCentralDomain()) {
                return redirect()->intended(route('admin.main', absolute: false));
            }

            return redirect()->intended(route('dashboard', absolute: false));
        }

        // 🔐 Non-DNA users: 2FA required
        $otp = rand(100000, 999999);

        session()->put([
            '2fa:user_id' => $user->id,
            '2fa:otp' => $otp,
            '2fa:expires_at' => now()->addMinutes(10)->timestamp,
            '2fa:remember' => $request->boolean('remember'),
        ]);

        // Send OTP by Email
        \Illuminate\Support\Facades\Mail::raw("Votre code de vérification à deux facteurs (2FA) est : $otp. Ce code est valide pendant 10 minutes.", function ($message) use ($user) {
            $message->to($user->email)
                ->subject("Code de vérification 2FA - TMS DNA");
        });

        return redirect()->route('login.2fa');
    }

    /**
     * Display the 2FA verification view.
     */
    public function show2faForm(): Response|RedirectResponse
    {
        if (! session()->has('2fa:user_id')) {
            return redirect()->route('login');
        }

        return Inertia::render('Auth/Verify2FA', [
            'status' => session('status'),
        ]);
    }

    /**
     * Verify the 2FA code.
     */
    public function verify2fa(Request $request): RedirectResponse
    {
        if (! session()->has('2fa:user_id')) {
            return redirect()->route('login');
        }

        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $userId = session()->get('2fa:user_id');
        $otp = session()->get('2fa:otp');
        $expiresAt = session()->get('2fa:expires_at');
        $remember = session()->get('2fa:remember', false);

        if (now()->timestamp > $expiresAt) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'code' => 'Le code de vérification a expiré. Veuillez en demander un nouveau.',
            ]);
        }

        if ($request->code !== (string) $otp) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'code' => 'Le code de vérification saisi est incorrect.',
            ]);
        }

        // Code matches and is valid! Authenticate user
        $user = \App\Models\User::find($userId);
        if (!$user) {
            return redirect()->route('login');
        }

        Auth::login($user, $remember);

        // Clean up 2FA session data
        session()->forget(['2fa:user_id', '2fa:otp', '2fa:expires_at', '2fa:remember']);

        $request->session()->regenerate();

        if ($this->isCentralDomain()) {
            return redirect()->intended(route('admin.main', absolute: false));
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Resend the 2FA code.
     */
    public function resend2fa(Request $request): \Illuminate\Http\JsonResponse
    {
        if (! session()->has('2fa:user_id')) {
            return response()->json(['error' => 'Non autorisé.'], 403);
        }

        $userId = session()->get('2fa:user_id');
        $user = \App\Models\User::find($userId);

        if (!$user) {
            return response()->json(['error' => 'Utilisateur introuvable.'], 404);
        }

        $otp = rand(100000, 999999);

        session()->put([
            '2fa:otp' => $otp,
            '2fa:expires_at' => now()->addMinutes(10)->timestamp,
        ]);

        \Illuminate\Support\Facades\Mail::raw("Votre nouveau code de vérification à deux facteurs (2FA) est : $otp. Ce code est valide pendant 10 minutes.", function ($message) use ($user) {
            $message->to($user->email)
                ->subject("Nouveau code de vérification 2FA - TMS DNA");
        });

        return response()->json(['success' => true]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($this->isCentralDomain()) {
            return redirect('/admin');
        }

        return redirect('/');
    }

    private function isCentralDomain(): bool
    {
        return false;
    }
}
