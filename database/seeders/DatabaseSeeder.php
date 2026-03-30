<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\DonationCategory;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'superadmin' => 'Superadmin',
            'admin' => 'Admin',
            'owner' => 'Owner / Ketua',
            'petugas' => 'Petugas',
            'umat' => 'Umat',
        ];

        foreach ($roles as $slug => $name) {
            Role::updateOrCreate(['slug' => $slug], ['name' => $name]);
        }

        $accounts = [
            ['role' => 'superadmin', 'name' => 'superadmin', 'username' => 'superadmin', 'email' => 'superadmin@g', 'phone' => '081100000001'],
            ['role' => 'admin', 'name' => 'admin', 'username' => 'admin', 'email' => 'admin@g', 'phone' => '081100000002'],
            ['role' => 'owner', 'name' => 'owner', 'username' => 'owner', 'email' => 'owner@g', 'phone' => '081100000003'],
            ['role' => 'petugas', 'name' => 'petugas', 'username' => 'petugas', 'email' => 'petugas@g', 'phone' => '081100000004'],
            ['role' => 'umat', 'name' => 'umat', 'username' => 'umat', 'email' => 'umat@g', 'phone' => '081100000005'],
        ];

        $superadmin = null;
        foreach ($accounts as $item) {
            $user = User::query()
                ->where('email', $item['email'])
                ->orWhere('username', $item['username'])
                ->first();

            if (! $user) {
                $user = new User();
            }

            $user->fill([
                'name' => $item['name'],
                'username' => $item['username'],
                'email' => $item['email'],
                'phone' => $item['phone'],
                'password' => Hash::make('password123'),
                'is_active' => true,
                'email_verified_at' => now(),
                'activated_at' => now(),
            ]);
            $user->save();

            $roleId = Role::where('slug', $item['role'])->value('id');
            if ($roleId) {
                $user->roles()->sync([$roleId]);
            }

            if ($item['role'] === 'superadmin') {
                $superadmin = $user;
            }
        }

        foreach ([
            ['name' => 'Donasi Umum'],
            ['name' => 'Donasi Pembangunan'],
            ['name' => 'Donasi Acara'],
        ] as $category) {
            DonationCategory::updateOrCreate(['name' => $category['name']], ['is_active' => true]);
        }

        if (Activity::count() === 0) {
            foreach ([
                ['title' => 'Puja Bakti Mingguan', 'days' => 3, 'quota' => 150],
                ['title' => 'Meditasi Purnama', 'days' => 10, 'quota' => 120],
                ['title' => 'Bakti Sosial Vihara', 'days' => 20, 'quota' => 80],
            ] as $item) {
                Activity::create([
                    'title' => $item['title'],
                    'slug' => Str::slug($item['title']) . '-' . Str::lower(Str::random(4)),
                    'description' => 'Kegiatan rutin vihara untuk kebaktian dan kebersamaan umat.',
                    'location' => 'Aula Utama Vihara',
                    'start_at' => now()->addDays($item['days'])->setHour(9),
                    'end_at' => now()->addDays($item['days'])->setHour(12),
                    'quota' => $item['quota'],
                    'registered_count' => 0,
                    'is_active' => true,
                    'created_by' => $superadmin?->id,
                ]);
            }
        }
    }
}
