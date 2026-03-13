@extends('layouts.app')

@section('content')
    <div class="container">

        <h2>Write Diagnosis</h2>

        <div style="margin-top: 15px;">
            <a href="{{ route('dashboard') }}">
                <button type="button" class="btn btn-secondary">← Back to Dashboard</button>
            </a>
        </div>

        @if(session('success'))
            <p style="color:green">{{ session('success') }}</p>
        @endif

        <form method="POST" action="{{ route('doctor.submit_diagnosis') }}" enctype="multipart/form-data"
            id="diagnosisForm">
            @csrf

            <!-- Patient selection -->
            <div class="form-group">
                <label for="patient_id">Select Patient</label>

                <select name="patient_id" id="patient_id" class="form-control" required>

                    <option value="">-- Select Patient --</option>

                    @foreach($patients as $grant)
                        <option value="{{ $grant->patient->id }}">
                            {{ $grant->patient->name }}
                        </option>
                    @endforeach

                </select>
            </div>


            <!-- Diagnosis -->
            <div class="form-group">
                <label for="diagnosis">Diagnosis</label>

                <textarea name="diagnosis" id="diagnosis" class="form-control" rows="4"
                    placeholder="Enter diagnosis"></textarea>
            </div>


            <!-- Prescription -->
            <div class="form-group">
                <label for="prescription">Prescription</label>

                <textarea name="prescription" id="prescription" class="form-control" rows="4"
                    placeholder="Enter prescription"></textarea>
            </div>


            <!-- File upload -->
            <div class="form-group">
                <label for="report_file">Upload Report File</label>

                <input type="file" name="report_file" id="report_file" class="form-control-file"
                    accept=".pdf,.jpg,.jpeg,.png">
            </div>


            <!-- Wallet verification -->
            <input type="hidden" name="wallet_address" id="wallet_address_input">
            <input type="hidden" name="signed_message" id="signed_message_input">


            <button type="submit" class="btn btn-primary">
                Sign with MetaMask & Submit
            </button>

        </form>

    </div>


    @push('scripts')

        <script>

            document.addEventListener('DOMContentLoaded', function () {

                const form = document.getElementById('diagnosisForm');

                form.addEventListener('submit', async function (e) {

                    e.preventDefault();

                    try {

                        if (!window.wallet) {
                            alert("Wallet module not loaded");
                            return;
                        }

                        const message = "Authorize doctor diagnosis submission";

                        const { address, signature } = await window.wallet.sign(message);

                        document.getElementById('wallet_address_input').value = address;
                        document.getElementById('signed_message_input').value = signature;

                        form.submit();

                    } catch (err) {

                        console.error(err);
                        alert(err.message || "MetaMask signing failed");

                    }

                });

            });

        </script>

    @endpush

@endsection