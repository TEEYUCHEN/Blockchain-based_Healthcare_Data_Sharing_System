<?php

namespace App\Http\Controllers;

use App\Models\GrantAccess;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function patientList()
    {
        // Fetch patients who have granted access to the authenticated doctor
        $patients = GrantAccess::where('authorized_id', Auth::id())
            ->where('role_type', 'doctor')
            ->with('patient')
            ->get();

        return view('doctor.patient_list', compact('patients'));
    }

    public function patientDetails($id)
    {
        // Fetch the patient details by ID
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
        $patient = User::findOrFail($id);

        $records = MedicalRecord::where('patient_id', $id)
            ->latest()
            ->get();

        return view('doctor.patient_reports', compact('patient', 'records'));
    }

    public function writeDiagnosis()
    {
        // get patients that doctor can access
        $patients = User::where('role', 'patient')->get();

        return view('doctor.write_diagnosis', compact('patients'));
    }
    public function submitDiagnosis(Request $request)
    {
        // validate
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'diagnosis' => 'nullable|string',
            'prescription' => 'nullable|string',
            'report_file' => 'nullable|file'
        ]);

        // save logic here (medical_records table or doctor_reports table)

        return redirect()->back()->with('success', 'Diagnosis submitted successfully.');
    }
}