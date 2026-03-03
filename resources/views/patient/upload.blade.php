@extends('layouts.app')

@section('title', 'Upload Medical Record')

@section('content')
<div class="container">
    <h2>Upload Medical Record</h2>

    <form method="POST" action="#">
        @csrf

        <div>
            <label>Record Title</label>
            <input type="text" name="title" required>
        </div>

        <div>
            <label>Description</label>
            <textarea name="description" required></textarea>
        </div>

        <div>
            <label>Upload File</label>
            <input type="file" name="file">
        </div>

        <button type="submit">Upload</button>
    </form>
</div>
@endsection