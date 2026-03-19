@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Dashboard</h2>

        @if(auth()->user()->role === 'patient')
        <p>Welcome, {{ auth()->user()->name }}</p>
            <h3>Patient Panel</h3>
            <a href="{{ route(name: 'patient.upload') }}">
                <button>Upload Medical Record</button>
            </a>

            <a href="{{ route('patient.grant.access') }}">
                <button>Grant Access</button>
            </a>

            <a href="{{ route('patient.records') }}">
                <button>View My Medical Records</button>
            </a>

        @elseif(auth()->user()->role === 'doctor')
        <p>Welcome, Doctor {{ auth()->user()->name }}</p>
            <h3>Doctor Panel</h3>
            <a href="{{ route('doctor.patient_list') }}">
                <button>Patient List</button>
            </a>

        @elseif(auth()->user()->role === 'lab')
        <p>Welcome to the Lab {{ auth()->user()->name }}</p>
            <h3>Lab Panel</h3>
            <a href="{{ route('lab.patient_list') }}">
                <button>Patient List</button>
            </a>
        @endif
        <!-- Logout Button -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">
                Logout
            </button>
        </form>
        <!-- Personal Profile Button -->
        <a href="{{ route('profile.view') }}">
            <button>Personal Profile</button>
        </a>
    </div>
@endsection