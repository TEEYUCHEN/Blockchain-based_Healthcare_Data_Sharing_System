@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Login</h2>

        {{-- Show Laravel validation errors --}}
        @if ($errors->any())
            <div style="color:red; margin-bottom:10px;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Show controller errors --}}
        @if(session('error'))
            <div style="color:red; margin-bottom:10px;">
                {{ session('error') }}
            </div>
        @endif

        <form id="loginForm" method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div>
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <div>
                <label>Role</label>
                <select name="role" required>
                    <option value="" disabled selected hidden>Select role</option>
                    <option value="patient">Patient</option>
                    <option value="doctor">Doctor</option>
                    <option value="lab">Lab</option>
                </select>
            </div>

            <input type="hidden" name="wallet_address" id="wallet_address_input">
            <input type="hidden" name="signed_message" id="signed_message_input">

            <button type="button" id="connectWalletBtn">
                Connect MetaMask & Login
            </button>
        </form>

        <p class="mt-4">
            Don't have an account yet?
            <a href="{{ route('register.form') }}">Register here</a>
        </p>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                const connectWalletBtn = document.getElementById('connectWalletBtn');

                connectWalletBtn.addEventListener('click', async function () {

                    try {

                        if (!window.wallet) {
                            alert("Wallet module not loaded");
                            return;
                        }

                        const message = "Login to Healthcare DApp";

                        const { address, signature } = await window.wallet.sign(message);

                        console.log("Wallet address:", address);
                        console.log("Signature:", signature);

                        document.getElementById('wallet_address_input').value = address;
                        document.getElementById('signed_message_input').value = signature;

                        document.getElementById('loginForm').submit();

                    } catch (err) {

                        console.error(err);
                        alert(err.message || "MetaMask signing failed");

                    }

                });

            });
        </script>
    @endpush

@endsection