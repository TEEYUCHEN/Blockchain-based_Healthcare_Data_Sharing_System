@extends('layouts.app')
@section('title', 'Granted Access')

@section('content')
    <div class="container">
        <h2>Granted Access</h2>

        <div style="margin-bottom: 15px;">
            <a href="{{ route('dashboard') }}">
                <button type="button">← Back to Dashboard</button>
            </a>
        </div>

        <div style="margin: 10px 0;">
            <a href="{{ route('patient.grant.access.browse', ['tab' => $tab]) }}">
                <button type="button">+ Add New Access</button>
            </a>
        </div>

        {{-- Tabs --}}
        <div style="display:flex; gap:10px; margin-bottom:12px;">
            <a href="{{ route('patient.grant.access', ['tab' => 'doctor']) }}">
                <button type="button" @if($tab === 'doctor') style="font-weight:bold;" @endif>Doctors</button>
            </a>
            <a href="{{ route('patient.grant.access', ['tab' => 'lab']) }}">
                <button type="button" @if($tab === 'lab') style="font-weight:bold;" @endif>Labs</button>
            </a>
        </div>

        <table border="1" cellpadding="8" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Info</th>
                    <th style="width:160px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($grants as $g)
                    @php $u = $authorizedUsers[$g->authorized_id] ?? null; @endphp
                    <tr>
                        <td>{{ $u?->name ?? ('User #' . $g->authorized_id) }}</td>
                        <td>{{ $u?->specialty ?? $u?->email ?? '-' }}</td>
                        <td>
                            <form method="POST" action="{{ route('patient.grant.access.revoke') }}" class="wallet-auth-form">
                                @csrf
                                <input type="hidden" name="role_type" value="{{ $tab }}">
                                <input type="hidden" name="authorized_id" value="{{ $g->authorized_id }}">
                                <input type="hidden" name="wallet_address" class="wallet_address_input">
                                <input type="hidden" name="signed_message" class="signed_message_input">
                                <button type="submit">Revoke</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">No {{ $tab }} access granted yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
    <script>

        document.addEventListener('DOMContentLoaded', function () {

            const forms = document.querySelectorAll('.wallet-auth-form');

            forms.forEach(form => {

                form.addEventListener('submit', async function (e) {

                    e.preventDefault();

                    try {

                        if (!window.wallet) {
                            alert("Wallet module not loaded");
                            return;
                        }

                        const message = "Authorize healthcare access change";

                        const { address, signature } = await window.wallet.sign(message);

                        console.log("Wallet:", address);
                        console.log("Signature:", signature);

                        form.querySelector('.wallet_address_input').value = address;
                        form.querySelector('.signed_message_input').value = signature;

                        form.submit();

                    } catch (err) {

                        console.error(err);
                        alert(err.message || "MetaMask signing failed");

                    }

                });

            });

        });

    </script>
@endpush