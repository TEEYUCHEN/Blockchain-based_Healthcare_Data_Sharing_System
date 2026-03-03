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
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:lab,doctor,patient',
            'phone' => 'nullable|string|max:20',
            'specialty' => 'nullable|string|max:100',
            'license_number' => 'nullable|unique:users', // for doctors
            'wallet_address' => 'required|string|unique:users', // for doctors/labs
            'signed_message' => 'nullable|string', // MetaMask signature
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'specialty' => $request->specialty,
            'license_number' => $request->license_number,
            'wallet_address' => $request->wallet_address,
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
