<h2>{{ $patient->name }} Medical Records</h2>

<div style="margin-top: 15px;">
    <a href="{{ route('doctor.patient_list') }}">
        <button class="btn btn-secondary">← Back to Patient List</button>
    </a><br>

    <a href="{{ route('doctor.write_diagnosis', ['patient' => $patient->id, 'from' => 'patient_reports']) }}">
        <button class="btn btn-primary">Write Diagnosis</button>
    </a>

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

</div>