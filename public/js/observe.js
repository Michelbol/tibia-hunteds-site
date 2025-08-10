document.addEventListener("click", () => {
    contextMenu.style.display = "none";
});
let contextMenuTarget = null;
const contextMenu = document.getElementById('contextMenu');

let createdAtMap = new Map();

function formatToHHMMSS(seconds) {
    const h = String(Math.floor(seconds / 3600)).padStart(2, '0');
    const m = String(Math.floor((seconds % 3600) / 60)).padStart(2, '0');
    const s = String(seconds % 60).padStart(2, '0');
    return `${h}:${m}:${s}`;
}

function updateCreatedAtTimers() {
    const now = new Date();
    createdAtMap.forEach((createdAt, id) => {
        const diffInSec = Math.floor((now - createdAt) / 1000);
        const cell = document.getElementById(`created-at-${id}`);
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
    });
}

function clearTables() {
    document.querySelector('#mainCharTable tbody').innerHTML = '';
    document.querySelector('#bombaoTable tbody').innerHTML = '';
    document.querySelector('#bombasTable tbody').innerHTML = '';
    document.querySelector('#makersTable tbody').innerHTML = '';
}

function addRow(tableId, index, character) {
    const tbody = document.querySelector(`#${tableId} tbody`);
    const row = document.createElement('tr');
    const createdAtDate = new Date(character.online_at);
    createdAtMap.set(`${tableId}-${index}`, createdAtDate);

    row.innerHTML = `
        <td>#${index + 1}</td>
        <td>${character.name}</td>
        <td>${character.level}</td>
        <td>${character.vocation}</td>
        <td id="created-at-${tableId}-${index}" class="normal">00:00:00</td>
      `;

    row.style.cursor = "pointer";
    row.title = `Clique para copiar: exiva "${character.name}"`;

    row.addEventListener("click", () => {
        const text = `exiva "${character.name}"`;
        copyToClipboard(text);
    });

    row.addEventListener("contextmenu", (event) => {
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

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        console.log(`Copiado: ${text}`);
    }).catch(err => {
        console.error('Erro ao copiar:', err);
    });
}

async function fetchOnlineCharacters() {
    try {
        const response = await fetch('/get-online-characters');
        const data = await response.json();

        clearTables();
        createdAtMap.clear();

        const sortedCharacters = data.onlineCharacters.sort((a, b) => {
            const timeA = new Date() - new Date(a.online_at);
            const timeB = new Date() - new Date(b.online_at);
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
            `Atualizado em: ${new Date().toLocaleTimeString()}`;

        updateCreatedAtTimers();
    } catch (error) {
        console.error('Erro ao buscar personagens:', error);
    }
}

setInterval(updateCreatedAtTimers, 1000);
fetchOnlineCharacters();
setInterval(fetchOnlineCharacters, 3000);
