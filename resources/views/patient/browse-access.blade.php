@extends('layouts.app')
@section('title', 'Add Access')

@section('content')
<div class="container">
    <h2>Add Access</h2>

    <div style="margin: 10px 0;">
        <a href="{{ route('patient.grant.access', ['tab' => $tab]) }}">
            <button type="button">← Back to Granted List</button>
        </a>
    </div>

    {{-- Tabs --}}
    <div style="display:flex; gap:10px; margin-bottom:12px;">
        <a href="{{ route('patient.grant.access.browse', ['tab' => 'doctor']) }}">
            <button type="button" @if($tab==='doctor') style="font-weight:bold;" @endif>Doctors</button>
        </a>
        <a href="{{ route('patient.grant.access.browse', ['tab' => 'lab']) }}">
            <button type="button" @if($tab==='lab') style="font-weight:bold;" @endif>Labs</button>
        </a>
    </div>

    {{-- Search (keeps tab) --}}
    <form method="GET" action="{{ route('patient.grant.access.browse') }}" style="margin-bottom:12px;">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <input type="text" name="q" value="{{ $q }}" placeholder="Search name/email/specialty...">
        <button type="submit">Search</button>
        <a href="{{ route('patient.grant.access.browse', ['tab' => $tab]) }}">
            <button type="button">Reset</button>
        </a>
    </form>

    <table border="1" cellpadding="8" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Name</th>
                <th>Info</th>
                <th style="width:220px;">Action</th>
            </tr>
        </thead>
        <tbody>
        @forelse($users as $u)
            <tr>
                <td>{{ $u->name }}</td>
                <td>{{ $u->specialty ?? $u->email }}</td>
                <td>
                    @if(in_array($u->id, $grantedIds))
                        <button type="button" disabled>Granted</button>
                    @else
                        <form method="POST" action="{{ route('patient.grant.access.store') }}" style="display:inline;">
                            @csrf
                            <input type="hidden" name="role_type" value="{{ $tab }}">
                            <input type="hidden" name="authorized_id" value="{{ $u->id }}">
                            <button type="submit">Grant</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="3">No {{ $tab }} found.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top:12px;">
        {{ $users->links() }}
    </div>
</div>
@endsection