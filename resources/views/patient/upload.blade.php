@extends('layouts.app')

@section('title', 'Upload Medical Record')

@section('content')
    <div class="container">
        <h2>Upload Medical Record</h2>

        <div style="margin-bottom: 15px;">
            <a href="{{ route('dashboard') }}">
                <button type="button">← Back to Dashboard</button>
            </a>
        </div>

        <form id="uploadForm">
            @csrf

            <input type="text" name="title" placeholder="Title">

            <textarea name="description" placeholder="Description"></textarea>

            <input type="text" name="category" placeholder="Category">

            <input type="file" name="record" required>

            <button type="submit">Sign with MetaMask & Upload</button>
        </form>

        <p id="successMsg" style="color:green;"></p>
        <p id="errorMsg" style="color:red;"></p>

    </div>
@endsection


@push('scripts')
    <script>
        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
            return null;
        }

        document.getElementById('uploadForm').addEventListener('submit', async function(e) {

    e.preventDefault();

    const errorEl = document.getElementById('errorMsg');
    const successEl = document.getElementById('successMsg');

    errorEl.textContent = '';
    successEl.textContent = '';

    try {

        if (!window.wallet) throw new Error("Wallet module not loaded");

        const message = "Upload medical record";

        const { address, signature } = await window.wallet.sign(message);

        const formData = new FormData(this);
        formData.append('wallet_address', address);
        formData.append('signed_message', signature);

        const response = await fetch('/patient/records', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await response.json();

        if (!response.ok) {
            errorEl.textContent = data.message || "Upload failed";
            return;
        }

        successEl.textContent = "Record uploaded successfully!";
        this.reset();

    } catch (err) {

        console.error(err);
        errorEl.textContent = err.message;

    }

});
    </script>
@endpush