@extends('layouts.app')

@section('title', 'Patient List')

@section('content')

    <div class="container">
        <h2>Patients List</h2>

        <div style="margin-bottom: 15px;">
            <a href="{{ route('dashboard') }}">
                <button type="button">← Back to Dashboard</button>
            </a>
        </div>

        @if(session('success'))
            <p style="color:green;">{{ session('success') }}</p>
        @endif

        @if($patients->isEmpty())
            <p>No patients have granted access yet.</p>
        @else

            <table border="1" cellpadding="8" cellspacing="0" style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th>Patient Name</th>
                        <th>Email</th>
                        <th>Upload Lab Result</th>
                        <th>View Reports</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($patients as $grant)
                        <tr>

                            <td>{{ $grant->patient->name }}</td>
                            <td>{{ $grant->patient->email }}</td>

                            <td>
                                <form action="{{ route('lab.upload') }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <input type="hidden" name="patient_id" value="{{ $grant->patient->id }}">

                                    <div style="margin-bottom:6px;">
                                        <input type="text" name="test_type" placeholder="Test Type (e.g. Blood Test)" required>
                                    </div>

                                    <div style="margin-bottom:6px;">
                                        <textarea name="result" placeholder="Result / Notes" rows="2" required></textarea>
                                    </div>

                                    <div style="margin-bottom:6px;">
                                        <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" required>
                                    </div>

                                    <button type="submit">Upload Result</button>

                                </form>
                            </td>

                            <td>
                                <a href="{{ route('lab.reports', $grant->patient->id) }}">
                                    <button>View Reports</button>
                                </a>
                            </td>

                        </tr>
                    @endforeach
                </tbody>

            </table>

        @endif
    </div>

@endsection