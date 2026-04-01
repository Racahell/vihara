<?php

return [
    'route_permissions' => [
        'dashboard' => 'dashboard.view',

        'admin.users.index' => 'data_user.view',
        'admin.users.store' => 'data_user.create',
        'admin.users.update-role' => 'data_user.edit',
        'admin.users.update' => 'data_user.edit',
        'admin.users.destroy' => 'data_user.delete',

        'admin.users.access' => 'hak_akses.view',
        'admin.users.access.update' => 'hak_akses.edit',

        'admin.pengurus.index' => 'pengurus.view',

        'admin.activities.index' => 'kegiatan.view',
        'admin.activities.store' => 'kegiatan.create',
        'umat.favorites' => 'kegiatan.view',

        'admin.registrations.index' => 'pendaftaran_kegiatan.view',

        'shared.checkin.index' => 'check_in.view',
        'shared.checkin.attendance' => 'check_in.view',
        'shared.checkin.by-code' => 'check_in.create',
        'shared.checkin.walkin' => 'check_in.create',

        'umat.donations.index' => 'donasi.view',
        'umat.donations.pay' => 'donasi.view',
        'umat.donations.store' => 'donasi.create',
        'umat.donations.upload-proof' => 'donasi.edit',

        'admin.donation-verification.index' => 'verifikasi_donasi.view',
        'admin.donation-receipts.download' => 'verifikasi_donasi.view',
        'admin.donation-proof.download' => 'verifikasi_donasi.view',
        'admin.donation-verification.verify' => 'verifikasi_donasi.edit',

        'reports.donations' => 'laporan.view',
        'reports.donations.pdf' => 'laporan.view',
        'reports.donations.excel' => 'laporan.view',
        'reports.donations.print' => 'laporan.view',

        'admin.notifications.index' => 'notifikasi.view',

        'admin.website-settings.edit' => 'pengaturan_website.view',
        'admin.website-settings.update' => 'pengaturan_website.edit',

        'admin.backup-restore.index' => 'backup_restore.view',
        'admin.restore-data.index' => 'backup_restore.view',
        'admin.backup-restore.backup' => 'backup_restore.create',
        'admin.backup-restore.restore' => 'backup_restore.edit',
        'admin.backup-restore.clear-data' => 'backup_restore.delete',

        'admin.logs.index' => 'log_sistem.view',
        'admin.logs.activity' => 'log_sistem.view',
        'admin.logs.login' => 'log_sistem.view',
    ],
];
