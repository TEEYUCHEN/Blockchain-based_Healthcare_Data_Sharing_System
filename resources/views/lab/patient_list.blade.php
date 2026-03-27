@extends('layouts.app')

@section('title', 'Patient List')

@section('content')

    <div class="container">
        <div class="dashboard-wrapper">
            <div class="page-header">
                <h2>Patients List</h2>
            </div>

            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                ← Back to Dashboard
            </a><br><br>


            @if(session('success'))
                <p style="color:green;">{{ session('success') }}</p>
            @endif

            @if($patients->isEmpty())
                <p>No patients have granted access yet.</p>
            @else

                <table class="table">
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
                                    <a href="{{ route('lab.patient_details', ['id' => $grant->patient->id]) }}" target="_blank"
                                        class="btn btn-secondary" style="padding: 2px 14px;">
                                        View Details
                                    </a>

                                    <!-- View Reports -->
                                    <a href="{{ route('lab.reports', $grant->patient->id) }}" class="btn btn-secondary"
                                        style="padding: 2px 14px; background-color: blue;">
                                        View Reports
                                    </a>

                                    <!-- Upload Lab Report -->
                                    <a href="{{ route('lab.write_lab_report', ['patient' => $grant->patient->id, 'from' => 'patient_list']) }}"
                                        class="btn btn-primary" style="padding: 2px 14px;">
                                        Write Lab Report
                                    </a>

                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>

            @endif
        </div>
    </div>

@endsection