@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="dashboard-wrapper">
            <h2>Edit Doctor Report</h2>
            <div class="card form-card">
                @if(session('success'))
                    <p style="color:green">{{ session('success') }}</p>
                @endif

                <form method="POST" action="{{ route('doctor.update_report', $report->id) }}">
                    @csrf

                    <div class="form-group">
                        <label>Diagnosis:</label>
                        <textarea name="diagnosis" class="form-control">{{ $report->diagnosis }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Prescription:</label>
                        <textarea name="prescription" class="form-control">{{ $report->prescription }}</textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Update</button>

                        <a href="{{ route('doctor.patient_reports', ['id' => $report->patient_id, 'tab' => 'doctor']) }}">
                            <button type="button" class="btn btn-secondary">Cancel</button>
                        </a>
                    </div>

                </form>
            </div>
        </div>

    </div>
@endsection