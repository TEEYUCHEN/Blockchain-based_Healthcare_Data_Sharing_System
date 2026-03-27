@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="dashboard-wrapper">
            <h2>Dashboard</h2>


            {{-- Welcome --}}
            @if(auth()->user()->role === 'patient')
                <p>Welcome, {{ auth()->user()->name }}</p>
                <h3>Patient Panel</h3>

                <div class="dashboard-grid">
                    <a href="{{ route('patient.upload') }}" class="dashboard-card">
                        <span>Upload Medical Record</span>
                    </a>

                    <a href="{{ route('patient.grant.access') }}" class="dashboard-card">
                        <span>Grant Access</span>
                    </a>

                    <a href="{{ route('patient.records') }}" class="dashboard-card">
                        <span>View My Medical Records</span>
                    </a>
                </div>

            @elseif(auth()->user()->role === 'doctor')
                <p>Welcome, Doctor {{ auth()->user()->name }}</p>
                <h3>Doctor Panel</h3>

                <div class="dashboard-grid">
                    <a href="{{ route('doctor.patient_list') }}" class="dashboard-card">
                        <span>Patient List</span>
                    </a>
                </div>

            @elseif(auth()->user()->role === 'lab')
                <p>Welcome to the Lab {{ auth()->user()->name }}</p>
                <h3>Lab Panel</h3>

                <div class="dashboard-grid">
                    <a href="{{ route('lab.patient_list') }}" class="dashboard-card">
                        <span>Patient List</span>
                    </a>
                </div>
            @endif


            <br><br>

            {{-- Footer actions --}}
            <div class="action-header">

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-secondary">
                        Logout
                    </button>
                </form>

                <a href="{{ route('profile.view') }}">
                    <button class="btn btn-primary">
                        Personal Profile
                    </button>
                </a>

            </div>
        </div>

    </div>
@endsection