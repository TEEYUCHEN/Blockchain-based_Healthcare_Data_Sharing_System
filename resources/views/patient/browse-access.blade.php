@extends('layouts.app')
@section('title', 'Add Access')

@section('content')
    <div class="container">
        <div class="dashboard-wrapper">
            <div class="page-header">
                <h2>Add Access</h2>
            </div>

            <a href="{{ route('patient.grant.access', ['tab' => $tab]) }}" class="btn btn-secondary">
                ← Back to Granted List
            </a><br><br>

            {{-- Tabs --}}
            <div class="tabs">
                <a href="{{ route('patient.grant.access.browse', ['tab' => 'doctor']) }}">
                    <button type="button" @if($tab === 'doctor') style="font-weight:bold;" @endif>Doctors</button>
                </a>
                <a href="{{ route('patient.grant.access.browse', ['tab' => 'lab']) }}">
                    <button type="button" @if($tab === 'lab') style="font-weight:bold;" @endif>Labs</button>
                </a>
            </div>

            {{-- Search (keeps tab) --}}
            <form method="GET" action="{{ route('patient.grant.access.browse') }}" style="margin-bottom:12px;">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <input type="text" name="q" value="{{ $q }}" placeholder="Search with name/email">
                <button type="submit" class="btn" style="background-color: gray; color: white;">Search</button>
                <a href="{{ route('patient.grant.access.browse', ['tab' => $tab]) }}" class="btn">
                    Reset
                </a>
            </form>

            <table class="table">
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
                            <td><strong>Email:</strong> {{ $u?->email ?? '-' }} <br>
                                @if($tab === 'doctor')
                                    <strong>Specialty:</strong> {{ $u?->specialty ?? '-' }}
                                @endif
                            </td>
                            <td>
                                <button type="button"
                                    onclick="window.location='{{ route('patient.access.show', ['id' => $u->id, 'from' => 'browse']) }}'"
                                    class="btn btn-secondary" style="padding: 2px 14px;">
                                    View Profile
                                </button>

                                @if(in_array($u->id, $grantedIds))
                                    <button type="button" disabled class="btn btn-success"
                                        style="padding: 2px 14px;">Granted</button>
                                @else
                                    <button type="button" onclick="handleGrant('{{ $u->wallet_address }}', this)"
                                        class="btn btn-primary" style="padding: 2px 14px;">
                                        Grant
                                    </button>
                                    {{--
                                    <form method="POST" action="{{ route('patient.grant.access.store') }}" style="display:inline;"
                                        class="wallet-auth-form">
                                        @csrf
                                        <input type="hidden" name="role_type" value="{{ $tab }}">
                                        <input type="hidden" name="authorized_id" value="{{ $u->id }}">
                                        <input type="hidden" name="wallet_address" class="wallet_address_input">
                                        <input type="hidden" name="signed_message" class="signed_message_input">
                                        <button type="submit">Grant</button>
                                    </form>
                                    --}}
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">No {{ $tab }} found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div style="margin-top:12px;">
                {{ $users->links() }}
            </div>
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

        async function handleGrant(targetWallet, btn) {

            try {
                btn.disabled = true;
                btn.innerText = "Processing...";

                // 1. blockchain
                await grantAccess(targetWallet);

                // 2. sync DB
                await fetch('/patient/grant/access/store', {
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

                alert("Access granted on blockchain");

                location.reload();

            } catch (err) {
                console.error(err);
                alert(err.message || "Transaction failed");

                btn.disabled = false;
                btn.innerText = "Grant";
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