@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Register</h2>
        <form id="registerForm" method="POST">
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
                <label>Phone</label>
                <input type="text" name="phone" required>
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
                    <option value="" disabled selected hidden>Select role</option>
                    <option value="patient">Patient</option>
                    <option value="doctor">Doctor</option>
                    <option value="lab">Lab</option>
                </select>
            </div>
            {{-- Patient extra --}}
            <div id="patientFields" style="display:none; margin-top:10px;">
                <label id="addressLabel">Address</label>
                <input type="text" name="address" id="address_input">
            </div>

            {{-- Doctor extra --}}
            <div id="doctorFields" style="display:none; margin-top:10px;">
                <div>
                    <label>Specialty</label>
                    <input type="text" name="specialty" id="specialty_input">
                </div>
                <div>
                    <label>License Number</label>
                    <input type="text" name="license_number" id="license_input">
                </div>
            </div>
            {{-- Lab and Doctor extra --}}
            <div id="orgField" style="display:none; margin-top:10px;">
                <label>Organization ID</label>
                <input type="text" name="organization_id" id="organization_id_input">
            </div>
            <input type="hidden" name="wallet_address" id="wallet_address_input">
            <input type="hidden" name="signed_message" id="signed_message_input">
            <br><button type="button" id="connectWalletBtn">Connect MetaMask & Register</button>
        </form>
        <p id="errorMsg" style="color:red;"></p>
        <p id="successMsg" style="color:green;"></p>

        <p class="mt-4">
            Already have an account?
            <a href="{{ route('login.form') }}" class="text-blue-600 underline">Login here</a>.
        </p>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                const roleSelect = document.querySelector('select[name="role"]');

                const patientFields = document.getElementById('patientFields');
                const doctorFields = document.getElementById('doctorFields');
                const orgField = document.getElementById('orgField');

                const addressInput = document.getElementById('address_input');
                const addressLabel = document.getElementById('addressLabel');
                const specialtyInput = document.getElementById('specialty_input');
                const orgInput = document.getElementById('organization_id_input');
                const licenseInput = document.getElementById('license_input');

                function hideAll() {
                    patientFields.style.display = 'none';
                    doctorFields.style.display = 'none';
                    orgField.style.display = 'none';

                    addressInput.required = false;
                    specialtyInput.required = false;
                    orgInput.required = false;
                    licenseInput.required = false;
                }

                function showByRole(role) {
                    hideAll();

                    if (role === 'patient') {
                        patientFields.style.display = 'block';
                        addressLabel.textContent = "Address";
                        addressInput.required = true;

                    } else if (role === 'doctor') {
                        doctorFields.style.display = 'block';
                        orgField.style.display = 'block';

                        specialtyInput.required = true;
                        licenseInput.required = true;
                        orgInput.required = true;

                    } else if (role === 'lab') {
                        patientFields.style.display = 'block'; // reuse address
                        orgField.style.display = 'block';

                        addressLabel.textContent = "Lab Address";
                        addressInput.required = true;
                        orgInput.required = true;
                    }
                }

                roleSelect.addEventListener('change', function () {
                    showByRole(this.value);
                });

                showByRole(roleSelect.value);

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
                            body: formData,
                            headers: { "Accept": "application/json" }
                        });

                        const data = await response.json();

                        if (!response.ok) {

                            if (data.errors) {
                                document.getElementById('errorMsg').textContent =
                                    Object.values(data.errors).flat().join("\n");
                            } else {
                                document.getElementById('errorMsg').textContent =
                                    data.message || "Registration failed";
                            }

                            return;
                        }

                        document.getElementById('errorMsg').textContent = '';
                        document.getElementById('successMsg').textContent =
                            'Registered successfully! Redirecting to login...';

                        window.location.href = "/login?registered=1";

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