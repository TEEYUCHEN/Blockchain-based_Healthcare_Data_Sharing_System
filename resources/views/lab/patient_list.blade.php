@extends('layouts.app')

@section('title', 'Patient List')

@section('content')

    <div class="container">
        <h2>Patients List</h2>

        <div style="margin-bottom: 15px;">
            <a href="{{ route('dashboard') }}">
                <button type="button">← Back to Dashboard</button>
            </a>
        </div>

        @if(session('success'))
            <p style="color:green;">{{ session('success') }}</p>
        @endif

        @if($patients->isEmpty())
            <p>No patients have granted access yet.</p>
        @else

            <table border="1" cellpadding="8" cellspacing="0" style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th>Patient Name</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($patients as $grant)
                        <tr>

                            <td>{{ $grant->patient->name }}</td>
                            <td>{{ $grant->patient->email }}</td>

                            <td style="display:flex; gap:8px;">
                                <!-- View Patient Details -->
                                <a href="{{ route('lab.patient_details', ['id' => $grant->patient->id]) }}" target="_blank">
                                    <button>View Details</button>
                                </a>

                                <!-- View Reports -->
                                <a href="{{ route('lab.reports', $grant->patient->id) }}">
                                    <button class="btn btn-info">View Reports</button>
                                </a>

                                <!-- Upload Lab Report -->
                                <a href="{{ route('lab.write_lab_report', ['patient' => $grant->patient->id, 'from' => 'patient_list']) }}">
                                    <button class="btn btn-primary">Write Lab Report</button>
                                </a>

                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>

        @endif
    </div>

@endsection