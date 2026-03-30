<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;

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
            'guest_hero_title' => ['required', 'string', 'max:255'],
            'guest_hero_subtitle' => ['required', 'string', 'max:500'],
            'guest_about_title' => ['required', 'string', 'max:255'],
            'guest_about_description' => ['required', 'string', 'max:1000'],
            'vihara_location_name' => ['required', 'string', 'max:255'],
            'vihara_location_address' => ['required', 'string', 'max:500'],
            'vihara_map_url' => ['required', 'url', 'max:1000'],
        ]);

        foreach ($data as $key => $value) {
            WebsiteSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('status', 'Pengaturan website berhasil diperbarui.');
    }

    public function currentValues(): array
    {
        $defaults = [
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
