<?php

namespace App\Http\Controllers;

use App\Models\GrantAccess;
use App\Models\User;
use App\Models\MedicalRecord;
use App\Models\DoctorReport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\LabReport;
use App\Helpers\Web3Helper;

class DoctorController extends Controller
{

    public function create(Request $request, $patient_id)
    {
        $patient = User::findOrFail($patient_id);
        $from = $request->query('from'); // get source

        return view('doctor.write_diagnosis', compact('patient', 'from'));
    }

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


    public function patientReports(Request $request, $id)
    {
        $hasAccess = GrantAccess::where('authorized_id', Auth::id())
            ->where('patient_id', $id)
            ->where('role_type', 'doctor')
            ->where('status', 'active')
            ->exists();

        if (!$hasAccess) {
            abort(403);
        }

        $patient = User::findOrFail($id);

        $tab = $request->query('tab', 'patient'); // default tab

        $patientRecords = MedicalRecord::where('patient_id', $id)->latest()->get();
        $doctorReports = DoctorReport::with('doctor')->where('patient_id', $id)->latest()->get();
        $labReports = LabReport::with('lab')->where('patient_id', $id)->latest()->get();

        return view('doctor.patient_reports', compact(
            'patient',
            'patientRecords',
            'doctorReports',
            'labReports',
            'tab'
        ));
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
            'diagnosis' => 'required|string',
            'prescription' => 'required|string',
            'report_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',

            'wallet_address' => 'required|string',
            'signed_message' => 'required|string',
        ]);

        // Wallet check
        if (!$doctor->wallet_address || strtolower($doctor->wallet_address) !== strtolower($validated['wallet_address'])) {
            return back()->withErrors('Wallet mismatch');
        }

        // Signature check
        $message = "Authorize doctor diagnosis submission for patient #{$validated['patient_id']}";

        $isValid = Web3Helper::verifySignature(
            $message,
            $validated['signed_message'],
            $validated['wallet_address']
        );

        if (!$isValid) {
            return back()->withErrors('Invalid wallet signature');
        }

        // Access check
        $hasAccess = GrantAccess::where('authorized_id', $doctor->id)
            ->where('patient_id', $validated['patient_id'])
            ->where('status', 'active')
            ->exists();

        if (!$hasAccess) {
            abort(403);
        }

        // File upload
        $file = $request->file('report_file');
        $patientId = $validated['patient_id'];

        $dir = "medical_records/patients/{$patientId}/doctor_reports";

        $storedName = now()->timestamp . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        $storedPath = $file->storeAs($dir, $storedName, 's3');

        if (!$storedPath) {
            return back()->withErrors('Upload failed');
        }

        $fileHash = hash_file('sha256', $file->getRealPath());

        DoctorReport::create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patientId,
            'diagnosis' => $validated['diagnosis'],
            'prescription' => $validated['prescription'],
            'report_file' => $storedPath,
            'original_filename' => $file->getClientOriginalName(),
            'file_hash' => $fileHash,
        ]);

        return redirect()->back()->with('success', 'Diagnosis submitted successfully.');
    }

    public function edit($id)
    {
        $report = DoctorReport::findOrFail($id);

        // 🔐 Ownership check
        if ($report->doctor_id !== auth()->id()) {
            abort(403);
        }

        return view('doctor.edit_report', compact('report'));
    }

    public function update(Request $request, $id)
    {
        $report = DoctorReport::findOrFail($id);

        if ($report->doctor_id !== auth()->id()) {
            abort(403);
        }

        $report->diagnosis = $request->diagnosis;
        $report->prescription = $request->prescription;

        $report->save();

        return redirect()
            ->route('doctor.patient_reports', [
                'id' => $report->patient_id,
                'tab' => 'doctor'
            ])
            ->with('success', 'Report updated');
    }
}