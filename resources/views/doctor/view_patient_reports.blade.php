@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>View Patient Reports</h2>
    </div>
    <div style="margin-bottom: 15px;">
        <a href="{{ route('dashboard') }}">
            <button type="button">← Back to Dashboard</button>
        </a>
    </div>
@endsection