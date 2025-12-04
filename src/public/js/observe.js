document.addEventListener("click", (event) => {
    if (event.target.tagName === 'INPUT') {
        return;
    }
    if (!document.getElementById('input-position')) return;
    document.getElementById('input-position').value = '';
    contextMenu.style.display = "none";
});
let contextMenuTarget = null;
const contextMenu = document.getElementById('contextMenu');

const SERVER_TIMESTAMP = new Date(window.SERVER_TIME + " UTC").getTime();
const LOCAL_TIMESTAMP_AT_LOAD = Date.now();
let createdAtMap = new Map();
let positionTimeMap = new Map();
let lastPlayed = serverDate();
let lastOnlineCount = 0;
let isFetching = false;

function formatToHHMMSS(seconds) {
    const h = String(Math.floor(seconds / 3600)).padStart(2, '0');
    const m = String(Math.floor((seconds % 3600) / 60)).padStart(2, '0');
    const s = String(seconds % 60).padStart(2, '0');
    return `${h}:${m}:${s}`;
}

function updateCreatedAtTimers() {
    createdAtMap.forEach(formatTimestampToTimer);
}

function updatePositionTimeTimers() {
    positionTimeMap.forEach(formatTimestampToTimer);
}

function formatTimestampToTimer(time, id) {
    if (time === null) {
        return;
    }
    const now = serverDate();
    const diffInSec = Math.max(0, Math.floor((now - time) / 1000));
    const cell = document.getElementById(id);
    if (cell) {
        cell.textContent = formatToHHMMSS(diffInSec);

        if (diffInSec < 300) {
            cell.className = 'red';
        } else if (diffInSec < 900) {
            cell.className = 'yellow';
        } else {
            cell.className = 'normal';
        }
    }
}
function clearTables() {
    document.querySelector('#mainCharTable tbody').innerHTML = '';
    document.querySelector('#bombaoTable tbody').innerHTML = '';
    document.querySelector('#bombasTable tbody').innerHTML = '';
    document.querySelector('#makersTable tbody').innerHTML = '';
}


function renderVocationImg(vocation) {
    switch (vocation) {
        case 'Master Sorcerer':
        case 'Sorcerer':
            return `<img class="vocation-icon" src="img/Sudden_Death_Rune.gif" alt="Master Sorcerer">`
        case 'Elder Druid':
        case 'Druid':
            return `<img class="vocation-icon" src="img/Paralyse_Rune.gif" alt="Elder Druid">`
        case 'Elite Knight':
        case 'Knight':
            return `<img class="vocation-icon" src="img/Dagger.gif" alt="Elite Knight">`
        case 'Paladin':
        case 'Royal Paladin':
            return `<img class="vocation-icon" src="img/Guardcatcher.gif" alt="Royal Paladin">`
        case 'Monk':
        case 'Exalted Monk':
            return `<img class="vocation-icon" src="img/Fists_of_Enlightenment.gif" alt="Exalted Monk">`
    }
}

function addRow(tableId, index, character) {
    const tbody = document.querySelector(`#${tableId} tbody`);
    const row = document.createElement('tr');
    row.className = 'vocation-tr';
    const createdAtDate = serverDate(character.online_at+ " UTC");
    const positionAtDate = character.position_time !== null ? serverDate(character.position_time) : null;
    createdAtMap.set(`created-at-${tableId}-${index}`, createdAtDate);
    positionTimeMap.set(`position-time-${tableId}-${index}`, positionAtDate);

    const imgVocation = renderVocationImg(character.vocation);
    row.innerHTML = `
        <td>#${index + 1}</td>
        <td>${character.name}</td>
        <td>${character.level}</td>
        <td>${imgVocation}<span class="vocation-text">${character.vocation}</span></td>
        <td id="created-at-${tableId}-${index}" class="normal">00:00:00</td>
        <td id="position-time-${tableId}-${index}" class="normal"></td>
        <td id="position" class="normal">${character.position ?? ''}</td>
      `;

    row.style.cursor = "pointer";
    row.title = `Clique para copiar: exiva "${character.name}"`;
    if (character.is_attacker_character) {
        row.className = 'attack-character';
    }

    row.addEventListener("click", () => {
        const text = `exiva "${character.name}"`;
        copyToClipboard(text);
        showCopyToast(`Copiado: ${text}`);
    });
    row.addEventListener("contextmenu", (event) => {
        if (!contextMenu) return;
        event.preventDefault();
        contextMenuTarget = character;
        contextMenu.style.top = `${event.pageY}px`;
        contextMenu.style.left = `${event.pageX}px`;
        contextMenu.style.display = "block";
    });

    tbody.appendChild(row);
}

function changeType(newType) {
    if (!contextMenuTarget) return;
    setCharacterType(contextMenuTarget.name, newType);
    contextMenu.style.display = "none";
}

function setAsAttacker(isAttacker) {
    if (!contextMenuTarget) return;
    setAsAttackerCharacter(contextMenuTarget.name, isAttacker);
    contextMenu.style.display = "none";
}

function changePosition() {
    if (!contextMenuTarget) return;
    if (!document.getElementById('input-position')) return;
    position = document.getElementById('input-position').value;
    setCharacterPosition(contextMenuTarget.name, position);
}
function changePositionToExit() {
    setCharacterPosition(contextMenuTarget.name, 'exit');
}

async function setCharacterType(characterName, newType) {
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const response = await fetch(`/set/${characterName}/as/${newType}`, {

            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({name: characterName})
        });

        if (!response.ok) {
            throw new Error(`Erro ao atualizar personagem: ${response.status}`);
        }

        console.log(`Personagem ${characterName} atualizado para ${newType}`);
        fetchOnlineCharacters(); // Atualiza a tabela após sucesso
    } catch (error) {
        console.error('Erro ao fazer POST:', error);
    }
}

async function setAsAttackerCharacter(characterName, isAttacker) {
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const response = await fetch(`/set/${characterName}/as/attacker/${isAttacker}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({name: characterName})
        });

        if (!response.ok) {
            throw new Error(`Erro ao atualizar personagem: ${response.status}`);
        }

        console.log(`Personagem ${characterName} atualizado para ${isAttacker}`);
        fetchOnlineCharacters(); // Atualiza a tabela após sucesso
    } catch (error) {
        console.error('Erro ao fazer POST:', error);
    }
}

async function setCharacterPosition(characterName, position) {
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const response = await fetch(`/position/${characterName}`, {

            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({position: position})
        });

        if (!response.ok) {
            throw new Error(`Erro ao atualizar posição: ${response.status}`);
        }

        console.log(`Personagem ${characterName} atualizado posição para ${position}`);
        fetchOnlineCharacters(); // Atualiza a tabela após sucesso
    } catch (error) {
        console.error('Erro ao fazer POST:', error);
    }
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        console.log(`Copiado: ${text}`);
    }).catch(err => {
        console.error('Erro ao copiar:', err);
    });
}

async function fetchOnlineCharacters() {
    if (isFetching) return;
    isFetching = true;

    try {
        let guildName = document.getElementById('guild-name').value;
        const response = await fetch('/get-online-characters?guild_name='+guildName);
        const data = await response.json();

        const charactersOnlineAtMinusThanOneMinute = data.onlineCharacters.filter(character => {
            const now = serverDate();
            const createdAtDate = serverDate(character.online_at);
            const diffInSec = Math.floor((now - createdAtDate) / 1000);
            if (diffInSec < 60 && character.level > 32) {
                return character;
            }
        });
        handleCountIncreaseAndPlay(lastOnlineCount, charactersOnlineAtMinusThanOneMinute.length);

        lastOnlineCount = charactersOnlineAtMinusThanOneMinute.length;
        clearTables();
        createdAtMap.clear();
        positionTimeMap.clear();

        const sortedCharacters = data.onlineCharacters.sort((a, b) => {
            const timeA = serverDate() - serverDate(a.online_at);
            const timeB = serverDate() - serverDate(b.online_at);
            return timeA - timeB; // ordem crescente: menor tempo online primeiro
        });

        let mainIndex = 0, bombaIndex = 0, bombaoIndex = 0, makerIndex = 0;

        sortedCharacters.forEach(character => {
            if (character.type === 'main') {
                addRow('mainCharTable', mainIndex++, character);
            } else if (character.type === 'bomba') {
                addRow('bombasTable', bombaIndex++, character);
            } else if (character.type === 'bombao') {
                addRow('bombaoTable', bombaoIndex++, character);
            } else {
                addRow('makersTable', makerIndex++, character);
            }
        });

        document.getElementById('lastUpdate').textContent =
            `Atualizado em: ${serverDate().toLocaleTimeString()}`;

        updateCreatedAtTimers();
        updatePositionTimeTimers();
        document.title = sortedCharacters.length + ' Characters Online';
    } catch (error) {
        console.error('Erro ao buscar personagens:', error);
    } finally {
        isFetching = false;
    }
}

function copySio() {
    let characterName = contextMenuTarget.name;
    copyToClipboard(`exura sio "${characterName}"`)
}

function getToastElement() {
    let el = document.getElementById('copyToast');
    if (!el) {
        el = document.createElement('div');
        el.id = 'copyToast';
        el.className = 'copy-toast';
        document.body.appendChild(el);
    }
    return el;
}

function showCopyToast(message, duration = 2000) {
    const el = getToastElement();
    el.textContent = message;
    void el.offsetWidth;
    el.classList.add('show');

    clearTimeout(el._hideTimeout);
    el._hideTimeout = setTimeout(() => {
        el.classList.remove('show');
    }, duration);
}

const SOUND_PATH = '/sounds/';

function startPolling() {
    async function loop() {
        await fetchOnlineCharacters();
        setTimeout(loop, 1500);
    }
    loop();
}

setInterval(updateCreatedAtTimers, 1000);
setInterval(updatePositionTimeTimers, 1000);
fetchOnlineCharacters();
startPolling();

// --- variáveis globais ---
let audioContext = null;
let audioBufferCache = new Map();
const DEFAULT_SOUND = 'alert.mp3';

// --- init: chamado após o clique do usuário ---
async function initAudioOnUserGesture() {
    try {
        // AudioContext
        audioContext = new (window.AudioContext || window.webkitAudioContext)();
        // pede permissão de Notificações
        if ('Notification' in window && Notification.permission === 'default') {
            await Notification.requestPermission();
        }
        // tenta Wake Lock (opcional e requer HTTPS)
        try {
            if ('wakeLock' in navigator) {
                await navigator.wakeLock.request('screen'); // previne apagar da tela (se suportado)
            }
        } catch (err) {
            console.warn('WakeLock não disponível ou requisition falhou:', err);
        }
        // oculta modal
        const modal = document.getElementById('audio-permission-modal');
        if (modal) modal.style.display = 'none';
    } catch (err) {
        console.error('Erro ao inicializar audio:', err);
    }
}

// --- carregador de sound em buffer (Web Audio) ---
async function loadSoundBuffer(filename) {
    if (!audioContext) return null;
    if (audioBufferCache.has(filename)) return audioBufferCache.get(filename);
    const url = SOUND_PATH + filename;
    try {
        const resp = await fetch(url);
        const arr = await resp.arrayBuffer();
        const buffer = await audioContext.decodeAudioData(arr);
        audioBufferCache.set(filename, buffer);
        return buffer;
    } catch (err) {
        console.error('Falha ao carregar som', url, err);
        return null;
    }
}

// --- toca som usando buffer (permite overlap) ---
function playBuffer(buffer) {
    if (!audioContext || !buffer) return;
    const src = audioContext.createBufferSource();
    src.buffer = buffer;
    src.connect(audioContext.destination);
    src.start(0);
}

// --- função que toca N vezes com 0.5s entre cada ---
async function playSelectedSoundNTimes(times, filename = DEFAULT_SOUND) {
    // rate-limit simples: evita tocar com muita frequência globalmente (por ex 1s)
    const now = Date.now();
    if (now - lastPlayed < 300) {
        // permite curto, mas evita floods
    }
    lastPlayed = now;

    // // se a aba está oculta, tenta notificar em vez de tocar (fallback)
    // if (document.hidden) {
    //     // se permissões dadas, cria notificações para não depender de audio em background
    //     if ('Notification' in window && Notification.permission === 'granted') {
    //         for (let i = 0; i < times; i++) {
    //             setTimeout(() => {
    //                 new Notification('Alert', { body: 'Novos caracteres online', tag: 'monitor-alert' });
    //             }, i * 500);
    //         }
    //     } else {
    //         console.warn('Página oculta e sem permissão de Notification - som ignorado');
    //     }
    // }

    if (audioContext && audioContext.state === 'suspended') {
        try { await audioContext.resume(); } catch (e) { console.warn('Falha ao resumir audioContext', e); }
    }

    const buffer = await loadSoundBuffer(filename);
    if (!buffer) return;

    for (let i = 0; i < times; i++) {
        setTimeout(() => {
            try { playBuffer(buffer); } catch (e) { console.error('Erro ao tocar buffer', e); }
        }, i * 500);
    }
}

function handleCountIncreaseAndPlay(prevCount, currentCount) {
    const prevBlocks = Math.floor(prevCount / 5);
    const currBlocks = Math.floor(currentCount / 5);
    if (currBlocks > prevBlocks) {
        const timesToPlay = currBlocks - prevBlocks;
        const select = document.getElementById('soundSelect');
        const value = select.value;
        playSelectedSoundNTimes(timesToPlay, value);
    }
}

function serverDate(...args) {

    if (args.length > 0) {
        return new Date(...args);
    }

    const nowLocal = Date.now();
    const diff = nowLocal - LOCAL_TIMESTAMP_AT_LOAD;
    return new Date(SERVER_TIMESTAMP + diff);
}
