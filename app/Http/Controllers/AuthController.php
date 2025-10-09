<?php

    namespace App\Http\Controllers;

    use Illuminate\Http\RedirectResponse;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Log;
    use Illuminate\View\View;

    class AuthController extends Controller
    {
        public function showLogin(): View
        {
            return view('auth.login');
        }

        public function login(Request $request): RedirectResponse
        {

            $credentials = $request->validate([
                'email'    => ['required', 'email'],
                'password' => ['required'],
            ]);

            $remember = $request->boolean('remember');

            // Använd rätt guard och logga utfallet (tillfällig debug)
            Auth::shouldUse('web');
            $ok = Auth::guard('web')->attempt($credentials, $remember);
            Log::info('LOGIN_ATTEMPT', ['ok' => $ok, 'email' => $credentials['email']]);

            if ($ok) {
                $request->session()->regenerate();                // viktigt!
                $request->session()->put('just_logged_in', now()); // debugmarkör
                // Redirecta till root på samma host (undvik fel domän)
                return redirect()->intended('/')->with('success', 'Welcome back!');
            }

            return back()
                ->withErrors(['email' => 'The provided credentials do not match our records.'])
                ->onlyInput('email');
        }

        public function logout(Request $request): RedirectResponse
        {
            Auth::shouldUse('web');
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('success', 'You have been logged out.');
        }
    }
