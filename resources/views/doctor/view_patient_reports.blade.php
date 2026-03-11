@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>View Patient Reports</h2>

        <div style="margin-top: 15px;">
            <a href="{{ route('dashboard') }}">
                <button type="button" class="btn btn-secondary">← Back to Dashboard</button>
            </a>
        </div>
        <div style="margin-top: 20px;">
            @if($reports->isEmpty())
                <p>No patients have granted access to their reports.</p>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>Patient Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $report)
                            <tr>
                                <td>{{ $report->patient->name }}</td>
                                <td>
                                    <a href="{{ route('doctor.patient_reports', ['id' => $report->patient->id]) }}">
                                        <button type="button" class="btn btn-primary">View Reports</button>
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