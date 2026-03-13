<?php

namespace App\Http\Controllers;

use App\Models\GrantAccess;
use App\Models\User;
use App\Models\MedicalRecord;
use App\Models\DoctorReport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DoctorController extends Controller
{

    public function patientList()
    {
        // Patients who granted access to this doctor
        $patients = GrantAccess::where('authorized_id', Auth::id())
            ->where('role_type', 'doctor')
            ->where('status', 'active')
            ->with('patient')
            ->get();

        return view('doctor.patient_list', compact('patients'));
    }


    public function patientDetails($id)
    {
        // Check doctor access
        $hasAccess = GrantAccess::where('authorized_id', Auth::id())
            ->where('patient_id', $id)
            ->where('role_type', 'doctor')
            ->where('status', 'active')
            ->exists();

        if (!$hasAccess) {
            abort(403, 'Unauthorized access to patient');
        }

        $patient = User::findOrFail($id);

        return view('doctor.patient_details', compact('patient'));
    }


    public function viewPatientReports()
    {
        $reports = GrantAccess::where('authorized_id', Auth::id())
            ->where('role_type', 'doctor')
            ->where('status', 'active')
            ->with('patient')
            ->get();

        return view('doctor.view_patient_reports', compact('reports'));
    }


    public function patientReports($id)
    {
        // Check doctor access
        $hasAccess = GrantAccess::where('authorized_id', Auth::id())
            ->where('patient_id', $id)
            ->where('role_type', 'doctor')
            ->where('status', 'active')
            ->exists();

        if (!$hasAccess) {
            abort(403, 'Unauthorized access to patient reports');
        }

        $patient = User::findOrFail($id);

        // Patient uploaded records
        $records = MedicalRecord::where('patient_id', $id)
            ->latest()
            ->get();

        return view('doctor.patient_reports', compact('patient', 'records'));
    }


    public function writeDiagnosis()
    {
        // Only patients who granted access
        $patients = GrantAccess::where('authorized_id', Auth::id())
            ->where('role_type', 'doctor')
            ->where('status', 'active')
            ->with('patient')
            ->get();

        return view('doctor.write_diagnosis', compact('patients'));
    }


    public function submitDiagnosis(Request $request)
    {
        $doctor = Auth::user();

        $validated = $request->validate([
            'patient_id' => 'required|exists:users,id',
            'diagnosis' => 'nullable|string',
            'prescription' => 'nullable|string',
            'report_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240'
        ]);

        // Verify doctor has permission
        $hasAccess = GrantAccess::where('authorized_id', $doctor->id)
            ->where('patient_id', $validated['patient_id'])
            ->where('role_type', 'doctor')
            ->where('status', 'active')
            ->exists();

        if (!$hasAccess) {
            abort(403, 'You are not authorized for this patient');
        }

        $storedPath = null;

        if ($request->hasFile('report_file')) {

            $file = $request->file('report_file');

            $patientId = $validated['patient_id'];

            // Consistent storage structure
            $dir = "medical_records/patients/{$patientId}/doctor_reports";

            $storedName = now()->format('Ymd_His') . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            $storedPath = $file->storeAs($dir, $storedName, 's3');
        }

        DoctorReport::create([
            'doctor_id' => $doctor->id,
            'patient_id' => $validated['patient_id'],
            'diagnosis' => $validated['diagnosis'],
            'prescription' => $validated['prescription'],
            'report_file' => $storedPath
        ]);

        return redirect()->back()->with('success', 'Diagnosis submitted successfully.');
    }
}