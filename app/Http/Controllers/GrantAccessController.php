<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GrantAccess;
use App\Models\User;
use Illuminate\Support\Facades\Storage;


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

    public function show(Request $request, $id)
    {
        $patient = Auth::user();

        // get user
        $user = User::findOrFail($id);

        // check access (IMPORTANT SECURITY)
        $grant = GrantAccess::where([
            'patient_id' => $patient->id,
            'authorized_id' => $id
        ])->first();

        abort_unless($grant, 403);

        return view('patient.access-detail', [
            'user' => $user,
            'profileUrl' => $user->profile_pic
                ? Storage::disk('s3')->temporaryUrl(
                    $user->profile_pic,
                    now()->addMinutes(60)
                )
                : null,
            'grant' => $grant,
            'from' => $request->get('from')
        ]);
    }

    /*
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
    } */

    public function store(Request $request)
    {
        $patient = Auth::user();

        $wallet = $request->authorized_wallet;

        // find user by wallet
        $user = User::where('wallet_address', $wallet)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // prevent duplicate
        GrantAccess::firstOrCreate([
            'patient_id' => $patient->id,
            'authorized_id' => $user->id,
            'role_type' => $request->role_type
        ]);

        return response()->json(['success' => true]);
    }

    /*
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
    } */
    public function destroy(Request $request)
    {
        $patient = Auth::user();

        $wallet = $request->authorized_wallet;

        $user = User::where('wallet_address', $wallet)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        GrantAccess::where([
            'patient_id' => $patient->id,
            'authorized_id' => $user->id,
            'role_type' => $request->role_type
        ])->delete();

        return response()->json(['success' => true]);
    }
}