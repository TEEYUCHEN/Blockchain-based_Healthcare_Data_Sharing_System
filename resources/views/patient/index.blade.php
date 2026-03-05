@extends('layouts.app')

@section('title', 'My Medical Records')

@section('content')
    <div class="container">
        <h2>My Medical Records</h2>

        <div style="margin: 10px 0;">
            <a href="{{ url('/patient/upload') }}">Upload New Record</a>
            {{-- change this link to your upload page route --}}
        </div>

        @if($records->isEmpty())
            <p>No records found.</p>
        @else
            <table border="1" cellpadding="8" cellspacing="0" style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>Uploaded By</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Uploaded At</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($records as $r)
                        <tr>
                            <td style="text-transform:capitalize;">
                                {{ $r->uploaded_by_role }}
                            </td>
                            <td>{{ $r->title ?? '(no title)' }}</td>
                            <td>{{ $r->category ?? '-' }}</td>
                            <td>{{ $r->verification_status }}</td>
                            <td>{{ $r->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <a href="{{ route('records.download', $r->id) }}">Download</a>

                                <button type="button" onclick="toggleDetails({{ $r->id }})">
                                    Details
                                </button>

                                @if($r->uploaded_by_role === 'patient')
                                    {{-- Optional: wire to API delete later --}}
                                    <button type="button" onclick="alert('Hook delete API here')">
                                        Delete
                                    </button>
                                @endif
                            </td>
                        </tr>

                        {{-- Details row (hidden by default) --}}
                        <tr id="details-{{ $r->id }}" style="display:none; background:#fafafa;">
                            <td colspan="6">
                                <div><b>Original filename:</b> {{ $r->original_filename }}</div>

                                <div><b>Description:</b><br>
                                    {{ $r->description ?? '(none)' }}
                                </div>

                                <div><b>File hash:</b> {{ $r->file_hash ?? '(not computed)' }}</div>

                                <div><b>Blockchain TX:</b> {{ $r->blockchain_tx_hash ?? '(not recorded)' }}</div>

                                @if($r->verified_at)
                                    <div><b>Verified at:</b> {{ $r->verified_at }}</div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        function toggleDetails(id) {
            const row = document.getElementById('details-' + id);
            row.style.display = (row.style.display === 'none' || row.style.display === '') ? 'table-row' : 'none';
        }
    </script>
@endpush