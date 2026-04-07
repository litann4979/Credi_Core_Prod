<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SalarySlip;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SalarySlipController extends Controller
{
public function index(Request $request)
{
    $user = $request->user();

    $slips = SalarySlip::where('user_id', $user->id)
                ->orderBy('month', 'desc')
                ->get()
                ->map(function ($slip) {
                    $slip->pdf_url = asset('storage/' . $slip->pdf_path);
                    // OR use: Storage::url($slip->pdf_path)
                    return $slip;
                });

    return response()->json([
        'status' => 'success',
        'data' => $slips,
    ]);
}


public function downloadPdf($id)
{
    $salarySlip = SalarySlip::findOrFail($id);

    $filePath = storage_path('app/public/' . $salarySlip->pdf_path);

    if (!file_exists($filePath)) {
        return response()->json([
            'status' => 'error',
            'message' => 'File not found',
        ], Response::HTTP_NOT_FOUND);
    }

    return response()->download($filePath);
}
}
