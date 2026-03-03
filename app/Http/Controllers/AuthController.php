<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Web3Helper;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|string|max:20',
                'password' => 'required|confirmed|min:6',
                'role' => 'required|in:patient,doctor,lab',
                'wallet_address' => 'required|string',
                'signed_message' => 'required|string',

                // conditional fields
                'address' => 'nullable|required_if:role,patient|string|max:255',
                'specialty' => 'nullable|required_if:role,doctor|string|max:255',
                'organization_id' => 'nullable|required_if:role,doctor,lab|string|max:100',
                'license_number' => 'nullable|required_if:role,doctor|string|max:100',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        // ✅ Verify wallet signature BEFORE marking verified
        $isWalletValid = Web3Helper::verifySignature(
            'Verify your wallet for Healthcare DApp',
            $validated['signed_message'],
            $validated['wallet_address']
        );

        if (!$isWalletValid) {
            return response()->json([
                'message' => 'Wallet signature invalid',
            ], 401);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],

            // extras (nullable columns in DB)
            'address' => $validated['address'] ?? null,
            'specialty' => $validated['specialty'] ?? null,
            'organization_id' => $validated['organization_id'] ?? null,
            'license_number' => $validated['license_number'] ?? null,

            'wallet_address' => $validated['wallet_address'],
            'wallet_verified' => true,
        ]);

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
            'wallet_address' => 'required|string',
            'signed_message' => 'required|string',
        ]);

        // 1️⃣ Verify wallet signature
        $isWalletValid = Web3Helper::verifySignature(
            'Login to Healthcare DApp',
            $request->signed_message,
            $request->wallet_address
        );

        if (!$isWalletValid) {
            return redirect()->back()->with('error', 'Wallet signature invalid');
        }

        // 2️⃣ Check email + password + wallet match
        $user = User::where('email', $request->email)
            ->where('wallet_address', $request->wallet_address)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return redirect()->back()->with('error', 'Wrong email, password, or wallet address');
        }

        // 3️⃣ Login the user
        Auth::login($user);

        // 4️⃣ Redirect to dashboard based on role (optional)
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
