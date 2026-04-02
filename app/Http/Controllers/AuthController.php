<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Web3Helper;
use Illuminate\Database\QueryException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:patient,doctor,lab',
            'address' => 'nullable|required_if:role,patient|string|max:255',
            'specialty' => 'nullable|required_if:role,doctor|string|max:255',
            'organization_id' => 'nullable|required_if:role,doctor,lab|integer|exists:organizations,id',
            'license_number' => 'nullable|required_if:role,doctor|string|max:255',
            'wallet_address' => 'required|string|size:42|unique:users,wallet_address',
            'signed_message' => 'required|string',
        ]);

        // ✅ Verify wallet FIRST
        $isWalletValid = Web3Helper::verifySignature(
            'Verify your wallet for Healthcare DApp',
            $validated['signed_message'],
            $validated['wallet_address']
        );

        if (!$isWalletValid) {
            return response()->json([
                'message' => 'Failed register: Invalid wallet signature',
            ], 401);
        }

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'address' => $validated['address'] ?? null,
                'specialty' => $validated['specialty'] ?? null,
                'organization_id' => $validated['organization_id'] ?? null,
                'license_number' => $validated['license_number'] ?? null,
                'wallet_address' => $validated['wallet_address'],
                'wallet_verified' => true,
            ]);
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'users_email_unique')) {
                $msg = 'Email already used';
            } elseif (str_contains($e->getMessage(), 'users_wallet_address_unique')) {
                $msg = 'Wallet already used';
            } else {
                $msg = 'Database error';
            }

            return response()->json([
                'message' => 'Failed register: ' . $msg,
            ], 422);
        }

        return response()->json([
            'user' => $user,
            'token' => $user->createToken('auth-token')->plainTextToken,
        ], 201);
    }

    public function userInfo(Request $request)
    {
        return response()->json($request->user());
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'role' => 'required|in:patient,doctor,lab',
            'wallet_address' => 'required|string',
            'signed_message' => 'required|string',
        ]);

        $user = User::where('email', $request->email)
            ->where('role', $request->role)
            ->where('wallet_address', $request->wallet_address)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return redirect()->back()->with('error', 'Wrong credentials');
        }

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        // Delete API token if it exists
        if ($request->user() && $request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }

        // Log out the session
        Auth::logout();

        // Redirect to login page
        return redirect()->route('login.form');
    }
}
