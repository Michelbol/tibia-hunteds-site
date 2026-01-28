@extends('layouts.default')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/settings.css') }}">
@endpush

@section('content')
    <div class="page">
        <form id="guildForm" class="card" method="POST" action="{{ route('settings.save') }}">
            <h2>Configurar Guild</h2>
            @csrf

            <label for="guild_name">
                Guild Name
                <span class="tooltip-wrapper">
            ⚠️
            <span class="tooltip">
                Para validar, o link gerado abaixo deve mostrar a página da guild
            </span>
        </span>
            </label>
            <input
                type="text"
                id="guild_name"
                name="guild_name"
                placeholder="Ex: Outlaw Warlords"
                autocomplete="off"
                value="{{$guildName}}"
            >

            <a
                id="guildLink"
                href="#"
                target="_blank"
                class="guild-link disabled"
            >
                Clique aqui para validar o guild name
            </a>

            <button type="submit" class="btn-save">
                Salvar
            </button>
        </form>
    </div>

    <div id="loadingOverlay">
        <div class="spinner"></div>
        <span>Salvando...</span>
    </div>

    <script src="{{ asset('js/settings.js?v=202601272130') }}"></script>
@endsection
