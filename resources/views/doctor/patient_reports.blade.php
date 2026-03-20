@extends('layouts.app')

@section('content')

    @if(session('success'))
        <p style="color:green">{{ session('success') }}</p>
    @endif

    <div class="container">

        <h2>{{ $patient->name }} Medical Records</h2>

        <!-- 🔹 Action Buttons -->
            <div style="margin: 15px 0; display:flex; gap:10px;">
                <a href="{{ route('doctor.patient_list') }}">
                    <button class="btn btn-secondary">← Back to Patient List</button>
                </a>

                <a href="{{ route('doctor.write_diagnosis', ['patient' => $patient->id, 'from' => 'patient_reports']) }}">
                    <button class="btn btn-primary">Write Diagnosis</button>
                </a>
            </div>

            <!-- 🔹 Tabs -->
            <div style="display:flex; gap:10px; margin-bottom:15px;">
                <a href="{{ route('doctor.patient_reports', ['id' => $patient->id, 'tab' => 'patient']) }}">
                    <button @if($tab === 'patient') style="font-weight:bold;" @endif>
                        Patient Uploads
                    </button>
                </a>

                <a href="{{ route('doctor.patient_reports', ['id' => $patient->id, 'tab' => 'doctor']) }}">
                    <button @if($tab === 'doctor') style="font-weight:bold;" @endif>
                        Doctor Reports
                    </button>
                </a>

                <a href="{{ route('doctor.patient_reports', ['id' => $patient->id, 'tab' => 'lab']) }}">
                    <button @if($tab === 'lab') style="font-weight:bold;" @endif>
                        Lab Reports
                    </button>
                </a>
            </div>

            <!-- 🔹 Patient Uploads -->
        @if($tab === 'patient')

            <h3>Patient Uploads</h3>

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
                                    <a href="{{ route('records.view', ['type' => 'medical', 'id' => $record->id]) }}" target="_blank">
                                        View
                                    </a>
                                    |
                                    <a href="{{ route('records.download', ['type' => 'medical', 'id' => $record->id]) }}">
                                        Download
                                        </a>
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
                                    <a href="{{ route('records.view', ['type' => 'doctor', 'id' => $report->id]) }}" target="_blank">
                                        View
                                    </a>
                                    |
                                    <a href="{{ route('records.download', ['type' => 'doctor', 'id' => $report->id]) }}">
                                        Download
                                        </a>

                                        @if($report->doctor_id === auth()->id())
                                            |
                                            <a href="{{ route('doctor.edit_report', $report->id) }}">
                                                       Edit
                                            </a>
                                        @endif

                                        <br>

                                        <small>
                                            {{ $report->original_filename ?? 'No filename' }}
                                            </small>
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
                                    <a href="{{ route('records.view', ['type' => 'lab', 'id' => $report->id]) }}" target="_blank">
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

@endsection