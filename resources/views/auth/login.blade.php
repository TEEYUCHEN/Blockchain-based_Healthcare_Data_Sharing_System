@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Login</h2>
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

            <button type="button" id="connectWalletBtn">Connect MetaMask & Login</button>
        </form>

        <p id="errorMsg" style="color:red;"></p>

        <p class="mt-4">
            Don't have an account yet?
            <a href="{{ route('register.form') }}" class="text-blue-600 underline">Register here</a>.
        </p>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const connectWallet = async () => {
                    try {
                        if (!window.wallet) {
                            alert('Wallet module not loaded');
                            return;
                        }

                        const message = "Login to Healthcare DApp";
                        const { address, signature } = await window.wallet.sign(message);

                        document.getElementById('wallet_address_input').value = address;
                        document.getElementById('signed_message_input').value = signature;

                        // Submit the form (will trigger Laravel controller)
                        document.getElementById('loginForm').submit();

                    } catch (err) {
                        console.error(err);
                        alert(err.message || 'An error occurred');
                    }
                };

                document.getElementById('connectWalletBtn')
                    .addEventListener('click', connectWallet);
            });
        </script>
    @endpush
@endsection