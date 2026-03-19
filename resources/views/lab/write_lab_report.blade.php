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

        <form method="POST" action="{{ route('lab.upload') }}" enctype="multipart/form-data">
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
                <input type="file" name="report_file" class="form-control-file">
            </div>

            <button type="submit" class="btn btn-primary">
                Submit Lab Report
            </button>

        </form>

    </div>

@endsection