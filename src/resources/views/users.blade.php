@extends('layouts.default')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/users.css') }}">
@endpush

@section('content')
    <div class="page">
        <div class="container">

            <form id="userForm" class="card" method="POST" action="{{ route('users.store') }}">
                <h2>Criar Usuário</h2>
                @csrf

                @if(session('success'))
                    <div class="alert-success">{{ session('success') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert-error">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <label for="name">Nome</label>
                <input type="text" id="name" name="name" placeholder="Ex: João Silva" value="{{ old('name') }}" autocomplete="off">

                <label for="email">E-mail</label>
                <input type="text" id="email" name="email" placeholder="Ex: joao@email.com" value="{{ old('email') }}" autocomplete="off">

                <label for="password">Senha</label>
                <div class="password-row">
                    <div class="password-wrap">
                        <input type="password" id="password" name="password" placeholder="Mínimo 8 caracteres" autocomplete="new-password">
                        <button type="button" class="btn-eye" data-target="password" title="Mostrar/ocultar senha">
                            <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg class="eye-off-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>
                    <button type="button" id="btnGenPassword" class="btn-gen" title="Gerar senha aleatória">Gerar</button>
                </div>

                <label for="password_confirmation">Confirmar Senha</label>
                <div class="password-wrap">
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repita a senha" autocomplete="new-password">
                    <button type="button" class="btn-eye" data-target="password_confirmation" title="Mostrar/ocultar senha">
                        <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        <svg class="eye-off-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                            <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                            <line x1="1" y1="1" x2="23" y2="23"/>
                        </svg>
                    </button>
                </div>

                <button type="submit" class="btn-save">Criar Usuário</button>
            </form>

            <div class="card users-list">
                <h2>Usuários Cadastrados</h2>
                @if($users->isEmpty())
                    <p class="empty-msg">Nenhum usuário cadastrado.</p>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>Admin</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->super_admin ? 'Sim' : 'Não' }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Remover {{ $user->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-delete">Remover</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

        </div>
    </div>
@push('js')
<script>
document.getElementById('btnGenPassword').addEventListener('click', function () {
    const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*';
    const array = new Uint8Array(16);
    crypto.getRandomValues(array);
    const password = Array.from(array, b => chars[b % chars.length]).join('');

    document.getElementById('password').value = password;
    document.getElementById('password_confirmation').value = password;

    this.textContent = 'Copiando...';
    navigator.clipboard.writeText(password).finally(() => {
        this.textContent = 'Copiado!';
        setTimeout(() => { this.textContent = 'Gerar'; }, 2000);
    });
});

document.querySelectorAll('.btn-eye').forEach(function (btn) {
    btn.addEventListener('click', function () {
        const input = document.getElementById(this.dataset.target);
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        this.querySelector('.eye-icon').style.display = isHidden ? 'none' : '';
        this.querySelector('.eye-off-icon').style.display = isHidden ? '' : 'none';
    });
});
</script>
@endpush

@endsection
