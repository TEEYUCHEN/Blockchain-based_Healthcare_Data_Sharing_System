@extends('layouts.app')

@section('title', 'My Medical Records')

@section('content')
    <div class="container">
        <div class="dashboard-wrapper">
            <div class="page-header">
                <h2>My Medical Records</h2>
            </div>

            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                ← Back to Dashboard
            </a><br><br>

            <!-- 🔹 Tabs -->
            <div class="tabs">
                <a href="{{ route('patient.records', ['tab' => 'patient']) }}">
                    <button @if($tab === 'patient') style="font-weight:bold;" @endif>
                        My Uploads
                    </button>
                </a>

                <a href="{{ route('patient.records', ['tab' => 'doctor']) }}">
                    <button @if($tab === 'doctor') style="font-weight:bold;" @endif>
                        Doctor Reports
                    </button>
                </a>

                <a href="{{ route('patient.records', ['tab' => 'lab']) }}">
                    <button @if($tab === 'lab') style="font-weight:bold;" @endif>
                        Lab Reports
                    </button>
                </a>
            </div>

            <!-- 🔹 Patient Uploads -->
            @if($tab === 'patient')

                <h3>My Uploads</h3>

                @if($patientRecords->isEmpty())
                    <p>No patient records.</p>
                @else
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Description</th>
                                <th>File</th>
                                <th>Date</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($patientRecords as $record)
                                <tr>
                                    <td>{{ $record->title }}</td>
                                    <td>{{ $record->description }}</td>
                                    <td>
                                        <small>
                                            {{ $record->original_filename ?? 'No filename' }}
                                        </small><br>
                                        <a href="{{ route('records.view', ['type' => 'medical', 'id' => $record->id]) }}"
                                            target="_blank" style="color:purple !important;">
                                            View
                                        </a>
                                        |
                                        <a href="{{ route('records.download', ['type' => 'medical', 'id' => $record->id]) }}">
                                            Download
                                        </a>

                                        @if($record->patient_id === auth()->id())
                                            |
                                            <a href="javascript:void(0);" onclick="deleteRecord({{ $record->id }})" style="color:red;">
                                                Delete
                                            </a>
                                        @endif

                                        <br>
                                    </td>
                                    <td>{{ $record->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

            @endif


            <!-- 🔹 Doctor Reports -->
            @if($tab === 'doctor')

                <h3>Doctor Reports</h3>

                @if($doctorReports->isEmpty())
                    <p>No doctor reports.</p>
                @else
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Diagnosis</th>
                                <th>Prescription</th>
                                <th>Doctor</th>
                                <th>File</th>
                                <th>Date</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($doctorReports as $report)
                                <tr>
                                    <td>{{ $report->diagnosis }}</td>
                                    <td>{{ $report->prescription }}</td>
                                    <td>{{ $report->doctor->name ?? 'Doctor' }}</td>
                                    <td>
                                        <small>
                                            {{ $report->original_filename ?? 'No filename' }}
                                        </small><br>
                                        <a href="{{ route('records.view', ['type' => 'doctor', 'id' => $report->id]) }}" target="_blank"
                                            style="color:purple;">
                                            View
                                        </a>
                                        |
                                        <a href="{{ route('records.download', ['type' => 'doctor', 'id' => $report->id]) }}">
                                            Download
                                        </a>
                                    </td>
                                    <td>{{ $report->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

            @endif


            <!-- 🔹 Lab Reports -->
            @if($tab === 'lab')

                <h3>Lab Reports</h3>

                @if($labReports->isEmpty())
                    <p>No lab reports.</p>
                @else
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Test Type</th>
                                <th>Result</th>
                                <th>Lab</th>
                                <th>File</th>
                                <th>Date</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($labReports as $report)
                                <tr>
                                    <td>{{ $report->test_type }}</td>
                                    <td>{{ $report->result }}</td>
                                    <td>{{ $report->lab->name ?? 'Lab' }}</td>
                                    <td>

                                        <small>
                                            {{ $report->original_filename ?? 'No filename' }}
                                        </small><br>
                                        <a href="{{ route('records.view', ['type' => 'lab', 'id' => $report->id]) }}" target="_blank"
                                            style="color:purple;">
                                            View
                                        </a>
                                        |
                                        <a href="{{ route('records.download', ['type' => 'lab', 'id' => $report->id]) }}">
                                            Download
                                        </a>
                                    </td>
                                    <td>{{ $report->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>

        function toggleDetails(id) {
            const row = document.getElementById('details-' + id);
            row.style.display = (row.style.display === 'none' || row.style.display === '') ? 'table-row' : 'none';
        }

        async function deleteRecord(id) {

            try {

                if (!window.wallet) {
                    alert("Wallet module not loaded");
                    return;
                }

                if (!confirm("Are you sure you want to delete this record?")) {
                    return;
                }

                const message = "Authorize deletion of medical record #" + id;

                const { address, signature } = await window.wallet.sign(message);

                const response = await fetch(`/records/${id}/delete`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        wallet_address: address,
                        signed_message: signature
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    alert(data.message || "Delete failed");
                    return;
                }

                alert("Record deleted successfully");
                location.reload();

            } catch (err) {

                console.error(err);
                alert(err.message || "MetaMask signing failed");

            }
        }

    </script>
@endpush