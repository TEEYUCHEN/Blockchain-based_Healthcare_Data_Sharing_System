<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\GrantAccess;
use App\Models\DoctorReport;
use App\Models\LabReport;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    // List records page
    public function index(Request $request)
    {
        $user = Auth::user();

        $tab = $request->query('tab', 'patient');

        $records = MedicalRecord::where('patient_id', $user->id)
            ->when($tab, function ($query) use ($tab) {
                return $query->where('uploaded_by_role', $tab);
            })
            ->latest()
            ->get();

        return view('patient.index', compact('records', 'tab'));
    }

    // Optional details page
    public function show(MedicalRecord $medicalRecord)
    {
        $this->authorizeView($medicalRecord);
        return view('records.show', compact('medicalRecord'));
    }

    private function getRecordData($type, $id)
    {
        switch ($type) {
            case 'medical':
                $record = MedicalRecord::findOrFail($id);
                return [
                    'path' => $record->stored_path,
                    'patient_id' => $record->patient_id,
                    'filename' => $record->original_filename ?? 'medical_record'
                ];

            case 'doctor':
                $record = DoctorReport::findOrFail($id);
                return [
                    'path' => $record->report_file,
                    'patient_id' => $record->patient_id,
                    'filename' => 'doctor_report_' . $id
                ];

            case 'lab':
                $record = LabReport::findOrFail($id);
                return [
                    'path' => $record->report_file,
                    'patient_id' => $record->patient_id,
                    'filename' => 'lab_report_' . $id
                ];

            default:
                abort(404, 'Invalid file type');
        }
    }

    private function checkAccess($patientId)
    {
        $user = Auth::user();

        if ($user->role === 'patient') {
            if ($user->id !== $patientId) {
                abort(403, 'Unauthorized');
            }
        } elseif (in_array($user->role, ['doctor', 'lab'])) {

            $hasAccess = GrantAccess::where('authorized_id', $user->id)
                ->where('patient_id', $patientId)
                ->where('status', 'active')
                ->exists();

            if (!$hasAccess) {
                abort(403, 'Access denied');
            }
        } else {
            abort(403, 'Invalid role');
        }
    }

    public function view($type, $id)
    {
        $data = $this->getRecordData($type, $id);

        $this->checkAccess($data['patient_id']);

        $disk = Storage::disk('s3');

        if (!$disk->exists($data['path'])) {
            abort(404, 'File not found');
        }

        // VIEW (no forced download)
        $url = $disk->temporaryUrl($data['path'], now()->addMinutes(5));

        return redirect($url);
    }

    // Download file from Backblaze (s3)
    public function download($type, $id)
    {
        if ($type === 'doctor') {
            $report = DoctorReport::findOrFail($id);

            return Storage::disk('s3')->download(
                $report->report_file,
                $report->original_filename ?? 'doctor_report.pdf'
            );
        }

        if ($type === 'lab') {
            $report = LabReport::findOrFail($id);

            return Storage::disk('s3')->download(
                $report->report_file,
                $report->original_filename ?? 'lab_report.pdf'
            );
        }

        if ($type === 'medical') {
            $record = MedicalRecord::findOrFail($id);

            return Storage::disk('s3')->download(
                $record->stored_path,
                $record->original_filename ?? 'medical_record.pdf'
            );
        }

        abort(404);
    }

    // Basic authorization (patient owns OR doctor/lab granted)
    private function authorizeView(MedicalRecord $medicalRecord): void
    {
        $user = Auth::user();

        // Patient only sees their own
        if ($user->role === 'patient') {
            abort_if($medicalRecord->patient_id !== $user->id, 403);
            return;
        }

        // Doctor/lab access check (placeholder)
        // abort_unless(GrantAccess::where('patient_id',$medicalRecord->patient_id)
        //     ->where('authorized_id',$user->id)->exists(), 403);

        // TEMP: block until you implement grant check
        abort(403);
    }

    public function delete($id)
    {
        $record = MedicalRecord::findOrFail($id);

        // Optional: check ownership
        if ($record->patient_id !== auth()->id()) {
            abort(403, "Unauthorized");
        }

        $record->delete();

        return response()->json([
            'message' => 'Record deleted successfully'
        ]);
    }
}