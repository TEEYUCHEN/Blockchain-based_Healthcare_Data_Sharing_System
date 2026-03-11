<h2>{{ $patient->name }} Medical Records</h2>

@if($records->isEmpty())
    <p>No records found.</p>
@else
    <table class="table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Uploaded By</th>
                <th>File</th>
            </tr>
        </thead>

        <tbody>
            @foreach($records as $record)
                <tr>
                    <td>{{ $record->title }}</td>
                    <td>{{ $record->uploaded_by_role }}</td>
                    <td>
                        <a href="{{ route('records.download', $record->id) }}">
                            Download
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>
@endif