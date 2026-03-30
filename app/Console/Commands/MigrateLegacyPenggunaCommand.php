<?php

namespace App\Console\Commands;

use App\Models\Pengguna;
use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MigrateLegacyPenggunaCommand extends Command
{
    protected $signature = 'app:migrate-pengguna';

    protected $description = 'Migrasi data tabel pengguna lama ke users + role_user';

    public function handle(): int
    {
        if (! DB::getSchemaBuilder()->hasTable('pengguna')) {
            $this->warn('Tabel pengguna tidak ditemukan. Migrasi dilewati.');
            return self::SUCCESS;
        }

        $map = [
            'superadmin' => 'superadmin',
            'admin' => 'admin',
            'owner' => 'owner',
            'ketua' => 'owner',
            'petugas' => 'petugas',
            'umat' => 'umat',
        ];

        $count = 0;

        Pengguna::query()->chunk(100, function ($rows) use ($map, &$count) {
            foreach ($rows as $legacy) {
                $email = Str::contains($legacy->username, '@')
                    ? $legacy->username
                    : strtolower($legacy->username) . '@legacy.vihara.local';

                $user = User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => $legacy->nama,
                        'username' => $legacy->username,
                        'password' => $legacy->password,
                        'is_active' => true,
                        'email_verified_at' => now(),
                        'activated_at' => now(),
                    ]
                );

                $slug = $map[strtolower((string) $legacy->peran)] ?? 'umat';
                $roleId = Role::where('slug', $slug)->value('id');

                if ($roleId) {
                    $user->roles()->syncWithoutDetaching([$roleId]);
                }

                $count++;
            }
        });

        $this->info("Migrasi selesai. Total akun diproses: {$count}");

        return self::SUCCESS;
    }
}
