<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class WebsiteSettingController extends Controller
{
    public function edit()
    {
        return view('admin.website-settings', [
            'settings' => $this->currentValues(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'website_name' => ['required', 'string', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:1000'],
            'website_language' => ['required', 'string', 'max:20'],
            'website_timezone' => ['required', 'string', Rule::in(timezone_identifiers_list())],
            'company_name' => ['nullable', 'string', 'max:255'],
            'manager_name' => ['nullable', 'string', 'max:255'],
            'company_address' => ['nullable', 'string', 'max:1000'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_whatsapp' => ['nullable', 'string', 'max:50'],
            'donation_bank_name' => ['nullable', 'string', 'max:100'],
            'donation_account_number' => ['nullable', 'string', 'max:100'],
            'donation_account_holder' => ['nullable', 'string', 'max:255'],
            'company_description' => ['nullable', 'string', 'max:1000'],
            'website_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'website_favicon' => ['nullable', 'file', 'mimes:ico,png,jpg,jpeg,svg', 'max:1024'],
            'website_logo_cropped' => ['nullable', 'string'],
            'website_favicon_cropped' => ['nullable', 'string'],
            'guest_hero_title' => ['required', 'string', 'max:255'],
            'guest_hero_subtitle' => ['required', 'string', 'max:500'],
            'guest_about_title' => ['required', 'string', 'max:255'],
            'guest_about_description' => ['required', 'string', 'max:1000'],
            'vihara_location_name' => ['required', 'string', 'max:255'],
            'vihara_location_address' => ['required', 'string', 'max:500'],
            'vihara_map_url' => ['required', 'url', 'max:1000'],
        ]);

        try {
            if ($request->filled('website_logo_cropped') || $request->hasFile('website_logo')) {
                $oldLogoPath = WebsiteSetting::query()->where('key', 'website_logo_path')->value('value');
                if ($oldLogoPath && Storage::disk('public')->exists($oldLogoPath)) {
                    Storage::disk('public')->delete($oldLogoPath);
                }
                $data['website_logo_path'] = $request->filled('website_logo_cropped')
                    ? $this->storeBase64Image((string) $request->input('website_logo_cropped'), 'logo')
                    : $request->file('website_logo')->store('website', 'public');
            }
            unset($data['website_logo']);
            unset($data['website_logo_cropped']);

            if ($request->filled('website_favicon_cropped') || $request->hasFile('website_favicon')) {
                $oldFaviconPath = WebsiteSetting::query()->where('key', 'website_favicon_path')->value('value');
                if ($oldFaviconPath && Storage::disk('public')->exists($oldFaviconPath)) {
                    Storage::disk('public')->delete($oldFaviconPath);
                }
                $data['website_favicon_path'] = $request->filled('website_favicon_cropped')
                    ? $this->storeBase64Image((string) $request->input('website_favicon_cropped'), 'favicon')
                    : $request->file('website_favicon')->store('website', 'public');
            }
            unset($data['website_favicon']);
            unset($data['website_favicon_cropped']);

            foreach ($data as $key => $value) {
                WebsiteSetting::updateOrCreate(['key' => $key], ['value' => $value]);
            }
        } catch (QueryException $e) {
            report($e);

            return back()->withErrors([
                'settings' => 'Pengaturan belum bisa disimpan karena tabel website_settings belum siap.',
            ]);
        } catch (\Throwable $e) {
            report($e);

            return back()->withErrors([
                'settings' => 'Gagal memproses gambar logo/favicon. Silakan coba file lain.',
            ])->withInput();
        }

        return back()->with('status', 'Pengaturan website berhasil diperbarui.');
    }

    public function currentValues(): array
    {
        $defaults = [
            'website_name' => config('app.name', 'Website'),
            'website_logo_path' => '',
            'website_favicon_path' => '',
            'website_url' => config('app.url'),
            'website_language' => 'id',
            'website_timezone' => config('app.timezone', 'Asia/Jakarta'),
            'company_name' => 'Vihara Dharma Sejahtera',
            'manager_name' => '',
            'company_address' => '',
            'contact_phone' => '',
            'contact_email' => '',
            'contact_whatsapp' => '',
            'donation_bank_name' => config('donation.bank_name', 'BCA'),
            'donation_account_number' => config('donation.account_number', '1234567890'),
            'donation_account_holder' => config('donation.account_holder', 'Vihara Dharma Sejahtera'),
            'company_description' => 'Ruang informasi kegiatan dan pelayanan umat.',
            'guest_hero_title' => 'Bersama Vihara, Menumbuhkan Kebajikan dan Kepedulian',
            'guest_hero_subtitle' => 'Ruang informasi kegiatan vihara, pelayanan umat, dan donasi untuk mendukung aktivitas kebajikan bersama.',
            'guest_about_title' => 'Vihara sebagai ruang pembinaan batin, kebajikan, dan kebersamaan umat.',
            'guest_about_description' => 'Melalui kegiatan rutin, pelayanan sosial, dan tata kelola donasi yang transparan, vihara hadir untuk mendukung perjalanan spiritual serta nilai kepedulian dalam kehidupan sehari-hari.',
            'vihara_location_name' => 'Vihara Dharma Sejahtera',
            'vihara_location_address' => 'Lokasi vihara dapat diperbarui dari menu Pengaturan Website.',
            'vihara_map_url' => 'https://maps.app.goo.gl/w8GL3PvF43DqNb986',
        ];

        try {
            $fromDb = WebsiteSetting::query()
                ->whereIn('key', array_keys($defaults))
                ->pluck('value', 'key')
                ->toArray();
        } catch (QueryException $e) {
            report($e);

            return $defaults;
        }

        return array_merge($defaults, $fromDb);
    }

    private function storeBase64Image(string $dataUri, string $prefix): string
    {
        if (! preg_match('/^data:image\/([a-zA-Z0-9.+-]+);base64,/', $dataUri, $matches)) {
            throw new \RuntimeException('Format gambar crop tidak valid.');
        }

        $mimeSubtype = strtolower((string) ($matches[1] ?? 'png'));
        $extension = match ($mimeSubtype) {
            'jpeg', 'jpg' => 'jpg',
            'png' => 'png',
            'webp' => 'webp',
            'svg+xml', 'svg' => 'svg',
            default => 'png',
        };

        $base64 = substr($dataUri, strpos($dataUri, ',') + 1);
        $binary = base64_decode($base64, true);
        if ($binary === false) {
            throw new \RuntimeException('Data gambar crop tidak dapat diproses.');
        }

        $path = 'website/' . $prefix . '-' . now()->format('YmdHis') . '-' . Str::lower(Str::random(8)) . '.' . $extension;
        Storage::disk('public')->put($path, $binary);

        return $path;
    }
}
