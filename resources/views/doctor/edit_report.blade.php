@extends('layouts.app')

@section('content')
    <div class="container">

        <h2>Edit Doctor Report</h2>

        @if(session('success'))
            <p style="color:green">{{ session('success') }}</p>
        @endif

        <form method="POST" action="{{ route('doctor.update_report', $report->id) }}">
            @csrf

            <div>
                <label>Diagnosis</label>
                <textarea name="diagnosis" class="form-control">{{ $report->diagnosis }}</textarea>
            </div>

            <div>
                <label>Prescription</label>
                <textarea name="prescription" class="form-control">{{ $report->prescription }}</textarea>
            </div>

            <div style="margin-top:10px;">
                <button type="submit" class="btn btn-primary">Update</button>

                <a href="{{ route('doctor.patient_reports', ['id' => $report->patient_id, 'tab' => 'doctor']) }}">
                    <button type="button" class="btn btn-secondary">Cancel</button>
                </a>
            </div>

        </form>

    </div>
@endsection