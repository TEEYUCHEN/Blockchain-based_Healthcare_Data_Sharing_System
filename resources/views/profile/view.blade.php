@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Personal Profile</h2>
        <div style="margin-bottom: 15px;">
            <a href="{{ route('dashboard') }}">
                <button type="button">← Back to Dashboard</button>
            </a>
        </div>

        <!-- Profile Picture Display -->
        <img src="{{ $profileUrl ?? asset('images/default-profile.png') }}" alt="Profile Picture"
            style="width:150px;height:150px;border-radius:50%;object-fit:cover;"
            onerror="this.onerror=null;this.src='{{ asset('images/default-profile.png') }}';">

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="profileForm">
            @csrf
            @method('PUT')

            @if($user->role === 'patient' || $user->role === 'doctor')
                <div style="margin-bottom: 15px;">
                    <input type="file" name="profile_pic" id="profile_pic">
                </div>
            @endif


            <div style="margin-bottom: 15px;">
                <label for="name">Name:</label><br>
                <input type="text" name="name" id="name" value="{{ $user->name }}" required>
            </div>


            <div style="margin-bottom: 15px;">
                <label for="email">Email:</label><br>
                <input type="email" name="email" id="email" value="{{ $user->email }}" required>
            </div>


            @if($user->role === 'patient' || $user->role === 'doctor')
                <div style="margin-bottom: 15px;">
                    <label for="phone">Phone:</label><br>
                    <input type="text" name="phone" id="phone" value="{{ $user->phone }}">
                </div>
            @endif

            @if($user->role === 'patient' || $user->role === 'lab')
                <div>
                    <label>Address</label>
                    <input type="text" name="address" value="{{ $user->address }}">
                </div>
            @endif

            @if($user->role === 'doctor')

                <div>
                    <label>License Number</label>
                    <input type="text" name="license_number" value="{{ $user->license_number }}">
                </div>

                <div>
                    <label>Specialty</label>
                    <input type="text" name="specialty" value="{{ $user->specialty }}">
                </div>

            @endif

            @if($user->role === 'doctor' || $user->role === 'lab')
                <div style="margin-bottom: 15px;">
                    <label>Organization</label><br>
                    <input type="text" name="organization_id" value="{{ $user->organization_id }}">
                </div>
            @endif



            <input type="hidden" name="wallet_address" id="wallet_address_input">
            <input type="hidden" name="signed_message" id="signed_message_input">

            <button type="submit" id="saveButton" disabled>Update Profile</button>
        </form>

        <script>
            const form = document.getElementById('profileForm');
            const saveButton = document.getElementById('saveButton');
            const inputs = form.querySelectorAll('input');

            let initialData = {};
            inputs.forEach(input => {
                if (input.type !== 'file') {
                    initialData[input.name] = input.value;
                }
            });

            form.addEventListener('input', () => {
                let isChanged = false;
                inputs.forEach(input => {
                    if (input.type === 'file' && input.files.length > 0) {
                        isChanged = true;
                    } else if (input.type !== 'file' && input.value !== initialData[input.name]) {
                        isChanged = true;
                    }
                });
                saveButton.disabled = !isChanged;
            });
        </script>

        @push('scripts')
            <script>

                document.addEventListener('DOMContentLoaded', function () {

                    const form = document.getElementById('profileForm');

                    form.addEventListener('submit', async function (e) {

                        e.preventDefault(); // stop normal submit

                        try {

                            if (!window.wallet) {
                                alert("Wallet module not loaded");
                                return;
                            }

                            const message = "Authorize profile update";

                            const { address, signature } = await window.wallet.sign(message);

                            console.log("Wallet:", address);
                            console.log("Signature:", signature);

                            document.getElementById('wallet_address_input').value = address;
                            document.getElementById('signed_message_input').value = signature;

                            form.submit(); // submit after signing

                        } catch (err) {

                            console.error(err);
                            alert(err.message || "MetaMask signing failed");

                        }

                    });

                });
            </script>
        @endpush
    </div>
@endsection