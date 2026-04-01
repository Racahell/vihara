<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Admin\WebsiteSettingController;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityRegistration;
use App\Models\Donation;
use App\Models\DonationCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GuestHomeController extends Controller
{
    public function __invoke(Request $request)
    {
        $websiteSettings = app(WebsiteSettingController::class)->currentValues();
        $guestSubmissionToken = (string) Str::uuid();
        $request->session()->put('guest_donation_submission_token', $guestSubmissionToken);

        return view('guest', [
            'stats' => [
                'activities' => Activity::where('is_active', true)->count(),
                'participants' => ActivityRegistration::count(),
                'donations' => Donation::count(),
            ],
            'latestActivities' => Activity::where('is_active', true)
                ->orderBy('start_at')
                ->take(3)
                ->get(),
            'donationCategories' => DonationCategory::where('is_active', true)->orderBy('name')->get(),
            'websiteSettings' => $websiteSettings,
            'aboutVihara' => [
                'title' => $websiteSettings['guest_about_title'],
                'description' => $websiteSettings['guest_about_description'],
            ],
            'guestDonationSubmissionToken' => $guestSubmissionToken,
        ]);
    }
}
