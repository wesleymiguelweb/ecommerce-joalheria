<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Painel Administrativo - Elegance Joias')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="shortcut icon" href="{{ asset('img/logo.png') }}" type="image/x-icon">
    @yield('extra-styles')
</head>
<body>
    @include('partials.admin-header')

    <main class="container" style="padding: 20px 0;">
        @yield('content')
    </main>

    <script type="module" src="{{ asset('js/app.js') }}"></script>
    @yield('extra-scripts')
</body>
</html>
