@extends('layouts.app')

@section('content')
    <div class="container">

        <h2>
            @if($user->role === 'doctor')
                Doctor {{ $user->name }}'s Profile
            @elseif($user->role === 'lab')
                Lab {{ $user->name }}'s Profile
            @else
                {{ $user->name }}'s Profile
            @endif
        </h2>

        <div style="margin-bottom: 15px;">
            @if($from === 'browse')
                <a href="{{ route('patient.grant.access.browse', ['tab' => $user->role]) }}">
                    <button type="button">← Back to Browse</button>
                </a>
            @elseif($from === 'grant')
                <a href="{{ route('patient.grant.access', ['tab' => $user->role]) }}">
                    <button type="button">← Back to Granted Access</button>
                </a>
            @else
                <a href="{{ route('dashboard') }}">
                    <button type="button">← Back to Dashboard</button>
                </a>
            @endif
        </div>

        {{-- Profile Picture --}}
        <img src="{{ $profileUrl ?? asset('images/default-profile.png') }}"
            alt="Profile Picture" style="width:150px;height:150px;border-radius:50%;object-fit:cover;"
            onerror="this.onerror=null;this.src='{{ asset('images/default-profile.png') }}';">

        {{-- Basic Info --}}
        <p><strong>Name:</strong> {{ $user->name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Phone:</strong> {{ $user->phone }}</p>

        {{-- Role-specific --}}
        @if($user->role === 'doctor')
            <p><strong>Specialty:</strong> {{ $user->specialty }}</p>
            <p><strong>License Number:</strong> {{ $user->license_number }}</p>
        @endif

        @if($user->role === 'lab')
            <p><strong>Address:</strong> {{ $user->address }}</p>
        @endif

        {{-- Organization --}}
        <p><strong>Organization ID:</strong> {{ $user->organization_id }}</p>

        {{-- Grant info --}}
        <p><strong>Granted At:</strong> {{ $grant->created_at }}</p>

        <br>

        <a href="{{ url()->previous() }}">
            <button>← Back</button>
        </a>

    </div>
@endsection