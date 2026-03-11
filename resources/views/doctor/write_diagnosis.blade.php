@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Write Diagnosis</h2>

        <div style="margin-top: 15px;">
            <a href="{{ route('dashboard') }}">
                <button type="button" class="btn btn-secondary">← Back to Dashboard</button>
            </a>
        </div>

        <form method="POST" action="{{ route('doctor.submit_diagnosis') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="patient_id">Select Patient</label>
                <select name="patient_id" id="patient_id" class="form-control" required>
                    <option value="">-- Select Patient --</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}">{{ $patient->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="diagnosis">Diagnosis</label>
                <textarea name="diagnosis" id="diagnosis" class="form-control" rows="4"
                    placeholder="Enter diagnosis"></textarea>
            </div>

            <div class="form-group">
                <label for="prescription">Prescription</label>
                <textarea name="prescription" id="prescription" class="form-control" rows="4"
                    placeholder="Enter prescription"></textarea>
            </div>

            <div class="form-group">
                <label for="report_file">Upload Report File</label>
                <input type="file" name="report_file" id="report_file" class="form-control-file">
            </div>

            <button type="submit" class="btn btn-primary">Submit Report</button>
        </form>


    </div>
@endsection