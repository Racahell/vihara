<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            'company_description' => ['nullable', 'string', 'max:1000'],
            'website_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'website_favicon' => ['nullable', 'file', 'mimes:ico,png,svg', 'max:1024'],
            'guest_hero_title' => ['required', 'string', 'max:255'],
            'guest_hero_subtitle' => ['required', 'string', 'max:500'],
            'guest_about_title' => ['required', 'string', 'max:255'],
            'guest_about_description' => ['required', 'string', 'max:1000'],
            'vihara_location_name' => ['required', 'string', 'max:255'],
            'vihara_location_address' => ['required', 'string', 'max:500'],
            'vihara_map_url' => ['required', 'url', 'max:1000'],
        ]);

        if ($request->hasFile('website_logo')) {
            $oldLogoPath = WebsiteSetting::query()->where('key', 'website_logo_path')->value('value');
            if ($oldLogoPath && Storage::disk('public')->exists($oldLogoPath)) {
                Storage::disk('public')->delete($oldLogoPath);
            }
            $data['website_logo_path'] = $request->file('website_logo')->store('website', 'public');
        }
        unset($data['website_logo']);

        if ($request->hasFile('website_favicon')) {
            $oldFaviconPath = WebsiteSetting::query()->where('key', 'website_favicon_path')->value('value');
            if ($oldFaviconPath && Storage::disk('public')->exists($oldFaviconPath)) {
                Storage::disk('public')->delete($oldFaviconPath);
            }
            $data['website_favicon_path'] = $request->file('website_favicon')->store('website', 'public');
        }
        unset($data['website_favicon']);

        foreach ($data as $key => $value) {
            WebsiteSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('status', 'Pengaturan website berhasil diperbarui.');
    }

    public function currentValues(): array
    {
        $defaults = [
            'website_name' => 'Portal Vihara',
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
            'company_description' => 'Ruang informasi kegiatan dan pelayanan umat.',
            'guest_hero_title' => 'Bersama Vihara, Menumbuhkan Kebajikan dan Kepedulian',
            'guest_hero_subtitle' => 'Ruang informasi kegiatan vihara, pelayanan umat, dan donasi untuk mendukung aktivitas kebajikan bersama.',
            'guest_about_title' => 'Vihara sebagai ruang pembinaan batin, kebajikan, dan kebersamaan umat.',
            'guest_about_description' => 'Melalui kegiatan rutin, pelayanan sosial, dan tata kelola donasi yang transparan, vihara hadir untuk mendukung perjalanan spiritual serta nilai kepedulian dalam kehidupan sehari-hari.',
            'vihara_location_name' => 'Vihara Dharma Sejahtera',
            'vihara_location_address' => 'Lokasi vihara dapat diperbarui dari menu Pengaturan Website.',
            'vihara_map_url' => 'https://maps.app.goo.gl/w8GL3PvF43DqNb986',
        ];

        $fromDb = WebsiteSetting::query()
            ->whereIn('key', array_keys($defaults))
            ->pluck('value', 'key')
            ->toArray();

        return array_merge($defaults, $fromDb);
    }
}
