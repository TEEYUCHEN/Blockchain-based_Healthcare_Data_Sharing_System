@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Patient Details</h2>

        <div style="margin-bottom: 15px;">
            <a href="{{ route('doctor.patient_list') }}">
                <button type="button">← Back to Patient List</button>
            </a>
        </div>
        <div>
            <img src="{{ $patient->profile_pic }}" alt="Profile Picture" style="width: 150px; height: 150px;">
        </div>
        <p><strong>Name:</strong> {{ $patient->name }}</p>
        <p><strong>Email:</strong> {{ $patient->email }}</p>
        <p><strong>Phone:</strong> {{ $patient->phone }}</p>
        <p><strong>Address:</strong> {{ $patient->address }}</p>

    </div>
@endsection