<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LabReport;
use App\Models\GrantAccess;

class LabReportController extends Controller
{
    // Add lab report
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|integer|exists:users,id',
            'test_type' => 'required|string|max:255',
            'result' => 'required|string',
            'file' => 'nullable|file|max:10240',
        ]);

        $lab = Auth::user();

        // Optional: wallet verification
        if (!$lab->wallet_verified) {
            return response()->json([
                'success' => false,
                'message' => 'Wallet not verified'
            ], 401);
        }

        if (
            !GrantAccess::where('authorized_id', $lab->id)
                ->where('patient_id', $request->patient_id)
                ->where('role_type', 'lab')
                ->where('status', 'active')
                ->exists()
        ) {

            return response()->json([
                'success' => false,
                'message' => 'No access to this patient'
            ], 403);
        }

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('lab-reports', 's3');
        }

        $report = LabReport::create([
            'lab_id' => $lab->id,
            'patient_id' => $request->patient_id,
            'test_type' => $request->test_type,
            'result' => $request->result,
            'file_path' => $filePath,
        ]);

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    // List lab reports for a patient
    public function index($patient_id)
    {
        $user = Auth::user();

        // Only allow if user is patient, or authorized (doctor/lab) with access
        if (
            ($user->role === 'doctor' || $user->role === 'lab') &&
            !GrantAccess::where('authorized_id', $user->id)
                ->where('patient_id', $patient_id)
                ->where('role_type', $user->role)
                ->where('status', 'active')
                ->exists()
        ) {
            return response()->json(['message' => 'No access'], 403);
        }

        $reports = LabReport::where('patient_id', $patient_id)->get();

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }
}