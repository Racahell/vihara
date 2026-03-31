<?php

return [
    'superadmin' => [
        [
            'title' => 'Manajemen',
            'items' => [
                ['label' => 'Dashboard', 'route' => 'dashboard'],
                ['label' => 'Manajemen Pengguna', 'route' => 'admin.users.index'],
                ['label' => 'Hak Akses', 'route' => 'admin.users.access'],
                ['label' => 'Pengurus', 'route' => 'admin.pengurus.index'],
                ['label' => 'Profil Saya', 'route' => 'profile.show'],
            ],
        ],
        [
            'title' => 'Operasional',
            'items' => [
                ['label' => 'Kegiatan', 'route' => 'admin.activities.index'],
                ['label' => 'Pendaftaran Kegiatan', 'route' => 'admin.registrations.index'],
                ['label' => 'Check In', 'route' => 'shared.checkin.index'],
                ['label' => 'Donasi', 'route' => 'umat.donations.index'],
                ['label' => 'Verifikasi Donasi', 'route' => 'admin.donation-verification.index'],
            ],
        ],
        [
            'title' => 'Laporan',
            'items' => [
                ['label' => 'Laporan', 'route' => 'reports.donations'],
                ['label' => 'Notifikasi', 'route' => 'admin.notifications.index'],
            ],
        ],
        [
            'title' => 'Sistem',
            'items' => [
                ['label' => 'Pengaturan Website', 'route' => 'admin.website-settings.edit'],
                ['label' => 'Backup Restore', 'route' => 'admin.backup-restore.index'],
                ['label' => 'Log Sistem', 'route' => 'admin.logs.index'],
            ],
        ],
    ],
    'admin' => [
        [
            'title' => 'Manajemen',
            'items' => [
                ['label' => 'Dashboard', 'route' => 'dashboard'],
                ['label' => 'Manajemen Pengguna', 'route' => 'admin.users.index'],
                ['label' => 'Pengurus', 'route' => 'admin.pengurus.index'],
                ['label' => 'Profil Saya', 'route' => 'profile.show'],
            ],
        ],
        [
            'title' => 'Operasional',
            'items' => [
                ['label' => 'Kegiatan', 'route' => 'admin.activities.index'],
                ['label' => 'Pendaftaran Kegiatan', 'route' => 'admin.registrations.index'],
                ['label' => 'Check In', 'route' => 'shared.checkin.index'],
                ['label' => 'Donasi', 'route' => 'umat.donations.index'],
                ['label' => 'Verifikasi Donasi', 'route' => 'admin.donation-verification.index'],
            ],
        ],
        [
            'title' => 'Laporan',
            'items' => [
                ['label' => 'Laporan', 'route' => 'reports.donations'],
                ['label' => 'Notifikasi', 'route' => 'admin.notifications.index'],
            ],
        ],
        [
            'title' => 'Sistem',
            'items' => [
                ['label' => 'Pengaturan Website', 'route' => 'admin.website-settings.edit'],
                ['label' => 'Log Sistem', 'route' => 'admin.logs.index'],
            ],
        ],
    ],
    'owner' => [
        [
            'title' => 'Monitoring',
            'items' => [
                ['label' => 'Dashboard', 'route' => 'dashboard'],
                ['label' => 'Kegiatan', 'route' => 'admin.activities.index'],
                ['label' => 'Donasi', 'route' => 'umat.donations.index'],
                ['label' => 'Laporan', 'route' => 'reports.donations'],
                ['label' => 'Pengurus', 'route' => 'admin.pengurus.index'],
                ['label' => 'Profil Saya', 'route' => 'profile.show'],
            ],
        ],
    ],
    'petugas' => [
        [
            'title' => 'Operasional Lapangan',
            'items' => [
                ['label' => 'Dashboard', 'route' => 'dashboard'],
                ['label' => 'Pendaftaran Kegiatan', 'route' => 'admin.registrations.index'],
                ['label' => 'Check In', 'route' => 'shared.checkin.index'],
                ['label' => 'Data Kehadiran', 'route' => 'shared.checkin.attendance'],
                ['label' => 'Profil Saya', 'route' => 'profile.show'],
            ],
        ],
    ],
    'umat' => [
        [
            'title' => 'Menu Umat',
            'items' => [
                ['label' => 'Beranda', 'route' => 'umat.dashboard'],
                ['label' => 'Riwayat Saya', 'route' => 'umat.my-history'],
                ['label' => 'Donasi', 'route' => 'umat.donations.index'],
                ['label' => 'Profil Saya', 'route' => 'profile.show'],
            ],
        ],
    ],
];
