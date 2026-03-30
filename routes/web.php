<?php

use App\Http\Controllers\Admin\ActivityManagementController;
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
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verify.email');
    Route::post('/guest/donations', [DonationController::class, 'storeGuest'])->name('guest.donations.store');
    Route::post('/guest/donations/{donation}/proof', [DonationController::class, 'uploadGuestProof'])->name('guest.donations.upload-proof');
});

Route::post('/payments/midtrans/callback', MidtransCallbackController::class)->name('payments.midtrans.callback');

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('role:superadmin,admin,owner')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/pengurus', [PengurusController::class, 'index'])->name('pengurus.index');
    });

    Route::middleware('role:superadmin,admin')->prefix('admin')->name('admin.')->group(function () {
        Route::post('/users/{user}/role', [UserManagementController::class, 'updateRole'])->name('users.update-role');
        Route::post('/users/{user}/update', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    });

    Route::middleware('role:superadmin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/website-settings', [WebsiteSettingController::class, 'edit'])->name('website-settings.edit');
        Route::post('/website-settings', [WebsiteSettingController::class, 'update'])->name('website-settings.update');
    });

    Route::middleware('role:superadmin,admin,owner')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/activities', [ActivityManagementController::class, 'index'])->name('activities.index');
    });

    Route::middleware('role:superadmin,admin,petugas')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/registrations', [RegistrationController::class, 'index'])->name('registrations.index');
    });

    Route::middleware('role:superadmin,admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/donation-verification', [DonationVerificationController::class, 'index'])->name('donation-verification.index');
        Route::get('/donation-receipts/{donation}', [DonationVerificationController::class, 'downloadReceipt'])->name('donation-receipts.download');
        Route::get('/donation-proofs/{donation}', [DonationVerificationController::class, 'downloadProof'])->name('donation-proof.download');
    });

    Route::middleware('role:superadmin,admin')->prefix('admin')->name('admin.')->group(function () {
        Route::post('/activities', [ActivityManagementController::class, 'store'])->name('activities.store');
        Route::post('/donation-verification/{donation}', [DonationVerificationController::class, 'verify'])->name('donation-verification.verify');
    });

    Route::middleware('role:superadmin,admin')->prefix('logs')->name('admin.logs.')->group(function () {
        Route::get('/', [LogController::class, 'index'])->name('index');
        Route::get('/activity', [LogController::class, 'activity'])->name('activity');
        Route::get('/login', [LogController::class, 'login'])->name('login');
    });

    Route::middleware('role:superadmin,admin,petugas')->prefix('checkin')->name('shared.checkin.')->group(function () {
        Route::get('/', [CheckInController::class, 'index'])->name('index');
        Route::post('/by-code', [CheckInController::class, 'byCode'])->name('by-code');
        Route::post('/walkin', [CheckInController::class, 'walkIn'])->name('walkin');
    });

    Route::middleware('role:superadmin,admin,owner')->prefix('reports')->name('reports.')->group(function () {
        Route::get('/donations', [ReportController::class, 'donation'])->name('donations');
        Route::get('/donations/pdf', [ReportController::class, 'donationPdf'])->name('donations.pdf');
        Route::get('/donations/excel', [ReportController::class, 'donationExcel'])->name('donations.excel');
        Route::get('/donations/print', [ReportController::class, 'donationPrint'])->name('donations.print');
    });

    Route::middleware('role:umat,superadmin,admin,owner')->prefix('umat')->name('umat.')->group(function () {
        Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
        Route::get('/activities/{activity}', [ActivityController::class, 'show'])->name('activities.show');
        Route::post('/activities/{activity}/register', [ActivityController::class, 'register'])->name('activities.register');
        Route::post('/activities/{activity}/favorite', [ActivityController::class, 'favorite'])->name('activities.favorite');

        Route::get('/donations', [DonationController::class, 'index'])->name('donations.index');

        Route::get('/my-history', MyHistoryController::class)->name('my-history');
        Route::get('/my-history/registrations/{registration}/ticket-pdf', [MyHistoryController::class, 'ticketPdf'])
            ->name('my-history.ticket-pdf');
    });

    Route::middleware('role:umat,superadmin,admin')->prefix('umat')->name('umat.')->group(function () {
        Route::post('/donations', [DonationController::class, 'store'])->name('donations.store');
        Route::post('/donations/{donation}/proof', [DonationController::class, 'uploadProof'])->name('donations.upload-proof');
    });
});
