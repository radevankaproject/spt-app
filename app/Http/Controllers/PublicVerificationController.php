<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use Illuminate\Http\Request;

class PublicVerificationController extends Controller
{
    public function verifyAgreement($code)
    {
        $agreement = Agreement::where('verification_code', $code)
            ->with(['leader.user', 'fieldCoordinator.user', 'activeParkingLocations.roadSection'])
            ->first();

        // Kirim data agreement (atau null jika tidak ditemukan) ke view
        return view('verification.agreement', compact('agreement'));
    }
}
