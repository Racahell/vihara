<?php

use App\Http\Controllers\Admin\ActivityManagementController;
use App\Http\Controllers\Admin\BackupRestoreController;
use App\Http\Controllers\Admin\DonationVerificationController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PengurusController;
use App\Http\Controllers\Admin\RegistrationController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\WebsiteSettingController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Guest\GuestHomeController;
use App\Http\Controllers\Shared\CheckInController;
use App\Http\Controllers\Shared\DashboardController;
use App\Http\Controllers\Shared\MidtransCallbackController;
use App\Http\Controllers\Shared\ProfileController;
use App\Http\Controllers\Shared\ReportController;
use App\Http\Controllers\Umat\ActivityController;
use App\Http\Controllers\Umat\DonationController;
use App\Http\Controllers\Umat\MyHistoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('guest.home'));

Route::middleware('guest')->group(function () {
    Route::get('/guest', GuestHomeController::class)->name('guest.home');
    Route::get('/quest', GuestHomeController::class)->name('quest.home');
    Route::get('/guest/donations', fn () => redirect()->to(route('guest.home') . '#donasi'))->name('guest.donations.index');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::post('/verify-email/resend', [AuthController::class, 'resendVerificationEmail'])->name('verify.email.resend');
    Route::post('/forgot-password/email', [AuthController::class, 'sendResetLinkEmail'])->name('forgot-password.email');
    Route::post('/forgot-password/whatsapp', [AuthController::class, 'prepareResetLinkWhatsapp'])->name('forgot-password.whatsapp');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
    Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verify.email');
    Route::post('/guest/donations', [DonationController::class, 'storeGuest'])->name('guest.donations.store');
    Route::post('/guest/donations/{donation}/proof', [DonationController::class, 'uploadGuestProof'])->name('guest.donations.upload-proof');
});

Route::post('/payments/midtrans/callback', MidtransCallbackController::class)->name('payments.midtrans.callback');

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->middleware('permission:dashboard.view')->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/reset-password/email', [ProfileController::class, 'sendResetEmail'])->name('profile.reset-password.email');
    Route::post('/profile/reset-password/whatsapp', [ProfileController::class, 'prepareResetWhatsapp'])->name('profile.reset-password.whatsapp');

    Route::middleware('role:superadmin,admin,owner')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->middleware('permission:data_user.view')->name('users.index');
        Route::get('/pengurus', [PengurusController::class, 'index'])->middleware('permission:pengurus.view')->name('pengurus.index');
    });

    Route::middleware('role:superadmin,admin')->prefix('admin')->name('admin.')->group(function () {
        Route::post('/users', [UserManagementController::class, 'store'])->middleware('permission:data_user.create')->name('users.store');
        Route::post('/users/{user}/role', [UserManagementController::class, 'updateRole'])->middleware('permission:data_user.edit')->name('users.update-role');
        Route::post('/users/{user}/update', [UserManagementController::class, 'update'])->middleware('permission:data_user.edit')->name('users.update');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->middleware('permission:data_user.delete')->name('users.destroy');
        Route::get('/notifications', [NotificationController::class, 'index'])->middleware('permission:notifikasi.view')->name('notifications.index');
        Route::get('/website-settings', [WebsiteSettingController::class, 'edit'])->middleware('permission:pengaturan_website.view')->name('website-settings.edit');
        Route::post('/website-settings', [WebsiteSettingController::class, 'update'])->middleware('permission:pengaturan_website.edit')->name('website-settings.update');
    });

    Route::middleware('role:superadmin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users/access', [UserManagementController::class, 'access'])->middleware('permission:hak_akses.view')->name('users.access');
        Route::post('/users/access', [UserManagementController::class, 'updateAccess'])->middleware('permission:hak_akses.edit')->name('users.access.update');
        Route::post('/users/{userId}/restore', [UserManagementController::class, 'restore'])->middleware('permission:data_user.delete')->name('users.restore');
        Route::delete('/users/{userId}/force-delete', [UserManagementController::class, 'forceDelete'])->middleware('permission:data_user.delete')->name('users.force-delete');
        Route::get('/backup-restore', [BackupRestoreController::class, 'index'])->middleware('permission:backup_restore.view')->name('backup-restore.index');
        Route::get('/restore-data', [BackupRestoreController::class, 'index'])->middleware('permission:backup_restore.view')->name('restore-data.index');
        Route::post('/backup-restore/backup', [BackupRestoreController::class, 'backup'])->middleware('permission:backup_restore.create')->name('backup-restore.backup');
        Route::post('/backup-restore/restore', [BackupRestoreController::class, 'restore'])->middleware('permission:backup_restore.edit')->name('backup-restore.restore');
        Route::post('/backup-restore/clear-data', [BackupRestoreController::class, 'clearData'])->middleware('permission:backup_restore.delete')->name('backup-restore.clear-data');
        Route::post('/backup-restore/clear-table', [BackupRestoreController::class, 'clearTable'])->middleware('permission:backup_restore.delete')->name('backup-restore.clear-table');
    });

    Route::middleware('role:superadmin,admin,owner')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/activities', [ActivityManagementController::class, 'index'])->middleware('permission:kegiatan.view')->name('activities.index');
    });

    Route::middleware('role:superadmin,admin,petugas')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/registrations', [RegistrationController::class, 'index'])->middleware('permission:pendaftaran_kegiatan.view')->name('registrations.index');
        Route::get('/registrations/excel', [RegistrationController::class, 'excel'])->middleware('permission:pendaftaran_kegiatan.view')->name('registrations.excel');
        Route::get('/registrations/pdf', [RegistrationController::class, 'pdf'])->middleware('permission:pendaftaran_kegiatan.view')->name('registrations.pdf');
        Route::get('/registrations/print', [RegistrationController::class, 'print'])->middleware('permission:pendaftaran_kegiatan.view')->name('registrations.print');
    });

    Route::middleware('role:superadmin,admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/donation-verification', [DonationVerificationController::class, 'index'])->middleware('permission:verifikasi_donasi.view')->name('donation-verification.index');
        Route::get('/donation-receipts/{donation}', [DonationVerificationController::class, 'downloadReceipt'])->middleware('permission:verifikasi_donasi.view')->name('donation-receipts.download');
        Route::get('/donation-proofs/{donation}', [DonationVerificationController::class, 'downloadProof'])->middleware('permission:verifikasi_donasi.view')->name('donation-proof.download');
        Route::get('/donation-proofs/{donation}/preview', [DonationVerificationController::class, 'previewProof'])->middleware('permission:verifikasi_donasi.view')->name('donation-proof.preview');
    });

    Route::middleware('role:superadmin,admin')->prefix('admin')->name('admin.')->group(function () {
        Route::post('/activities', [ActivityManagementController::class, 'store'])->middleware('permission:kegiatan.create')->name('activities.store');
        Route::put('/activities/{activity}', [ActivityManagementController::class, 'update'])->middleware('permission:kegiatan.edit')->name('activities.update');
        Route::delete('/activities/{activity}', [ActivityManagementController::class, 'destroy'])->middleware('permission:kegiatan.delete')->name('activities.destroy');
        Route::post('/donation-verification/{donation}', [DonationVerificationController::class, 'verify'])->middleware('permission:verifikasi_donasi.edit')->name('donation-verification.verify');
    });

    Route::middleware('role:superadmin')->prefix('admin')->name('admin.')->group(function () {
        Route::post('/activities/{activityId}/restore', [ActivityManagementController::class, 'restore'])->middleware('permission:kegiatan.delete')->name('activities.restore');
        Route::delete('/activities/{activityId}/force-delete', [ActivityManagementController::class, 'forceDelete'])->middleware('permission:kegiatan.delete')->name('activities.force-delete');
    });

    Route::middleware('role:superadmin,admin')->prefix('logs')->name('admin.logs.')->group(function () {
        Route::get('/', [LogController::class, 'index'])->middleware('permission:log_sistem.view')->name('index');
        Route::get('/activity', [LogController::class, 'activity'])->middleware('permission:log_sistem.view')->name('activity');
        Route::get('/login', [LogController::class, 'login'])->middleware('permission:log_sistem.view')->name('login');
    });

    Route::middleware('role:superadmin,admin,petugas')->prefix('checkin')->name('shared.checkin.')->group(function () {
        Route::get('/', [CheckInController::class, 'index'])->middleware('permission:check_in.view')->name('index');
        Route::get('/attendance', [CheckInController::class, 'attendance'])->middleware('permission:check_in.view')->name('attendance');
        Route::post('/by-code', [CheckInController::class, 'byCode'])->middleware('permission:check_in.create')->name('by-code');
        Route::post('/walkin', [CheckInController::class, 'walkIn'])->middleware('permission:check_in.create')->name('walkin');
    });

    Route::middleware('role:superadmin,admin,owner')->prefix('reports')->name('reports.')->group(function () {
        Route::get('/donations', [ReportController::class, 'donation'])->middleware('permission:laporan.view')->name('donations');
        Route::get('/donations/pdf', [ReportController::class, 'donationPdf'])->middleware('permission:laporan.view')->name('donations.pdf');
        Route::get('/donations/excel', [ReportController::class, 'donationExcel'])->middleware('permission:laporan.view')->name('donations.excel');
        Route::get('/donations/print', [ReportController::class, 'donationPrint'])->middleware('permission:laporan.view')->name('donations.print');
    });

    Route::middleware('role:umat,superadmin,admin,owner')->prefix('umat')->name('umat.')->group(function () {
        Route::get('/dashboard', [ActivityController::class, 'index'])->name('dashboard');
        Route::get('/favorites', [ActivityController::class, 'favorites'])->middleware('permission:kegiatan.view')->name('favorites');
        Route::get('/activities/{activity}', [ActivityController::class, 'show'])->name('activities.show');
        Route::post('/activities/{activity}/register', [ActivityController::class, 'register'])->name('activities.register');
        Route::post('/activities/{activity}/favorite', [ActivityController::class, 'favorite'])->name('activities.favorite');

        Route::get('/donations', [DonationController::class, 'index'])->middleware('permission:donasi.view')->name('donations.index');
        Route::get('/donations/{donation}/pay', [DonationController::class, 'pay'])->middleware('permission:donasi.view')->name('donations.pay');

        Route::get('/my-history', MyHistoryController::class)->name('my-history');
        Route::get('/my-history/registrations/{registration}/ticket-pdf', [MyHistoryController::class, 'ticketPdf'])
            ->name('my-history.ticket-pdf');
    });

    Route::middleware('role:umat,superadmin,admin')->prefix('umat')->name('umat.')->group(function () {
        Route::post('/donations', [DonationController::class, 'store'])->middleware('permission:donasi.create')->name('donations.store');
        Route::post('/donations/{donation}/proof', [DonationController::class, 'uploadProof'])->name('donations.upload-proof');
    });
});
