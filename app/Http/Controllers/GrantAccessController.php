<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GrantAccess;

class GrantAccessController extends Controller
{
    // Grant access to doctor or lab
    public function store(Request $request)
    {
        $request->validate([
            'authorized_id' => 'required|integer',
            'role_type' => 'required|in:doctor,lab',
        ]);

        $patient = Auth::user();

        // Check if already granted
        if (
            GrantAccess::where('patient_id', $patient->id)
                ->where('authorized_id', $request->authorized_id)
                ->where('role_type', $request->role_type)->exists()
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Access already granted'
            ], 409);
        }

        GrantAccess::create([
            'patient_id' => $patient->id,
            'authorized_id' => $request->authorized_id,
            'role_type' => $request->role_type,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Access granted'
        ]);
    }

    // Revoke access
    public function destroy(Request $request)
    {
        $patient = Auth::user();

        $grant = GrantAccess::where('patient_id', $patient->id)
            ->where('authorized_id', $request->authorized_id)
            ->where('role_type', $request->role_type)->first();

        if (!$grant) {
            return response()->json([
                'success' => false,
                'message' => 'Access not found'
            ], 404);
        }

        $grant->delete();

        return response()->json([
            'success' => true,
            'message' => 'Access revoked'
        ]);
    }
}