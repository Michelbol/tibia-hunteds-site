<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Online Characters - Dark Theme</title>
    <style>
        body {
            background-color: #1e1e1e;
            color: #e0e0e0;
            font-family: Arial, sans-serif;
            font-size: 13px;
            padding: 20px;
        }

        .tables-container {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .table-box {
            flex: 1;
            min-width: 300px;
        }

        h1, h2 {
            font-size: 16px;
            color: #ffffff;
            margin-bottom: 5px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 5px;
            font-size: 12px;
            background-color: #2c2c2c;
            color: #e0e0e0;
        }

        th, td {
            border: 1px solid #444;
            padding: 4px;
            text-align: left;
        }

        th {
            background-color: #333;
            color: #ffffff;
        }

        .red {
            color: #ff6b6b;
            font-weight: bold;
        }

        .yellow {
            color: #f1c40f;
            font-weight: bold;
        }

        .normal {
            color: #e0e0e0;
        }

        #lastUpdate {
            margin-bottom: 10px;
            color: #aaa;
        }
    </style>
</head>
<body>

<h1>Online Characters</h1>
<div id="lastUpdate">Atualizado agora</div>

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
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <div class="table-box">
        <h2>Makers</h2>
        <table id="makersTable">
            <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Level</th>
                <th>Vocation</th>
                <th>Tempo Online</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<script>
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

        tbody.appendChild(row);
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
            const response = await fetch('http://localhost:8000/get-online-characters');
            const data = await response.json();

            clearTables();
            createdAtMap.clear();

            const sortedCharacters = data.onlineCharacters.sort((a, b) => {
                const timeA = new Date() - new Date(a.created_at);
                const timeB = new Date() - new Date(b.created_at);
                return timeA - timeB; // ordem crescente: menor tempo online primeiro
            });

            let mainIndex = 0, bombaIndex = 0, makerIndex = 0;

            sortedCharacters.forEach(character => {
                if (character.type === 'main') {
                    addRow('mainCharTable', mainIndex++, character);
                } else if (character.type === 'bomba') {
                    addRow('bombasTable', bombaIndex++, character);
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
</script>
</body>
</html>
