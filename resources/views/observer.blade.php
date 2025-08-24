<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Online Characters - Dark Theme</title>
    <link rel="stylesheet" href="{{ asset('css/observe.css') }}">
</head>
<body>

<h1>Online Characters</h1>
<div id="lastUpdate">Atualizado agora</div>
<a href="{{ route('online-graphics-gant') }}">Análise de Players Online</a>
<div>
    <form action="{{ route('home', ['guild' => $search]) }}">
        <select name="guild_name" id="guild-name">
            @foreach($guilds as $guild)
                <option
                    value="{!! $guild['value'] !!}"
                    @if($search === $guild['value']) selected="selected" @endif
                >
                    {!! $guild['name'] !!}
                </option>
            @endforeach
        </select>
        <button type="submit">Change Guild</button>
    </form>
</div>

<div class="tables-container">
    <div class="table-box">
        <h2>Main Char</h2>
        <table id="mainCharTable">
            <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Level</th>
                <th>Vocation</th>
                <th>Tempo Online</th>
                <th>Tempo Exiva</th>
                <th>Exiva</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <div class="table-box">
        <h2>Bombas</h2>
        <table id="bombasTable">
            <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Level</th>
                <th>Vocation</th>
                <th>Tempo Online</th>
                <th>Tempo Exiva</th>
                <th>Exiva</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <div class="table-box">
        <h2>Bombao</h2>
        <table id="bombaoTable">
            <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Level</th>
                <th>Vocation</th>
                <th>Tempo Online</th>
                <th>Tempo Exiva</th>
                <th>Exiva</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <div class="table-box tabela">
        <h2>Makers</h2>
        <table id="makersTable">
            <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Level</th>
                <th>Vocation</th>
                <th>Tempo Online</th>
                <th>Tempo Exiva</th>
                <th>Exiva</th>
            </tr>
            </thead>
            <tbody>
                <div id="contextMenu" class="context-menu">
                    <div onclick="changeType('main')">Marcar como Main</div>
                    <div onclick="changeType('bomba')">Marcar como Bomba</div>
                    <div onclick="changeType('bombao')">Marcar como Bombão</div>
                    <div onclick="changeType('maker')">Marcar como Maker</div>
                    <div>
                        <input id="input-position" type="text">
                        <button type="button" onclick="changePosition()">Submit</button>
                    </div>
                </div>
            </tbody>
        </table>
    </div>
</div>

<script src="{{ asset('js/observe.js') }}"></script>
</body>
</html>
