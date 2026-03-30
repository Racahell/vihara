<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityRegistration;

class RegistrationController extends Controller
{
    public function index()
    {
        return view('admin.registrations', [
            'registrations' => ActivityRegistration::with('activity')->latest('registered_at')->paginate(20),
        ]);
    }
}
