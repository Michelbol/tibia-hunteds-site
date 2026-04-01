<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Online Characters - Dark Theme</title>
    @stack('css')
    @if(Auth::hasUser() && Auth::user()->super_admin)
        @vite('resources/js/app.js')
    @endif
</head>
<body>
    @yield('content')
    @stack('js')
</body>
</html>
