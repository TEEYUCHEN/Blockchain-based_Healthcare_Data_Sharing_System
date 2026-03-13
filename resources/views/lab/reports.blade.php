@extends('layouts.app')

@section('title', 'Patient Reports')

@section('content')

    <div class="container">

        <h2>Patient Reports</h2>

        <div style="margin-bottom:15px;">
            <a href="{{ route('lab.patient_list') }}">
                <button>← Back to Patient List</button>
            </a>
        </div>

        <h3>Lab Reports</h3>

        @if($labReports->isEmpty())
            <p>No lab reports.</p>
        @else
            <table border="1" cellpadding="8">
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
                            @if($report->report_file)
                                <a href="{{ Storage::disk('s3')->temporaryUrl($report->report_file, now()->addMinutes(60)) }}">
                                    Download
                                </a>
                            @endif
                        </td>

                        <td>{{ $report->created_at }}</td>
                    </tr>
                @endforeach
            </table>
        @endif


        <h3>Doctor Reports</h3>

        @if($doctorReports->isEmpty())
            <p>No doctor reports.</p>
        @else
            <table border="1" cellpadding="8">
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
                            @if($report->report_file)
                                <a href="{{ Storage::disk('s3')->temporaryUrl($report->report_file, now()->addMinutes(60)) }}">
                                    Download
                                </a>
                            @endif
                        </td>

                        <td>{{ $report->created_at }}</td>
                    </tr>
                @endforeach
            </table>
        @endif


        <h3>Patient Uploads</h3>

        @if($patientRecords->isEmpty())
            <p>No patient records.</p>
        @else
            <table border="1" cellpadding="8">
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
                            <a href="{{ Storage::disk('s3')->temporaryUrl($record->stored_path, now()->addMinutes(60)) }}">
                                Download
                            </a>
                        </td>

                        <td>{{ $record->created_at }}</td>
                    </tr>
                @endforeach
            </table>
        @endif

    </div>

@endsection