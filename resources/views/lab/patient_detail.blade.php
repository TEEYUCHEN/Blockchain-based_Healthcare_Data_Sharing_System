@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Patient Details</h2>

        <div style="margin-bottom: 15px;">
            <a href="{{ route('lab.patient_list') }}">
                <button type="button">← Back to Patient List</button>
            </a>
        </div>
        <div>
            <img src="{{ $profileUrl ?? asset('images/default-profile.png') }}"
            alt="Profile Picture" style="width:150px;height:150px;border-radius:50%;object-fit:cover;"
            onerror="this.onerror=null;this.src='{{ asset('images/default-profile.png') }}';">
        </div>
        <p><strong>Name:</strong> {{ $patient->name }}</p>
        <p><strong>Email:</strong> {{ $patient->email }}</p>
        <p><strong>Phone:</strong> {{ $patient->phone }}</p>
        <p><strong>Address:</strong> {{ $patient->address }}</p>

    </div>
@endsection