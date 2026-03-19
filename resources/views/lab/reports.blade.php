@extends('layouts.app')

@section('content')

    <div class="container">

        <h2>{{ $patient->name }} Medical Records</h2>

        <!-- 🔹 Action Buttons -->
        <div style="margin: 15px 0; display:flex; gap:10px;">
            <a href="{{ route('lab.patient_list') }}">
                <button class="btn btn-secondary">← Back to Patient List</button>
            </a>

            <a href="{{ route('lab.write_lab_report', ['patient' => $patient->id, 'from' => 'reports']) }}">
                <button class="btn btn-primary">Write Lab Report</button>
            </a>
        </div>

        <!-- 🔹 Tabs -->
        <div style="display:flex; gap:10px; margin-bottom:15px;">
            <a href="{{ route('lab.reports', ['patient_id' => $patient->id, 'tab' => 'patient']) }}">
                <button @if($tab === 'patient') style="font-weight:bold;" @endif>
                    Patient Uploads
                </button>
            </a>

            <a href="{{ route('lab.reports', ['patient_id' => $patient->id, 'tab' => 'doctor']) }}">
                <button @if($tab === 'doctor') style="font-weight:bold;" @endif>
                    Doctor Reports
                </button>
            </a>

            <a href="{{ route('lab.reports', ['patient_id' => $patient->id, 'tab' => 'lab']) }}">
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
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>File</th>
                        <th>Date</th>
                    </tr>

                    @foreach($patientRecords as $record)
                        <tr>
                            <td>{{ $record->title }}</td>
                            <td>{{ $record->description }}</td>
                            <td>
                                <a href="{{ route('records.download', ['type' => 'medical', 'id' => $record->id]) }}">
                                    Download
                                </a>
                            </td>
                            <td>{{ $record->created_at }}</td>
                        </tr>
                    @endforeach
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
                    <tr>
                        <th>Diagnosis</th>
                        <th>Prescription</th>
                        <th>File</th>
                        <th>Date</th>
                    </tr>

                    @foreach($doctorReports as $report)
                        <tr>
                            <td>{{ $report->diagnosis }}</td>
                            <td>{{ $report->prescription }}</td>
                            <td>
                                <a href="{{ route('records.download', ['type' => 'doctor', 'id' => $report->id]) }}">
                                    Download
                                </a>
                            </td>
                            <td>{{ $report->created_at }}</td>
                        </tr>
                    @endforeach
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
                    <tr>
                        <th>Test Type</th>
                        <th>Result</th>
                        <th>File</th>
                        <th>Date</th>
                    </tr>

                    @foreach($labReports as $report)
                        <tr>
                            <td>{{ $report->test_type }}</td>
                            <td>{{ $report->result }}</td>
                            <td>
                                <a href="{{ route('records.download', ['type' => 'lab', 'id' => $report->id]) }}">
                                    Download
                                </a>
                            </td>
                            <td>{{ $report->created_at }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif
        @endif

    </div>

@endsection