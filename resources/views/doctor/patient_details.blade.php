@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="dashboard-wrapper">
            <div class="page-header">
                <h2>Patient Details</h2>
            </div>

            <a href="{{ route('doctor.patient_list') }}" class="btn btn-secondary">
                ← Back to Patient List
            </a><br><br>

            <div class="card profile-card">

                <div class="profile-wrapper">

                    <div class="profile-left">
                        <img src="{{ $profileUrl ?? asset('images/default-profile.png') }}" alt="Profile Picture"
                            onerror="this.onerror=null;this.src='{{ asset('images/default-profile.png') }}';"
                            class="profile-img">
                    </div>

                    <div class="profile-right">
                        <p><strong>Name:</strong> {{ $patient->name }}</p>
                        <p><strong>Email:</strong> {{ $patient->email }}</p>
                        <p><strong>Phone:</strong> {{ $patient->phone }}</p>
                        <p><strong>Address:</strong> {{ $patient->address }}</p>
                    </div>

                </div>

            </div>
        </div>

    </div>
@endsection