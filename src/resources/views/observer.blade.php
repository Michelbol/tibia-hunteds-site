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
<div>
    <input readonly disabled name="guild_name" id="guild-name" value="{{ $guilds[0]['name'] }}">
    <select id="soundSelect" onchange="playSelectedSound()">
        <option>Sem Som</option>
        <option selected value="logaram_bombas.mp3">Logaram bombas 1</option>
        <option value="logaram_bombas_voz_grossa.mp3">Logaram bombas 2</option>
        <option value="logaram_bombas_veia.mp3">Logaram bombas veia</option>
        <option value="lombraram gombas.mp3">Lombraram Gombas</option>
        <option value="roubaram_bombas.mp3">Roubaram bombas</option>
        <option value="Ivaudio1.mp3">Ivaudio 1</option>
        <option value="Ivaudio2.mp3">Ivaudio 2</option>
        <option value="Ivaudio3.m4a">Ivaudio 3</option>
        <option value="Ivaudio4.mp3">Ivaudio 4</option>
        <option value="Ivaudio5.mp3">Ivaudio 5</option>
    </select>
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
            @if(\Illuminate\Support\Facades\Auth::hasUser())
            <div id="contextMenu" class="context-menu">
                <div onclick="changeType('main')">Marcar como Main</div>
                <div onclick="changeType('bomba')">Marcar como Bomba</div>
                <div onclick="changeType('bombao')">Marcar como Bombão</div>
                <div onclick="changeType('maker')">Marcar como Maker</div>
                <div onclick="setAsAttacker(true)">Marcar como Char de Atk</div>
                <div onclick="setAsAttacker(false)">Desmarcar como Char de Atk</div>
                <div onclick="copySio()">Copiar Sio</div>
                <div>
                    <input id="input-position" type="text">
                    <button type="button" onclick="changePosition()">Submit</button>
                </div>
            </div>
            @endif
            </tbody>
        </table>
    </div>
</div>

<script src="{{ asset('js/observe.js?v=202511081442') }}"></script>
</body>
</html>
