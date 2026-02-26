<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DoctorReport;
use App\Models\GrantAccess;

class DoctorReportController extends Controller
{
    // Add doctor report
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|integer|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'file' => 'nullable|file|max:10240', // optional upload
        ]);

        $doctor = Auth::user();

        // Optional: check wallet verification
        if (!$doctor->wallet_verified) {
            return response()->json([
                'success' => false,
                'message' => 'Wallet not verified'
            ], 401);
        }

        // Check if doctor has access to patient
        if (
            !GrantAccess::where('authorized_id', $doctor->id)
                ->where('patient_id', $request->patient_id)
                ->where('role_type', 'doctor')->exists()
        ) {
            return response()->json([
                'success' => false,
                'message' => 'No access to this patient'
            ], 403);
        }

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('doctor-reports', 's3');
        }

        $report = DoctorReport::create([
            'doctor_id' => $doctor->id,
            'patient_id' => $request->patient_id,
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $filePath,
        ]);

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    // List reports for a patient
    public function index($patient_id)
    {
        $doctor = Auth::user();
        if ($doctor->role !== 'doctor') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Check access
        if (
            !GrantAccess::where('authorized_id', $doctor->id)
                ->where('patient_id', $patient_id)
                ->where('role_type', 'doctor')->exists()
        ) {
            return response()->json([
                'success' => false,
                'message' => 'No access to this patient'
            ], 403);
        }

        $reports = DoctorReport::where('patient_id', $patient_id)->get();

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }
}