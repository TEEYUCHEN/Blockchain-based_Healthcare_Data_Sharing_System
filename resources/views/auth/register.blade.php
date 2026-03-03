@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Register</h2>
        <form id="registerForm">
            @csrf
            <div>
                <label>Name</label>
                <input type="text" name="name" required>
            </div>
            <div>
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div>
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div>
                <label>Confirm Password</label>
                <input type="password" name="password_confirmation" required>
            </div>
            <div>
                <label>Role</label>
                <select name="role" required>
                    <option value="">Select role</option>
                    <option value="patient">Patient</option>
                    <option value="doctor">Doctor</option>
                    <option value="lab">Lab</option>
                </select>
            </div>
            <input type="hidden" name="wallet_address" id="wallet_address_input">
            <input type="hidden" name="signed_message" id="signed_message_input">
            <button type="button" id="connectWalletBtn">Connect MetaMask & Register</button>
        </form>
        <p id="errorMsg" style="color:red;"></p>

        <p class="mt-4">
            Already have an account?
            <a href="{{ route('login.form') }}" class="text-blue-600 underline">Login here</a>.
        </p>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                const connectWallet = async () => {
                    try {

                        if (!window.wallet) {
                            document.getElementById('errorMsg').textContent = 'Wallet module not loaded';
                            return;
                        }

                        const message = "Verify your wallet for Healthcare DApp";

                        // 🔥 Use reusable wallet helper
                        const { address, signature } =
                            await window.wallet.sign(message);

                        document.getElementById('wallet_address_input').value = address;
                        document.getElementById('signed_message_input').value = signature;

                        const formData = new FormData(document.getElementById('registerForm'));
                        const response = await fetch("/api/register", {
                            method: 'POST',
                            body: formData
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            document.getElementById('errorMsg').textContent =
                                data.message || JSON.stringify(data);
                            return;
                        }

                        alert('Registered successfully!');
                        window.location.href = '/login';

                    } catch (err) {
                        console.error(err);
                        document.getElementById('errorMsg').textContent =
                            err.message || 'An error occurred';
                    }
                };

                document.getElementById('connectWalletBtn')
                    .addEventListener('click', connectWallet);
            });
        </script>
    @endpush
@endsection