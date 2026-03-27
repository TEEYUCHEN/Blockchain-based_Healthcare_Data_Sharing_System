@extends('layouts.app')

@section('content')
    <div class="container">

        <h2>Edit Lab Report</h2>

        <div class="card form-card">
            @if(session('success'))
                <p style="color:green">{{ session('success') }}</p>
            @endif

            <form method="POST" action="{{ route('lab.update_report', $report->id) }}">
                @csrf

                <div class="form-group">
                    <label>Test Type:</label>
                    <input type="text" name="test_type" class="form-control" value="{{ $report->test_type }}" required>
                </div>

                <div class="form-group">
                    <label>Result:</label>
                    <textarea name="result" required>{{ $report->result }}</textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update</button>

                    <a href="{{ route('lab.reports', ['patient_id' => $report->patient_id, 'tab' => 'lab']) }}">
                        <button type="button" class="btn btn-secondary">Cancel</button>
                    </a>
                </div>

            </form>
        </div>

    </div>
@endsection