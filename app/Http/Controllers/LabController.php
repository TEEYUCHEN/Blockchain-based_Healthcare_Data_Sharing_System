<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LabReport;
use App\Models\MedicalRecord;
use App\Models\DoctorReport;
use App\Models\GrantAccess;
use App\Models\User;

class LabController extends Controller
{

    // List patients who granted access to this lab
    public function patients()
    {
        $lab = Auth::user();

        $patients = GrantAccess::with('patient')
            ->where('authorized_id', $lab->id)
            ->where('role_type', 'lab')
            ->where('status', 'active')
            ->get();

        return view('lab.patient_list', compact('patients'));
    }



    // Upload lab result
    public function store(Request $request)
    {
        $lab = Auth::user();

        if (!$lab || $lab->role !== 'lab') {
            abort(403);
        }

        $validated = $request->validate([
            'patient_id' => 'required|integer|exists:users,id',
            'test_type' => 'required|string|max:255',
            'result' => 'required|string|max:2000',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        // Check permission
        $hasAccess = GrantAccess::where('authorized_id', $lab->id)
            ->where('patient_id', $validated['patient_id'])
            ->where('role_type', 'lab')
            ->where('status', 'active')
            ->exists();

        if (!$hasAccess) {
            abort(403, 'No access to this patient');
        }


        // ---- store file ----
        $file = $validated['file'];
        $patientId = $validated['patient_id'];

        $dir = "medical_records/patients/{$patientId}/lab_reports";

        $storedName = now()->format('Ymd_His') . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        $storedPath = $file->storeAs($dir, $storedName, 's3');


        LabReport::create([
            'lab_id' => $lab->id,
            'patient_id' => $patientId,
            'test_type' => $validated['test_type'],
            'result' => $validated['result'],
            'report_file' => $storedPath,
        ]);


        return redirect()->back()->with('success', 'Lab report uploaded successfully');
    }



    // View all reports for a patient
    public function index($patient_id)
    {
        $user = Auth::user();

        // Patient can view their own reports
        if ($user->role === 'patient' && $user->id == $patient_id) {
            $hasAccess = true;
        } else {
            $hasAccess = GrantAccess::where('authorized_id', $user->id)
                ->where('patient_id', $patient_id)
                ->where('role_type', $user->role)
                ->where('status', 'active')
                ->exists();
        }

        if (!$hasAccess) {
            abort(403);
        }


        $labReports = LabReport::where('patient_id', $patient_id)->latest()->get();

        $doctorReports = DoctorReport::where('patient_id', $patient_id)->latest()->get();

        $patientRecords = MedicalRecord::where('patient_id', $patient_id)->latest()->get();


        return view('lab.reports', compact(
            'labReports',
            'doctorReports',
            'patientRecords'
        ));
    }
}