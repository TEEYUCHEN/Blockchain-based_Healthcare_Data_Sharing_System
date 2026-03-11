<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MedicalRecordController extends Controller
{
    // List records page
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'patient') {
            $records = MedicalRecord::where('patient_id', $user->id)->latest()->get();
            return view('patient.index', compact('records'));
        }


        $records = collect(); // placeholder until grant logic is added
        return view('patient.index', compact('records'));
    }

    // Optional details page
    public function show(MedicalRecord $medicalRecord)
    {
        $this->authorizeView($medicalRecord);
        return view('records.show', compact('medicalRecord'));
    }

    // Download file from Backblaze (s3)
    public function download(MedicalRecord $medicalRecord)
    {
        $user = Auth::user();

        // Patient can only download their own records
        if (!$user || $medicalRecord->patient_id !== $user->id) {
            abort(403);
        }

        $disk = Storage::disk('s3');
        $path = $medicalRecord->stored_path;

        if (!$disk->exists($path)) {
            abort(404);
        }

        $stream = $disk->readStream($path);

        return response()->streamDownload(function () use ($stream) {
            fpassthru($stream);
        }, $medicalRecord->original_filename);
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