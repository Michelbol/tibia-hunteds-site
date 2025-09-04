<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Tibia Tracker</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

<div class="login-container">
    <h2>Login Guild Observer</h2>
    @error('email')
    <div id="errorBox" class="alert"> {{ $message }}</div>
    @enderror
    <form action="/login" method="POST">
        @csrf
        <input type="text" id="email" name="email" placeholder="Usuário" required>
        <input type="password" id="password" name="password" placeholder="Senha" required>
        <button type="submit">Entrar</button>
    </form>
</div>

</body>
</html>
