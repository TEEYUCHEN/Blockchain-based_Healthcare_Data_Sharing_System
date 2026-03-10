@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Personal Profile</h2>
        <div style="margin-bottom: 15px;">
            <a href="{{ route('dashboard') }}">
                <button type="button">← Back to Dashboard</button>
            </a>
        </div>

        <!-- Profile Picture Display -->
        <img src="{{ $profileUrl ?? asset('images/default-profile.png') }}" alt="Profile Picture"
            style="width:150px;height:150px;border-radius:50%;object-fit:cover;"
            onerror="this.onerror=null;this.src='{{ asset('images/default-profile.png') }}';">

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="profileForm">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 15px;">
                <label for="name">Name:</label><br>
                <input type="text" name="name" id="name" value="{{ $user->name }}" required>
            </div>

            <div style="margin-bottom: 15px;">
                <label for="email">Email:</label><br>
                <input type="email" name="email" id="email" value="{{ $user->email }}" required>
            </div>

            <div style="margin-bottom: 15px;">
                <label for="phone">Phone:</label><br>
                <input type="text" name="phone" id="phone" value="{{ $user->phone }}">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="address">Address:</label><br>
                <input type="text" name="address" id="address" value="{{ $user->address }}">
            </div>

            <div style="margin-bottom: 15px;">
                <input type="file" name="profile_pic" id="profile_pic">
            </div>

            <button type="submit" id="saveButton" disabled>Update Profile</button>
        </form>

        <script>
            const form = document.getElementById('profileForm');
            const saveButton = document.getElementById('saveButton');
            const inputs = form.querySelectorAll('input');

            let initialData = {};
            inputs.forEach(input => {
                if (input.type !== 'file') {
                    initialData[input.name] = input.value;
                }
            });

            form.addEventListener('input', () => {
                let isChanged = false;
                inputs.forEach(input => {
                    if (input.type === 'file' && input.files.length > 0) {
                        isChanged = true;
                    } else if (input.type !== 'file' && input.value !== initialData[input.name]) {
                        isChanged = true;
                    }
                });
                saveButton.disabled = !isChanged;
            });
        </script>
    </div>
@endsection