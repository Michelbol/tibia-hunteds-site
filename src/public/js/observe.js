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

let createdAtMap = new Map();
let positionTimeMap = new Map();
let lastPlayed = new Date();

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
    const now = new Date();
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
    const createdAtDate = new Date(character.online_at);
    const positionAtDate = character.position_time !== null ? new Date(character.position_time) : null;
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
    try {
        let guildName = document.getElementById('guild-name').value;
        const response = await fetch('/get-online-characters?guild_name='+guildName);
        const data = await response.json();

        clearTables();
        createdAtMap.clear();
        positionTimeMap.clear();

        const sortedCharacters = data.onlineCharacters.sort((a, b) => {
            const timeA = new Date() - new Date(a.online_at);
            const timeB = new Date() - new Date(b.online_at);
            return timeA - timeB; // ordem crescente: menor tempo online primeiro
        });
        const charactersOnlineAtMinusThanOneMinute = data.onlineCharacters.filter(character => {
            const now = new Date();
            const createdAtDate = new Date(character.online_at);
            const diffInSec = Math.floor((now - createdAtDate) / 1000);
            if (diffInSec < 60) {
                return character;
            }
        });
        playAudioIfNeeded(charactersOnlineAtMinusThanOneMinute);

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
            `Atualizado em: ${new Date().toLocaleTimeString()}`;

        updateCreatedAtTimers();
        updatePositionTimeTimers();
        document.title = sortedCharacters.length + ' Characters Online';
    } catch (error) {
        console.error('Erro ao buscar personagens:', error);
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

function playSelectedSound() {
    const select = document.getElementById('soundSelect');
    const value = select.value;
    if (!value) return;

    const audio = new Audio(SOUND_PATH + value);
    audio.play().catch(err => console.error('Erro ao tocar som:', err));
    lastPlayed = new Date();
}

function playAudioIfNeeded(charactersOnlineAtMinusThanOneMinute){
    if (charactersOnlineAtMinusThanOneMinute.length > 5) {
        const now = new Date();
        const diffInSec = Math.floor((now - lastPlayed) / 1000);
        if (diffInSec < 60) {
            return;
        }
        playSelectedSound();
    }
}

setInterval(updateCreatedAtTimers, 1000);
setInterval(updatePositionTimeTimers, 1000);
fetchOnlineCharacters();
setInterval(fetchOnlineCharacters, 1500);
