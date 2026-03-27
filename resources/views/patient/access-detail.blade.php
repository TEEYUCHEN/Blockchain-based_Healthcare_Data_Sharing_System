@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="dashboard-wrapper">
            <div class="page-header">
                <h2>
                    @if($user->role === 'doctor')
                        Doctor {{ $user->name }}'s Profile
                    @elseif($user->role === 'lab')
                        Lab {{ $user->name }}'s Profile
                    @else
                        {{ $user->name }}'s Profile
                    @endif
                </h2>
            </div>

            <div>
                @if($from === 'browse')
                    <a href="{{ route('patient.grant.access.browse', ['tab' => $user->role]) }}" class="btn btn-secondary">
                        ← Back to Browse
                    </a>
                @elseif($from === 'grant')
                    <a href="{{ route('patient.grant.access', ['tab' => $user->role]) }}" class="btn btn-secondary">
                        ← Back to Granted Access
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                        ← Back to Dashboard
                    </a>
                @endif
            </div><br><br>
            <div class="card profile-card">
                {{-- Profile Picture --}}
                <div class="profile-left">
                    <img src="{{ $profileUrl ?? asset('images/default-profile.png') }}" alt="Profile Picture"
                        onerror="this.onerror=null;this.src='{{ asset('images/default-profile.png') }}';"
                        class="profile-img">
                </div>
                <div class="profile-right">
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
                    <p><strong>Granted At:</strong> {{ $grant->created_at ?? 'Not Granted' }}</p>
                </div>
            </div>
        </div>
    </div>

@endsection