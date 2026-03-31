<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        return view('shared.profile', [
            'user' => auth()->user(),
            'waResetUrl' => session('wa_reset_url'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:32'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'profile_photo_cropped' => ['nullable', 'string', 'max:9000000'],
        ]);

        unset($data['profile_photo'], $data['profile_photo_cropped']);

        $newPath = $this->storeProfilePhoto($request);
        if ($newPath !== null) {
            if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $data['profile_photo_path'] = $newPath;
        }

        $user->update($data);

        return back()->with('status', 'Profil berhasil diperbarui.');
    }

    public function sendResetEmail(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $status = Password::sendResetLink(['email' => $user->email]);
        if ($status !== Password::RESET_LINK_SENT) {
            return back()->withErrors(['reset_password' => __($status)]);
        }

        return back()->with('status', 'Link reset password telah dikirim ke email Anda.');
    }

    public function prepareResetWhatsapp(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $phoneDigits = $this->normalizeWhatsappNumber((string) $user->phone);
        if ($phoneDigits === null) {
            return back()->withErrors([
                'reset_password' => 'Nomor HP pada profil belum valid untuk WhatsApp. Gunakan format 08xxx atau 62xxx.',
            ]);
        }

        $token = Password::broker()->createToken($user);
        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]);

        $message = "Halo {$user->name}, ini link reset password akun Anda: {$resetUrl}";
        $waUrl = 'https://wa.me/' . $phoneDigits . '?text=' . rawurlencode($message);

        return back()->with('wa_reset_url', $waUrl)->with('status', 'Link reset password WhatsApp sudah dibuat.');
    }

    private function storeProfilePhoto(Request $request): ?string
    {
        $croppedData = trim((string) $request->input('profile_photo_cropped', ''));
        if ($croppedData !== '') {
            if (preg_match('/^data:image\/(png|jpe?g|webp);base64,(.+)$/i', $croppedData, $matches) !== 1) {
                return null;
            }

            $ext = strtolower($matches[1]);
            if ($ext === 'jpeg') {
                $ext = 'jpg';
            }

            $binary = base64_decode($matches[2], true);
            if ($binary === false) {
                return null;
            }

            $path = 'profile-photos/' . Str::uuid() . '.' . $ext;
            Storage::disk('public')->put($path, $binary);

            return $path;
        }

        if ($request->hasFile('profile_photo')) {
            return $request->file('profile_photo')->store('profile-photos', 'public');
        }

        return null;
    }

    private function normalizeWhatsappNumber(string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', $phone);
        if (! $digits) {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        }

        if (! str_starts_with($digits, '62') || strlen($digits) < 10) {
            return null;
        }

        return $digits;
    }
}

