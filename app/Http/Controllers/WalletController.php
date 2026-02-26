<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Web3Helper;
class WalletController extends Controller
{
    // Verify wallet signature
    public function verifySignature(Request $request)
    {
        $request->validate([
            'wallet_address' => 'required|string',
            'signed_message' => 'required|string',
        ]);

        $user = Auth::user();

        // TODO: verify signed_message with wallet_address
        $isValid = $this->verifyWalletSignature(
            $request->wallet_address,
            $request->signed_message,
            $request->message ?? 'Login to Healthcare Data Sharing System'
        );

        if (!$isValid) {
            return response()->json([
                'success' => false,
                'message' => 'Wallet verification failed'
            ], 401);
        }

        $user->wallet_address = $request->wallet_address;
        $user->wallet_verified = true;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Wallet verified successfully'
        ]);
    }

    // Example helper (you will implement actual signature verification)
    protected function verifyWalletSignature($wallet, $signature, $message)
    {
        return Web3Helper::verifySignature($message, $signature, $wallet);
    }
}