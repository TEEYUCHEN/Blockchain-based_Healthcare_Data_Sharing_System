@extends('layouts.app')

@section('content')
    <div class="container">

        <h2>Write Diagnosis</h2>

        <div style="margin-top: 15px;">
            @if($from == 'patient_list')
                <a href="{{ route('doctor.patient_list') }}">
                    <button class="btn btn-secondary">← Back to patient list</button>
                </a>

            @elseif($from == 'patient_reports')
                <a href="{{ route('doctor.patient_reports', ['id' => $patient->id]) }}">
                    <button class="btn btn-secondary">← Back to patient report</button>
                </a>

            @else
                <a href="{{ route('dashboard') }}">
                    <button class="btn btn-secondary">← Back to Dashboard</button>
                </a>
            @endif
        </div>

        @if(session('success'))
            <p style="color:green">{{ session('success') }}</p>
        @endif

        <form method="POST" action="{{ route('doctor.submit_diagnosis') }}" enctype="multipart/form-data"
            id="diagnosisForm">
            @csrf

            <!-- Patient selection -->
            <div class="form-group">
                <input type="hidden" name="patient_id" value="{{ $patient->id }}">

                <p><strong>Patient:</strong> {{ $patient->name }}</p>
            </div>


            <!-- Diagnosis -->
            <div class="form-group">
                <label for="diagnosis">Diagnosis</label>

                <textarea name="diagnosis" id="diagnosis" class="form-control" rows="4" placeholder="Enter diagnosis"
                    required></textarea>
            </div>


            <!-- Prescription -->
            <div class="form-group">
                <label for="prescription">Prescription</label>

                <textarea name="prescription" id="prescription" class="form-control" rows="4"
                    placeholder="Enter prescription" required></textarea>
            </div>


            <!-- File upload -->
            <div class="form-group">
                <label for="report_file">Upload Report File</label>

                <input type="file" name="report_file" id="report_file" class="form-control-file"
                    accept=".pdf,.jpg,.jpeg,.png" required>
            </div>


            <!-- Wallet verification -->
            <input type="hidden" name="wallet_address" id="wallet_address_input">
            <input type="hidden" name="signed_message" id="signed_message_input">


            <button type="submit" class="btn btn-primary">
                Sign with MetaMask & Submit
            </button>

        </form>

    </div>

@endsection

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

                    const message = `Authorize doctor diagnosis submission for patient #${form.patient_id.value}`;

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