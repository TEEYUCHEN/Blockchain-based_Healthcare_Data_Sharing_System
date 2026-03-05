<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Healthcare System')</title>
</head>

<body>
    <div class="container">
        @yield('content')
    </div>

    {{-- Session alerts (only work for normal redirect with session, not fetch API) --}}
    @if(session('success'))
        <script>
            alert(@json(session('success')));
        </script>
    @endif

    @if(session('error'))
        <script>
            alert(@json(session('error')));
        </script>
    @endif

    <script src="{{ mix('js/app.js') }}"></script>
    @stack('scripts')

    {{-- ✅ Query param alert (works with fetch redirect /login?registered=1) --}}
    <script>
        (function () {
            const url = new URL(window.location.href);

            if (url.searchParams.get('registered') === '1') {
                alert('Registered successfully!');
                url.searchParams.delete('registered');
                history.replaceState({}, document.title, url.pathname + url.search);
            }
        })();
    </script>
</body>

</html>