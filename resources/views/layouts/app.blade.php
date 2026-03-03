<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Healthcare System')</title>
</head>

<body>
    <div class="container">
        @yield('content')
    </div>

    @if(session('success'))
        <script>
            alert('{{ session('success') }}');
        </script>
    @endif

    @if(session('error'))
        <script>
            alert('{{ session('error') }}');
        </script>
    @endif
    <!-- Load Ethers library first -->
    <script src="{{ mix('js/app.js') }}"></script>

    <!-- Page-specific scripts -->
    @stack('scripts')
</body>

</html>