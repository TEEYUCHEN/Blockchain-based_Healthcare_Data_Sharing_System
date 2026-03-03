<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GrantAccess;
use App\Models\User;



class GrantAccessController extends Controller
{
    public function index(Request $request)
    {
        $patient = Auth::user();
        abort_unless($patient && $patient->role === 'patient', 403);

        $tab = $request->get('tab', 'doctor'); // doctor|lab

        $grants = GrantAccess::where('patient_id', $patient->id)
            ->where('role_type', $tab)
            ->latest()
            ->get();

        $authorizedUsers = User::whereIn('id', $grants->pluck('authorized_id'))
            ->get()
            ->keyBy('id');

        return view('patient.grant-access', compact('tab', 'grants', 'authorizedUsers'));
    }

    public function browse(Request $request)
    {
        $patient = Auth::user();
        abort_unless($patient && $patient->role === 'patient', 403);

        $tab = $request->get('tab', 'doctor'); // doctor|lab
        $q = $request->get('q', '');

        $usersQuery = User::where('role', $tab);

        if ($q !== '') {
            $usersQuery->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        $users = $usersQuery->orderBy('name')->paginate(10);
        $users->appends(['tab' => $tab, 'q' => $q]); // keep tab + search in pagination

        $grantedIds = GrantAccess::where('patient_id', $patient->id)
            ->where('role_type', $tab)
            ->pluck('authorized_id')
            ->toArray();

        return view('patient.browse-access', compact('tab', 'q', 'users', 'grantedIds'));
    }

    public function store(Request $request)
    {
        $patient = Auth::user();
        abort_unless($patient && $patient->role === 'patient', 403);

        $request->validate([
            'authorized_id' => 'required|integer',
            'role_type' => 'required|in:doctor,lab',
        ]);

        $exists = GrantAccess::where('patient_id', $patient->id)
            ->where('authorized_id', $request->authorized_id)
            ->where('role_type', $request->role_type)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Access already granted');
        }

        GrantAccess::create([
            'patient_id' => $patient->id,
            'authorized_id' => $request->authorized_id,
            'role_type' => $request->role_type,
        ]);

        return back()->with('success', 'Access granted');
    }

    public function destroy(Request $request)
    {
        $patient = Auth::user();
        abort_unless($patient && $patient->role === 'patient', 403);

        $request->validate([
            'authorized_id' => 'required|integer',
            'role_type' => 'required|in:doctor,lab',
        ]);

        $grant = GrantAccess::where('patient_id', $patient->id)
            ->where('authorized_id', $request->authorized_id)
            ->where('role_type', $request->role_type)
            ->first();

        if (!$grant) {
            return back()->with('error', 'Access not found');
        }

        $grant->delete();

        return back()->with('success', 'Access revoked');
    }
}