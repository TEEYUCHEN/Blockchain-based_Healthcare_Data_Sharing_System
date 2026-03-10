@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Patient List</h2>
        <div style="margin-bottom: 15px;">
            <a href="{{ route('dashboard') }}">
                <button type="button">← Back to Dashboard</button>
            </a>
        </div>
        @if($patients->isEmpty())
            <p>No patients have granted you access.</p>
        @else
            <table border="1" cellpadding="10">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($patients as $grant)
                        <tr>
                            <td>{{ $grant->patient->name }}</td>
                            <td>{{ $grant->patient->phone }}</td>
                            <td>
                                <a href="{{ route('doctor.patient_details', ['id' => $grant->patient->id]) }}" target="_blank">
                                    <button>View Details</button>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection