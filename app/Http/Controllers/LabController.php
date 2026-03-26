<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LabReport;
use App\Models\MedicalRecord;
use App\Models\DoctorReport;
use App\Models\GrantAccess;
use App\Models\User;
use App\Helpers\Web3Helper;
use Illuminate\Support\Facades\Storage;

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

    public function create(Request $request, $patient_id)
    {
        $patient = User::findOrFail($patient_id);

        $from = $request->query('from'); // optional (for back button)

        return view('lab.write_lab_report', compact('patient', 'from'));
    }

    // Upload lab result
    public function store(Request $request)
    {
        $lab = Auth::user();

        if (!$lab || $lab->role !== 'lab') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'patient_id' => 'required|integer|exists:users,id',
            'test_type' => 'required|string|max:255',
            'result' => 'required|string|max:2000',
            'report_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',

            // signature
            'wallet_address' => 'required|string',
            'signed_message' => 'required|string',
        ]);

        // ✅ Wallet must match logged-in lab
        if (!$lab->wallet_address || strtolower($lab->wallet_address) !== strtolower($validated['wallet_address'])) {
            return response()->json(['message' => 'Wallet mismatch'], 403);
        }

        // ✅ Verify signature
        $message = "Authorize lab report submission";
        $isValid = Web3Helper::verifySignature(
            $message,
            $validated['signed_message'],
            $validated['wallet_address']
        );

        if (!$isValid) {
            return response()->json(['message' => 'Invalid signature'], 401);
        }

        // ✅ Access check
        $hasAccess = GrantAccess::where('authorized_id', $lab->id)
            ->where('patient_id', $validated['patient_id'])
            ->where('status', 'active')
            ->exists();

        if (!$hasAccess) {
            abort(403, 'No access to this patient');
        }

        // ---- store file ----
        $file = $validated['report_file'];
        $patientId = $validated['patient_id'];

        $dir = "medical_records/patients/{$patientId}/lab_reports";

        $storedName = now()->timestamp . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        $storedPath = $file->storeAs($dir, $storedName, 's3');

        $fileHash = hash_file('sha256', $file->getRealPath());

        LabReport::create([
            'lab_id' => $lab->id,
            'patient_id' => $patientId,
            'test_type' => $validated['test_type'],
            'result' => $validated['result'],
            'report_file' => $storedPath,
            'original_filename' => $file->getClientOriginalName(), // ADD THIS
            'file_hash' => $fileHash,
        ]);

        return redirect()->back()->with('success', 'Lab report uploaded successfully');
    }



    // View all reports for a patient
    public function index(Request $request, $patient_id)
    {
        // 🔒 Access check
        $hasAccess = GrantAccess::where('authorized_id', auth()->id())
            ->where('patient_id', $patient_id)
            ->where('role_type', 'lab')
            ->where('status', 'active')
            ->exists();

        if (!$hasAccess) {
            abort(403);
        }

        $patient = User::findOrFail($patient_id);

        $tab = $request->query('tab', 'lab');

        $patientRecords = MedicalRecord::where('patient_id', $patient_id)->latest()->get();
        $doctorReports = DoctorReport::where('patient_id', $patient_id)->latest()->get();
        $labReports = LabReport::where('patient_id', $patient_id)->latest()->get();

        return view('lab.reports', compact(
            'patient',
            'patientRecords',
            'doctorReports',
            'labReports',
            'tab'
        ));
    }

    public function patientDetails($id)
    {
        $hasAccess = GrantAccess::where('authorized_id', Auth::id())
            ->where('patient_id', $id)
            ->where('role_type', 'lab')
            ->where('status', 'active')
            ->exists();

        if (!$hasAccess) {
            abort(403, 'Unauthorized access to patient');
        }

        $patient = User::findOrFail($id);

        $profileUrl = $patient->profile_pic
            ? Storage::disk('s3')->temporaryUrl(
                $patient->profile_pic,
                now()->addMinutes(60)
            )
            : null;

        return view('doctor.patient_details', compact('patient', 'profileUrl'));
    }

    public function edit($id)
    {
        $report = LabReport::findOrFail($id);

        if ($report->lab_id !== auth()->id()) {
            abort(403);
        }

        return view('lab.edit_report', compact('report'));
    }

    public function update(Request $request, $id)
    {
        $report = LabReport::findOrFail($id);

        if ($report->lab_id !== auth()->id()) {
            abort(403);
        }

        $report->test_type = $request->test_type;
        $report->result = $request->result;

        $report->save();

        return redirect()
            ->route('lab.reports', [
                'patient_id' => $report->patient_id,
                'tab' => 'lab'
            ])
            ->with('success', 'Lab report updated');
    }
}