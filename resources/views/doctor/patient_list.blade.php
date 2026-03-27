@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="dashboard-wrapper">
            <div class="page-header">
                <h2>Patient List</h2>
            </div>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                ← Back to Dashboard
            </a><br><br>
            @if($patients->isEmpty())
                <p>No patients have granted you access.</p>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patients as $grant)
                            <tr>
                                <td>{{ $grant->patient->name }}</td>
                                <td>{{ $grant->patient->phone }}</td>
                                <td>
                                    <a href="{{ route('doctor.patient_details', ['id' => $grant->patient->id]) }}" target="_blank"
                                        class="btn btn-secondary" style="padding: 2px 14px;">
                                        View Details
                                    </a>

                                    <a href="{{ route('doctor.patient_reports', ['id' => $grant->patient->id]) }}" target="_blank"
                                        class="btn btn-secondary" style="padding: 2px 14px; background-color: orange;">
                                        View Medical Records
                                    </a>

                                    <a href="{{ route('doctor.write_diagnosis', ['patient' => $grant->patient->id, 'from' => 'patient_list']) }}"
                                        class="btn btn-primary" style="padding: 2px 14px;">
                                        Write Diagnosis
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