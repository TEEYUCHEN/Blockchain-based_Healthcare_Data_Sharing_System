@extends('layouts.app')

@section('content')

    <div class="container">

        <h2>Write Lab Report</h2>

        <div style="margin-top: 15px;">
            @if($from == 'patient_list')
                <a href="{{ route('lab.patient_list') }}">
                    <button class="btn btn-secondary">← Back to Patient List</button>
                </a>

            @elseif($from == 'reports')
                <a href="{{ route('lab.reports', $patient->id) }}">
                    <button class="btn btn-secondary">← Back to Patient Report</button>
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

        <form method="POST" action="{{ route('lab.upload') }}" enctype="multipart/form-data" id="labForm">
            @csrf

            <!-- Patient -->
            <input type="hidden" name="patient_id" value="{{ $patient->id }}">

            <div class="form-group">
                <label>Patient:</label>
                <p><strong>{{ $patient->name }}</strong></p>
            </div>

            <!-- Test Type -->
            <div class="form-group">
                <label for="test_type">Test Type</label>
                <input type="text" name="test_type" class="form-control" required>
            </div>

            <!-- Result -->
            <div class="form-group">
                <label for="result">Result</label>
                <textarea name="result" class="form-control" rows="4" required></textarea>
            </div>

            <!-- File -->
            <div class="form-group">
                <label for="report_file">Upload File</label>
                <input type="file" name="report_file" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png" required>
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

            const form = document.getElementById('labForm');

            form.addEventListener('submit', async function (e) {

                e.preventDefault();

                try {

                    if (!window.wallet) {
                        alert("Wallet module not loaded");
                        return;
                    }

                    const message = "Authorize lab report submission";

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