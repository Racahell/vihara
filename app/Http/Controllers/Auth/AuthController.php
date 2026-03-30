<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister(Request $request)
    {
        $captchaQuestion = null;
        if ($this->isOfflineCaptchaEnabled()) {
            [$captchaQuestion, $captchaAnswer] = $this->generateOfflineCaptcha();
            $request->session()->put('offline_captcha_answer', $captchaAnswer);
        }

        return view('auth.register', [
            'useRecaptcha' => $this->shouldUseRecaptcha(),
            'recaptchaSiteKey' => (string) config('services.recaptcha.site_key'),
            'useOfflineCaptcha' => $this->isOfflineCaptchaEnabled(),
            'offlineCaptchaQuestion' => $captchaQuestion,
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:32'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $this->validateCaptcha($request);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'username' => Str::before($validated['email'], '@') . '-' . Str::lower(Str::random(4)),
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'is_active' => false,
            'registration_ip' => $request->ip(),
        ]);

        $umatRole = Role::where('slug', 'umat')->first();
        if ($umatRole) {
            $user->roles()->syncWithoutDetaching([$umatRole->id]);
        }

        $verifyUrl = route('verify.email', [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]);

        try {
            Mail::raw(
                "Halo {$user->name},\n\nSilakan aktivasi akun Anda dengan membuka tautan ini:\n{$verifyUrl}\n\nTerima kasih.",
                fn ($message) => $message->to($user->email)->subject('Aktivasi Akun Vihara')
            );
        } catch (\Throwable $e) {
            // Fallback handled by admin activation menu.
        }

        return redirect()->route('login')->with('status', 'Pendaftaran berhasil. Cek email untuk aktivasi akun.');
    }

    public function verifyEmail(Request $request, int $id, string $hash)
    {
        $user = User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->email))) {
            abort(403);
        }

        if (! $user->email_verified_at) {
            $user->forceFill(['email_verified_at' => now()]);
        }

        $user->forceFill([
            'is_active' => true,
            'activated_at' => now(),
        ])->save();

        return redirect()->route('login')->with('status', 'Email berhasil diverifikasi. Silakan login.');
    }

    public function login(Request $request, AuditLogService $auditLogService)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, (bool) $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = $request->user();
            $user->forceFill([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
                'last_login_user_agent' => substr((string) $request->userAgent(), 0, 65535),
            ])->save();

            LoginLog::create([
                'user_id' => $user->id,
                'email' => $credentials['email'],
                'ip_address' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
                'successful' => true,
                'logged_in_at' => now(),
            ]);

            $auditLogService->record($request, 'login', 'Pengguna berhasil login', 'users', $user->id);

            return redirect()->intended(route('dashboard'));
        }

        LoginLog::create([
            'email' => $credentials['email'],
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'successful' => false,
            'logged_in_at' => now(),
        ]);

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request, AuditLogService $auditLogService)
    {
        $user = $request->user();
        if ($user) {
            $auditLogService->record($request, 'logout', 'Pengguna logout', 'users', $user->id);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function validateCaptcha(Request $request): void
    {
        $useRecaptcha = $this->shouldUseRecaptcha();
        $useOfflineCaptcha = $this->isOfflineCaptchaEnabled();
        $captchaMode = (string) $request->input('captcha_mode', 'online');
        $recaptchaToken = trim((string) $request->input('g-recaptcha-response', ''));
        $offlineAnswer = trim((string) $request->input('offline_captcha_answer', ''));
        $offlineExpected = (string) $request->session()->get('offline_captcha_answer', '');

        $recaptchaPassed = false;
        $offlinePassed = false;

        if ($useRecaptcha && $captchaMode !== 'offline' && $recaptchaToken !== '') {
            $recaptchaPassed = $this->verifyRecaptchaToken($recaptchaToken, $request->ip());
        }

        if ($useOfflineCaptcha && $captchaMode === 'offline' && $offlineAnswer !== '' && $offlineExpected !== '') {
            $offlinePassed = hash_equals($offlineExpected, $offlineAnswer);
        }

        $isValid = false;
        if ($useRecaptcha && $captchaMode !== 'offline') {
            $isValid = $recaptchaPassed;
        } elseif ($useOfflineCaptcha) {
            $isValid = $offlinePassed;
        } else {
            $isValid = true;
        }

        if (! $isValid) {
            throw ValidationException::withMessages([
                'captcha' => 'Verifikasi captcha gagal. Coba lagi.',
            ]);
        }

        $request->session()->forget('offline_captcha_answer');
    }

    private function verifyRecaptchaToken(string $token, ?string $ipAddress): bool
    {
        try {
            $response = Http::asForm()
                ->timeout(8)
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => (string) config('services.recaptcha.secret_key'),
                    'response' => $token,
                    'remoteip' => $ipAddress,
                ])
                ->throw()
                ->json();
        } catch (\Throwable) {
            return false;
        }

        return (bool) ($response['success'] ?? false);
    }

    private function shouldUseRecaptcha(): bool
    {
        $enabled = filter_var((string) config('services.recaptcha.enabled', false), FILTER_VALIDATE_BOOLEAN);
        $siteKey = trim((string) config('services.recaptcha.site_key', ''));
        $secretKey = trim((string) config('services.recaptcha.secret_key', ''));

        return $enabled && $siteKey !== '' && $secretKey !== '';
    }

    private function isOfflineCaptchaEnabled(): bool
    {
        return filter_var((string) config('services.recaptcha.offline_enabled', true), FILTER_VALIDATE_BOOLEAN);
    }

    private function generateOfflineCaptcha(): array
    {
        $first = random_int(1, 20);
        $second = random_int(1, 20);

        return ["{$first} + {$second}", (string) ($first + $second)];
    }
}
