<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Helpers\Web3Helper;

class PatientUploadController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'patient') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'category' => 'nullable|string|max:50',
            'record' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',

            // ✅ signature fields
            'wallet_address' => 'required|string',
            'signed_message' => 'required|string',
        ]);

        // ✅ Must match user's wallet
        if (!$user->wallet_address || strtolower($user->wallet_address) !== strtolower($validated['wallet_address'])) {
            return response()->json(['message' => 'Wallet does not match logged-in user'], 403);
        }

        // ✅ Verify signature for this action
        $message = "Upload medical record"; // must match frontend exactly
        $isValid = Web3Helper::verifySignature($message, $validated['signed_message'], $validated['wallet_address']);

        if (!$isValid) {
            return response()->json(['message' => 'Wallet signature invalid'], 401);
        }

        // ---- store file ----
        $file = $validated['record'];
        $patientId = $user->id;
        $dir = "medical_records/patients/{$patientId}/patient_uploads";
        $storedName = now()->format('Ymd_His') . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $storedPath = $file->storeAs($dir, $storedName, 's3');

        $fileHash = hash_file('sha256', $file->getRealPath());

        $record = MedicalRecord::create([
            'patient_id' => $patientId,
            'uploaded_by_user_id' => $user->id,
            'uploaded_by_role' => 'patient',
            'title' => $validated['title'] ?? null,
            'description' => $validated['description'] ?? null,
            'category' => $validated['category'] ?? null,
            'original_filename' => $file->getClientOriginalName(),
            'stored_path' => $storedPath,
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'file_hash' => $fileHash,
            'blockchain_tx_hash' => null,
            'verification_status' => 'unverified',
        ]);

        return response()->json([
            'message' => 'Uploaded successfully (signed)',
            'record' => $record,
        ], 201);
    }

    public function index()
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'patient') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $records = MedicalRecord::where('patient_id', $user->id)
            ->latest()
            ->get();

        return response()->json(['records' => $records]);
    }

    public function destroy(MedicalRecord $medicalRecord)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'patient') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($medicalRecord->patient_id !== $user->id || $medicalRecord->uploaded_by_role !== 'patient') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        Storage::disk('s3')->delete($medicalRecord->stored_path);
        $medicalRecord->delete();

        return response()->json(['message' => 'Deleted']);
    }
}