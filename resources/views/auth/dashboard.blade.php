@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Dashboard</h2>

        <p>Welcome, {{ auth()->user()->name }}</p>



        @if(auth()->user()->role === 'patient')
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
            <h3>Doctor Panel</h3>
            <button>View Patient Reports</button>
            <button>Write Diagnosis</button>

        @elseif(auth()->user()->role === 'lab')
            <h3>Lab Panel</h3>
            <button>Upload Lab Result</button>
            <button>View Test Requests</button>
        @endif
        <!-- Logout Button -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">
                Logout
            </button>
        </form>
    </div>
@endsection