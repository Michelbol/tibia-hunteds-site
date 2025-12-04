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
    <select id="soundSelect" onchange="playSelectedSoundNTimes(1, this.value)">
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
                <div onclick="changePositionToExit()">Char no exit</div>
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
<div id="audio-permission-modal" style="
  position: fixed;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.8);
  backdrop-filter: blur(4px);
  z-index: 9999;
  color: #f1f1f1;
  font-family: Arial, sans-serif;
">
    <div style="
    background: #1e1e1e;
    border: 1px solid #333;
    padding: 24px 32px;
    border-radius: 12px;
    box-shadow: 0 0 20px rgba(0,0,0,0.4);
    text-align: center;
    max-width: 400px;
  ">
        <h3 style="margin-bottom: 12px;">Bem vindo ao Site de Hunteds</h3>
        <p style="margin-bottom: 20px; color: #bbb; font-size: 14px;">
            Ativae ou se não, não vai falar "logaram bombas"
        </p>
        <button id="enable-audio-btn"
                onclick="initAudioOnUserGesture()"
                style="
      background: #0078ff;
      color: white;
      border: none;
      padding: 10px 20px;
      font-size: 15px;
      border-radius: 8px;
      cursor: pointer;
      transition: 0.2s;
    ">Ativar</button>
    </div>
</div>

<script>
    window.SERVER_TIME = "{{ now()->format('Y-m-d H:i:s') }}";
</script>
<script src="{{ asset('js/observe.js?v=20251203183200') }}"></script>
</body>
</html>
