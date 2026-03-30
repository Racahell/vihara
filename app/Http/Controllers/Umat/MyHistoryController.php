<?php

namespace App\Http\Controllers\Umat;

use App\Http\Controllers\Controller;
use App\Models\ActivityRegistration;
use App\Models\Donation;
use App\Services\QrCodeService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class MyHistoryController extends Controller
{
    public function __invoke()
    {
        $user = auth()->user();

        return view('umat.history', [
            'registrations' => ActivityRegistration::with('activity')->where('user_id', $user->id)->latest('registered_at')->get(),
            'donations' => Donation::where('user_id', $user->id)->latest()->get(),
        ]);
    }

    public function ticketPdf(Request $request, ActivityRegistration $registration, QrCodeService $qrCodeService)
    {
        abort_unless((int) $registration->user_id === (int) $request->user()->id, 403);

        $pdf = Pdf::loadView('reports.pdf.ticket-registration', [
            'registration' => $registration->load('activity'),
            'qrDataUri' => $qrCodeService->dataUri((string) $registration->qr_payload, 260, 8),
            'printedAt' => now(),
        ])->setPaper('a5', 'portrait');

        return $pdf->download('tiket-' . $registration->registration_code . '.pdf');
    }
}
