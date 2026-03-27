@extends('layouts.app')
@section('title', 'Granted Access')

@section('content')
    <div class="container">
        <div class="dashboard-wrapper">
            <div class="page-header">
                <h2>Granted Access</h2>
            </div>

            <div>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                    ← Back to Dashboard
                </a>
            </div>

            <div style="margin: 10px 0;">
                <a href="{{ route('patient.grant.access.browse', ['tab' => $tab]) }}" class="btn btn-primary">
                    + Add New Access
                </a>
            </div>

            {{-- Tabs --}}
            <div class="tabs">
                <a href="{{ route('patient.grant.access', ['tab' => 'doctor']) }}">
                    <button type="button" @if($tab === 'doctor') style="font-weight:bold;" @endif>Doctors</button>
                </a>
                <a href="{{ route('patient.grant.access', ['tab' => 'lab']) }}">
                    <button type="button" @if($tab === 'lab') style="font-weight:bold;" @endif>Labs</button>
                </a>
            </div>

            <table class="table">
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
                            <td><strong>Email:</strong> {{ $u?->email ?? '-' }} <br>
                                @if($tab === 'doctor')
                                    <strong>Specialty:</strong> {{ $u?->specialty ?? '-' }}
                                @endif
                            </td>
                            <td>
                                <button type="button"
                                    onclick="window.location='{{ route('patient.access.show', ['id' => $u->id, 'from' => 'grant']) }}'"
                                    class="btn btn-secondary" style="padding: 2px 14px;">
                                    View Profile
                                </button>
                                <button type="button" onclick="handleRevoke('{{ $u->wallet_address }}', this)"
                                    class="btn btn-danger" style="padding: 2px 14px">
                                    Revoke
                                </button>
                                {{--
                                <form method="POST" action="{{ route('patient.grant.access.revoke') }}"
                                    class="wallet-auth-form">
                                    @csrf
                                    <input type="hidden" name="role_type" value="{{ $tab }}">
                                    <input type="hidden" name="authorized_id" value="{{ $g->authorized_id }}">
                                    <input type="hidden" name="wallet_address" class="wallet_address_input">
                                    <input type="hidden" name="signed_message" class="signed_message_input">
                                    <button type="submit">Revoke</button>
                                </form> --}}
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
    </div>
@endsection

@push('scripts')

    <script src="https://cdn.jsdelivr.net/npm/web3@1.10.0/dist/web3.min.js"></script>
    <script src="/js/web3helper.js"></script>

    <script>

        const CONTRACT_ADDRESS = @json($contractAddress);
        const ABI = @json($contractABI);

        window.addEventListener("load", async () => {
            await initWeb3(ABI, CONTRACT_ADDRESS);
        });

        async function handleRevoke(targetWallet, btn) {

            try {
                btn.disabled = true;
                btn.innerText = "Processing...";

                // 1. blockchain revoke
                await revokeAccess(targetWallet);

                // 2. sync DB
                await fetch("{{ route('patient.grant.access.revoke') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        authorized_wallet: targetWallet,
                        role_type: '{{ $tab }}'
                    })
                });

                alert("Access revoked on blockchain");

                location.reload();

            } catch (err) {
                console.error(err);
                alert(err.message || "Transaction failed");

                btn.disabled = false;
                btn.innerText = "Revoke";
            }
        }

    </script>

@endpush
{{--
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
--}}