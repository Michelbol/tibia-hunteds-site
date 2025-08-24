<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login</title>
</head>
<body>
<div>

@error('email')
    <div>{{ $message }}</div>
@enderror
</div>
<form action="/login" method="POST">
    @csrf
    <input name="email">
    <input name="password" type="password">
    <button type="submit">Submit</button>
</form>
</body>
</html>
