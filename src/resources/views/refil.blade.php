<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Reposição de Itens</title>

    <style>
        body {
            background-color: #0f0f0f;
            font-family: Arial, Helvetica, sans-serif;
            color: #ddd;
        }

        .container {
            width: 1000px;
            margin: 40px auto;
        }

        .card {
            background-color: #1a1a1a;
            border: 1px solid #2a2a2a;
            padding: 20px;
            border-radius: 6px;
        }

        h2 {
            color: #ffffff;
            margin-bottom: 15px;
        }

        select, input {
            background-color: #121212;
            border: 1px solid #333;
            color: #fff;
            padding: 8px;
            border-radius: 4px;
        }

        button {
            background-color: #2f2f2f;
            border: 1px solid #444;
            color: #fff;
            padding: 8px 12px;
            cursor: pointer;
        }

        button:hover {
            background-color: #3a3a3a;
        }

        .tables {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }

        .section {
            width: 50%;
        }

        .section h3 {
            color: #00ff88;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #2a2a2a;
            padding: 10px;
            border-bottom: 1px solid #444;
            text-align: left;
        }

        td {
            padding: 8px;
            border-bottom: 1px solid #2a2a2a;
            vertical-align: middle;
        }

        tr:hover {
            background-color: #222;
        }

        .item-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .item-cell img {
            width: 32px;
            height: 32px;
            image-rendering: pixelated;
        }

        /* SWITCH */
        .switch {
            position: relative;
            display: inline-block;
            width: 42px;
            height: 22px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #444;
            transition: .3s;
            border-radius: 30px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .3s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #00ff88;
        }

        input:checked + .slider:before {
            transform: translateX(20px);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">

        <h2>Reposição de Itens</h2>

        <label>Vocação:</label>
        <select id="vocacao">
            <option value="">Selecione</option>
            <option value="Mage">Mage</option>
            <option value="Paladin">Paladin</option>
        </select>

        <label style="margin-left:20px;">Level:</label>
        <input type="number" id="level" min="1"/>

        <button onclick="gerarLista()">Gerar Lista</button>
        <div style="margin-top:15px;">
            <strong>Capacidade Total:</strong>
            <span id="capacidade">0</span> oz
        </div>


        <div class="tables">

            <div class="section">
                <h3>Pendentes</h3>
                <table>
                    <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qtd</th>
                        <th>Peso Unit.</th>
                        <th>Total</th>
                        <th>OK</th>
                    </tr>
                    </thead>
                    <tbody id="pendentes"></tbody>
                </table>
                <div style="margin-top:8px;">
                    <strong>Total Peso:</strong>
                    <span id="peso-pendentes">0</span> oz
                </div>
            </div>

            <div class="section">
                <h3>Conferidos</h3>
                <table>
                    <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qtd</th>
                        <th>Peso Unit.</th>
                        <th>Total</th>
                        <th>OK</th>
                    </tr>
                    </thead>
                    <tbody id="conferidos"></tbody>
                </table>
                <div style="margin-top:8px;">
                    <strong>Total Peso:</strong>
                    <span id="peso-conferidos">0</span> oz
                </div>

            </div>

        </div>
    </div>
</div>

<script>
    const tabelas = {
        Mage: [
            {
                min: 600,
                max: 900,
                itens: [

                    // Potions
                    {nome: "Ultimate Mana Potion", qtd: 700, peso: 3.1, img: "/img/Ultimate_Mana_Potion.gif"},
                    {nome: "Magic Shield Potion", qtd: 5, peso: 3.2, img: "/img/Magic_Shield_Potion.gif"},

                    // Runes
                    {nome: "Avalanche Rune", qtd: 150, peso: 0.52, img: "/img/Avalanche_Rune.gif"},
                    {nome: "Sudden Death Rune", qtd: 300, peso: 0.70, img: "/img/Sudden_Death_Rune.gif"},
                    {nome: "Magic Wall Rune", qtd: 200, peso: 0.70, img: "/img/Magic_Wall_Rune.gif"},
                    {nome: "Wild Growth Rune", qtd: 100, peso: 1.05, img: "/img/Wild_Growth_Rune.gif"},
                    {nome: "Destroy Field Rune", qtd: 50, peso: 0.70, img: "/img/Destroy_Field_Rune.gif"},
                    {nome: "Disintegrate Rune", qtd: 50, peso: 0.70, img: "/img/Disintegrate_Rune.gif"},
                    {nome: "Poison Bomb Rune", qtd: 20, peso: 1.05, img: "/img/Poison_Bomb_Rune.gif"},
                    {nome: "Fire Bomb Rune", qtd: 20, peso: 1.05, img: "/img/Fire_Bomb_Rune.gif"},
                    {nome: "Energy Bomb Rune", qtd: 20, peso: 1.05, img: "/img/Energy_Bomb_Rune.gif"},
                    {nome: "Paralyse Rune", qtd: 50, peso: 2.1, img: "/img/Paralyse_Rune.gif"},

                    // Rings & Amulets
                    {nome: "Prismatic Ring", qtd: 2, peso: 1.05, img: "/img/Prismatic_Ring.gif"},
                    {nome: "Time Ring", qtd: 10, peso: 0.9, img: "/img/Time_Ring.gif"},
                    {nome: "Might Ring", qtd: 20, peso: 1.0, img: "/img/Might_Ring.gif"},
                    {nome: "Dwarven Ring", qtd: 5, peso: 1.1, img: "/img/Dwarven_Ring.gif"},
                    {nome: "Stone Skin Amulet", qtd: 20, peso: 7.0, img: "/img/Stone_Skin_Amulet.gif"},
                    {nome: "Prismatic Necklace", qtd: 5, peso: 6.5, img: "/img/Prismatic_Necklace.gif"},
                    {nome: "Gill Necklace", qtd: 5, peso: 4.1, img: "/img/Gill_Necklace.gif"},
                    {nome: "Glacier Amulet", qtd: 5, peso: 5.0, img: "/img/Glacier_Amulet.gif"},

                    // Equipment
                    {nome: "Tiara of Power", qtd: 2, peso: 11.5, img: "/img/Tiara_of_Power.gif"},
                    {nome: "Void Boots", qtd: 2, peso: 15, img: "/img/Void_Boots.gif"},

                    // Outros
                    {nome: "Honey Flower", qtd: 20, peso: 10, img: "/img/Honey_Flower.gif"},

                    // Sistema (sem peso físico)
                    {nome: "Frag", qtd: 1, peso: 0, img: "/img/Red_Skull.gif"},
                    {nome: "Bless", qtd: 1, peso: 0, img: "/img/Amulet_of_Loss.gif"},
                    {nome: "Crítico", qtd: 1, peso: 0, img: "/img/Strike_(Dano_Crítico).gif"}

                ]
            },
            {
                min: 500,
                max: 600,
                itens: [

                    // Potions
                    {nome: "Ultimate Mana Potion", qtd: 500, peso: 3.1, img: "/img/Ultimate_Mana_Potion.gif"},
                    {nome: "Magic Shield Potion", qtd: 5, peso: 3.2, img: "/img/Magic_Shield_Potion.gif"},

                    // Runes
                    {nome: "Avalanche Rune", qtd: 100, peso: 0.52, img: "/img/Avalanche_Rune.gif"},
                    {nome: "Sudden Death Rune", qtd: 200, peso: 0.70, img: "/img/Sudden_Death_Rune.gif"},
                    {nome: "Magic Wall Rune", qtd: 100, peso: 0.70, img: "/img/Magic_Wall_Rune.gif"},
                    {nome: "Wild Growth Rune", qtd: 50, peso: 1.05, img: "/img/Wild_Growth_Rune.gif"},
                    {nome: "Destroy Field Rune", qtd: 20, peso: 0.70, img: "/img/Destroy_Field_Rune.gif"},
                    {nome: "Disintegrate Rune", qtd: 20, peso: 0.70, img: "/img/Disintegrate_Rune.gif"},
                    {nome: "Poison Bomb Rune", qtd: 20, peso: 1.05, img: "/img/Poison_Bomb_Rune.gif"},
                    {nome: "Fire Bomb Rune", qtd: 20, peso: 1.05, img: "/img/Fire_Bomb_Rune.gif"},
                    {nome: "Energy Bomb Rune", qtd: 20, peso: 1.05, img: "/img/Energy_Bomb_Rune.gif"},
                    {nome: "Paralyse Rune", qtd: 50, peso: 2.1, img: "/img/Paralyse_Rune.gif"},

                    // Rings & Amulets
                    // {nome: "Prismatic Ring", qtd: 2, peso: 1.05, img: "/img/Prismatic_Ring.gif"},
                    {nome: "Time Ring", qtd: 10, peso: 0.9, img: "/img/Time_Ring.gif"},
                    {nome: "Might Ring", qtd: 20, peso: 1.0, img: "/img/Might_Ring.gif"},
                    {nome: "Dwarven Ring", qtd: 5, peso: 1.1, img: "/img/Dwarven_Ring.gif"},
                    {nome: "Stone Skin Amulet", qtd: 20, peso: 7.0, img: "/img/Stone_Skin_Amulet.gif"},
                    // {nome: "Prismatic Necklace", qtd: 5, peso: 6.5, img: "/img/Prismatic_Necklace.gif"},
                    {nome: "Gill Necklace", qtd: 2, peso: 4.1, img: "/img/Gill_Necklace.gif"},
                    {nome: "Glacier Amulet", qtd: 5, peso: 5.0, img: "/img/Glacier_Amulet.gif"},

                    // Equipment
                    {nome: "Tiara of Power", qtd: 2, peso: 11.5, img: "/img/Tiara_of_Power.gif"},
                    {nome: "Void Boots", qtd: 2, peso: 15, img: "/img/Void_Boots.gif"},

                    // Outros
                    {nome: "Honey Flower", qtd: 20, peso: 10, img: "/img/Honey_Flower.gif"},

                    // Sistema (sem peso físico)
                    {nome: "Frag", qtd: 1, peso: 0, img: "/img/Red_Skull.gif"},
                    {nome: "Bless", qtd: 1, peso: 0, img: "/img/Amulet_of_Loss.gif"},
                    {nome: "Crítico", qtd: 1, peso: 0, img: "/img/Strike_(Dano_Crítico).gif"}

                ]
            },
            {
                min: 300,
                max: 400,
                itens: [
                    // Potions
                    {nome: "Ultimate Mana Potion", qtd: 400, peso: 3.1, img: "/img/Ultimate_Mana_Potion.gif"},
                    {nome: "Magic Shield Potion", qtd: 5, peso: 3.2, img: "/img/Magic_Shield_Potion.gif"},

                    // Runes
                    {nome: "Avalanche Rune", qtd: 100, peso: 0.52, img: "/img/Avalanche_Rune.gif"},
                    {nome: "Sudden Death Rune", qtd: 200, peso: 0.70, img: "/img/Sudden_Death_Rune.gif"},
                    {nome: "Magic Wall Rune", qtd: 100, peso: 0.70, img: "/img/Magic_Wall_Rune.gif"},
                    {nome: "Wild Growth Rune", qtd: 20, peso: 1.05, img: "/img/Wild_Growth_Rune.gif"},
                    {nome: "Destroy Field Rune", qtd: 20, peso: 0.70, img: "/img/Destroy_Field_Rune.gif"},
                    {nome: "Disintegrate Rune", qtd: 20, peso: 0.70, img: "/img/Disintegrate_Rune.gif"},
                    {nome: "Poison Bomb Rune", qtd: 20, peso: 1.05, img: "/img/Poison_Bomb_Rune.gif"},
                    {nome: "Fire Bomb Rune", qtd: 20, peso: 1.05, img: "/img/Fire_Bomb_Rune.gif"},
                    {nome: "Energy Bomb Rune", qtd: 20, peso: 1.05, img: "/img/Energy_Bomb_Rune.gif"},
                    {nome: "Paralyse Rune", qtd: 50, peso: 2.1, img: "/img/Paralyse_Rune.gif"},

                    // Rings & Amulets
                    // {nome: "Prismatic Ring", qtd: 2, peso: 1.05, img: "/img/Prismatic_Ring.gif"},
                    {nome: "Time Ring", qtd: 10, peso: 0.9, img: "/img/Time_Ring.gif"},
                    {nome: "Might Ring", qtd: 20, peso: 1.0, img: "/img/Might_Ring.gif"},
                    {nome: "Dwarven Ring", qtd: 5, peso: 1.1, img: "/img/Dwarven_Ring.gif"},
                    {nome: "Stone Skin Amulet", qtd: 20, peso: 7.0, img: "/img/Stone_Skin_Amulet.gif"},
                    // {nome: "Prismatic Necklace", qtd: 5, peso: 6.5, img: "/img/Prismatic_Necklace.gif"},
                    {nome: "Gill Necklace", qtd: 2, peso: 4.1, img: "/img/Gill_Necklace.gif"},
                    {nome: "Glacier Amulet", qtd: 5, peso: 5.0, img: "/img/Glacier_Amulet.gif"},

                    // Equipment
                    {nome: "Tiara of Power", qtd: 2, peso: 11.5, img: "/img/Tiara_of_Power.gif"},
                    {nome: "Void Boots", qtd: 2, peso: 15, img: "/img/Void_Boots.gif"},

                    // Outros
                    {nome: "Honey Flower", qtd: 20, peso: 10, img: "/img/Honey_Flower.gif"},

                    // Sistema (sem peso físico)
                    {nome: "Frag", qtd: 1, peso: 0, img: "/img/Red_Skull.gif"},
                    {nome: "Bless", qtd: 1, peso: 0, img: "/img/Amulet_of_Loss.gif"},
                    {nome: "Crítico", qtd: 1, peso: 0, img: "/img/Strike_(Dano_Crítico).gif"}

                ]
            },
            {
                min: 201,
                max: 300,
                itens: [
                    // Potions
                    {nome: "Ultimate Mana Potion", qtd: 300, peso: 3.1, img: "/img/Ultimate_Mana_Potion.gif"},
                    // {nome: "Magic Shield Potion", qtd: 5, peso: 3.2, img: "/img/Magic_Shield_Potion.gif"},

                    // Runes
                    {nome: "Avalanche Rune", qtd: 50, peso: 0.52, img: "/img/Avalanche_Rune.gif"},
                    {nome: "Sudden Death Rune", qtd: 200, peso: 0.70, img: "/img/Sudden_Death_Rune.gif"},
                    {nome: "Magic Wall Rune", qtd: 50, peso: 0.70, img: "/img/Magic_Wall_Rune.gif"},
                    {nome: "Wild Growth Rune", qtd: 20, peso: 1.05, img: "/img/Wild_Growth_Rune.gif"},
                    {nome: "Destroy Field Rune", qtd: 20, peso: 0.70, img: "/img/Destroy_Field_Rune.gif"},
                    {nome: "Disintegrate Rune", qtd: 20, peso: 0.70, img: "/img/Disintegrate_Rune.gif"},
                    {nome: "Poison Bomb Rune", qtd: 20, peso: 1.05, img: "/img/Poison_Bomb_Rune.gif"},
                    {nome: "Fire Bomb Rune", qtd: 20, peso: 1.05, img: "/img/Fire_Bomb_Rune.gif"},
                    {nome: "Energy Bomb Rune", qtd: 20, peso: 1.05, img: "/img/Energy_Bomb_Rune.gif"},
                    {nome: "Paralyse Rune", qtd: 50, peso: 2.1, img: "/img/Paralyse_Rune.gif"},

                    // Rings & Amulets
                    // {nome: "Prismatic Ring", qtd: 2, peso: 1.05, img: "/img/Prismatic_Ring.gif"},
                    {nome: "Time Ring", qtd: 10, peso: 0.9, img: "/img/Time_Ring.gif"},
                    // {nome: "Might Ring", qtd: 20, peso: 1.0, img: "/img/Might_Ring.gif"},
                    {nome: "Dwarven Ring", qtd: 5, peso: 1.1, img: "/img/Dwarven_Ring.gif"},
                    // {nome: "Stone Skin Amulet", qtd: 20, peso: 7.0, img: "/img/Stone_Skin_Amulet.gif"},
                    // {nome: "Prismatic Necklace", qtd: 5, peso: 6.5, img: "/img/Prismatic_Necklace.gif"},
                    // {nome: "Gill Necklace", qtd: 2, peso: 4.1, img: "/img/Gill_Necklace.gif"},
                    {nome: "Glacier Amulet", qtd: 5, peso: 5.0, img: "/img/Glacier_Amulet.gif"},

                    // Equipment
                    {nome: "Tiara of Power", qtd: 2, peso: 11.5, img: "/img/Tiara_of_Power.gif"},
                    {nome: "Void Boots", qtd: 2, peso: 15, img: "/img/Void_Boots.gif"},

                    // Outros
                    {nome: "Honey Flower", qtd: 20, peso: 10, img: "/img/Honey_Flower.gif"},

                    // Sistema (sem peso físico)
                    {nome: "Frag", qtd: 1, peso: 0, img: "/img/Red_Skull.gif"},
                    {nome: "Bless", qtd: 1, peso: 0, img: "/img/Amulet_of_Loss.gif"},
                    {nome: "Crítico", qtd: 1, peso: 0, img: "/img/Strike_(Dano_Crítico).gif"}

                ]
            },
            {
                min: 130,
                max: 200,
                itens: [
                    // Potions
                    {nome: "Ultimate Mana Potion", qtd: 200, peso: 3.1, img: "/img/Ultimate_Mana_Potion.gif"},
                    // {nome: "Magic Shield Potion", qtd: 5, peso: 3.2, img: "/img/Magic_Shield_Potion.gif"},

                    // Runes
                    {nome: "Avalanche Rune", qtd: 50, peso: 0.52, img: "/img/Avalanche_Rune.gif"},
                    {nome: "Sudden Death Rune", qtd: 200, peso: 0.70, img: "/img/Sudden_Death_Rune.gif"},
                    {nome: "Magic Wall Rune", qtd: 50, peso: 0.70, img: "/img/Magic_Wall_Rune.gif"},
                    {nome: "Wild Growth Rune", qtd: 20, peso: 1.05, img: "/img/Wild_Growth_Rune.gif"},
                    {nome: "Destroy Field Rune", qtd: 20, peso: 0.70, img: "/img/Destroy_Field_Rune.gif"},
                    {nome: "Disintegrate Rune", qtd: 20, peso: 0.70, img: "/img/Disintegrate_Rune.gif"},
                    {nome: "Poison Bomb Rune", qtd: 20, peso: 1.05, img: "/img/Poison_Bomb_Rune.gif"},
                    {nome: "Fire Bomb Rune", qtd: 20, peso: 1.05, img: "/img/Fire_Bomb_Rune.gif"},
                    {nome: "Energy Bomb Rune", qtd: 20, peso: 1.05, img: "/img/Energy_Bomb_Rune.gif"},
                    {nome: "Paralyse Rune", qtd: 50, peso: 2.1, img: "/img/Paralyse_Rune.gif"},

                    // Rings & Amulets
                    // {nome: "Prismatic Ring", qtd: 2, peso: 1.05, img: "/img/Prismatic_Ring.gif"},
                    {nome: "Time Ring", qtd: 10, peso: 0.9, img: "/img/Time_Ring.gif"},
                    // {nome: "Might Ring", qtd: 20, peso: 1.0, img: "/img/Might_Ring.gif"},
                    {nome: "Dwarven Ring", qtd: 5, peso: 1.1, img: "/img/Dwarven_Ring.gif"},
                    // {nome: "Stone Skin Amulet", qtd: 20, peso: 7.0, img: "/img/Stone_Skin_Amulet.gif"},
                    // {nome: "Prismatic Necklace", qtd: 5, peso: 6.5, img: "/img/Prismatic_Necklace.gif"},
                    // {nome: "Gill Necklace", qtd: 2, peso: 4.1, img: "/img/Gill_Necklace.gif"},
                    {nome: "Glacier Amulet", qtd: 5, peso: 5.0, img: "/img/Glacier_Amulet.gif"},

                    // Equipment
                    {nome: "Tiara of Power", qtd: 2, peso: 11.5, img: "/img/Tiara_of_Power.gif"},
                    {nome: "Void Boots", qtd: 2, peso: 15, img: "/img/Void_Boots.gif"},

                    // Outros
                    {nome: "Honey Flower", qtd: 20, peso: 10, img: "/img/Honey_Flower.gif"},

                    // Sistema (sem peso físico)
                    {nome: "Frag", qtd: 1, peso: 0, img: "/img/Red_Skull.gif"},
                    {nome: "Bless", qtd: 1, peso: 0, img: "/img/Amulet_of_Loss.gif"},
                    {nome: "Crítico", qtd: 1, peso: 0, img: "/img/Strike_(Dano_Crítico).gif"}

                ]
            },
        ],
        Paladin: [
            {
                min: 500,
                max: 800,
                itens: [
                    //Potions
                    {nome: "Ultimate Spirit Potion", qtd: 600, peso: 3.1, img: "/img/Ultimate_Spirit_Potion.gif"},
                    {nome: "Great Mana Potion", qtd: 600, peso: 3.1, img: "/img/Great_Mana_Potion.gif"},

                    //Ammunition
                    {nome: "Diamond Arrow", qtd: 200, peso: 0.8, img: "/img/Diamond_Arrow.gif"},
                    {nome: "Spectral Bolt", qtd: 1000, peso: 0.9, img: "/img/Spectral_Bolt.gif"},

                    // Runes
                    {nome: "Avalanche Rune", qtd: 150, peso: 0.52, img: "/img/Avalanche_Rune.gif"},
                    {nome: "Sudden Death Rune", qtd: 300, peso: 0.70, img: "/img/Sudden_Death_Rune.gif"},
                    {nome: "Magic Wall Rune", qtd: 200, peso: 0.70, img: "/img/Magic_Wall_Rune.gif"},
                    {nome: "Destroy Field Rune", qtd: 50, peso: 0.70, img: "/img/Destroy_Field_Rune.gif"},
                    {nome: "Disintegrate Rune", qtd: 50, peso: 0.70, img: "/img/Disintegrate_Rune.gif"},
                    {nome: "Poison Bomb Rune", qtd: 20, peso: 1.05, img: "/img/Poison_Bomb_Rune.gif"},
                    {nome: "Fire Bomb Rune", qtd: 20, peso: 1.05, img: "/img/Fire_Bomb_Rune.gif"},
                    {nome: "Energy Bomb Rune", qtd: 20, peso: 1.05, img: "/img/Energy_Bomb_Rune.gif"},

                    // Rings & Amulets
                    {nome: "Prismatic Ring", qtd: 1, peso: 1.05, img: "/img/Prismatic_Ring.gif"},
                    {nome: "Time Ring", qtd: 10, peso: 0.9, img: "/img/Time_Ring.gif"},
                    {nome: "Energy Ring", qtd: 5, peso: 0.8, img: "/img/Energy_Ring.gif"},
                    {nome: "Might Ring", qtd: 20, peso: 1.0, img: "/img/Might_Ring.gif"},
                    {nome: "Dwarven Ring", qtd: 5, peso: 1.1, img: "/img/Dwarven_Ring.gif"},
                    {nome: "Stone Skin Amulet", qtd: 20, peso: 7.0, img: "/img/Stone_Skin_Amulet.gif"},
                    {nome: "Prismatic Necklace", qtd: 2, peso: 6.5, img: "/img/Prismatic_Necklace.gif"},
                    {nome: "Gill Necklace", qtd: 2, peso: 4.1, img: "/img/Gill_Necklace.gif"},
                    {nome: "Glacier Amulet", qtd: 5, peso: 5.0, img: "/img/Glacier_Amulet.gif"},
                    {nome: "Stealth Ring", qtd: 5, peso: 5.0, img: "/img/Stealth_Ring.gif"},

                    // Equipment
                    {nome: "Void Boots", qtd: 2, peso: 15, img: "/img/Void_Boots.gif"},

                    // Outros
                    {nome: "Honey Flower", qtd: 20, peso: 10, img: "/img/Honey_Flower.gif"},

                    // Sistema (sem peso físico)
                    {nome: "Frag", qtd: 1, peso: 0, img: "/img/Red_Skull.gif"},
                    {nome: "Bless", qtd: 1, peso: 0, img: "/img/Amulet_of_Loss.gif"},
                    {nome: "Crítico", qtd: 1, peso: 0, img: "/img/Strike_(Dano_Crítico).gif"},
                ]
            }
        ]
    };

    function gerarLista() {
        const vocacao = document.getElementById("vocacao").value;
        const level = parseInt(document.getElementById("level").value);

        if (!vocacao || !level) {
            alert("Selecione vocação e informe o level.");
            return;
        }

        document.getElementById("pendentes").innerHTML = "";
        document.getElementById("conferidos").innerHTML = "";

        const faixas = tabelas[vocacao];

        if (!faixas) {
            alert("Vocação inválida.");
            return;
        }

        const faixa = faixas
            .sort((a, b) => b.min - a.min)
            .find(f => level >= f.min && level <= f.max);

        if (!faixa) {
            alert("Nenhuma configuração encontrada para esse level.");
            return;
        }

        faixa.itens.forEach(item => {
            adicionarLinha(item, false);
        });
    }

    function adicionarLinha(item, conferido) {
        const tr = document.createElement("tr");

        const pesoTotal = item.qtd * item.peso;

        tr.innerHTML = `
        <td>
            <div class="item-cell">
                <img src="${item.img}">
                ${item.nome}
            </div>
        </td>
        <td>${item.qtd}</td>
        <td>${item.peso} oz</td>
        <td>${pesoTotal.toLocaleString()} oz</td>
        <td>
            <label class="switch">
                <input type="checkbox" ${conferido ? "checked" : ""}>
                <span class="slider"></span>
            </label>
        </td>
    `;

        const checkbox = tr.querySelector("input");

        checkbox.addEventListener("change", function () {
            tr.remove();
            adicionarLinha(item, this.checked);
            atualizarPesoTotal();
        });

        if (conferido) {
            document.getElementById("conferidos").appendChild(tr);
        } else {
            document.getElementById("pendentes").appendChild(tr);
        }

        atualizarPesoTotal();
    }

    function atualizarPesoTotal() {

        function calcular(idTabela, idResultado) {
            const linhas = document.querySelectorAll(`#${idTabela} tr`);
            let total = 0;

            linhas.forEach(tr => {
                const colunas = tr.querySelectorAll("td");
                if (colunas.length >= 4) {
                    const texto = colunas[3].innerText.replace(" oz", "").replace(",", "");
                    total += parseFloat(texto);
                }
            });

            document.getElementById(idResultado).innerText = total.toLocaleString();
        }

        calcular("pendentes", "peso-pendentes");
        calcular("conferidos", "peso-conferidos");
    }


    function calcularCapacidade(level, vocacao) {

        const ganhoPorLevel = {
            "Mage": 10,        // Druid / Sorcerer
            "Paladin": 20,
            "Knight": 25,
            "Monk": 25,
            "Rook": 10
        };

        if (!ganhoPorLevel[vocacao]) return 0;

        return level * ganhoPorLevel[vocacao];
    }

    function atualizarCapacidade() {
        const vocacao = document.getElementById("vocacao").value;
        const level = parseInt(document.getElementById("level").value);

        if (!vocacao || !level) {
            document.getElementById("capacidade").innerText = "0";
            return;
        }

        const cap = calcularCapacidade(level, vocacao);
        document.getElementById("capacidade").innerText = cap.toLocaleString();
    }

    /* Atualiza automaticamente */
    document.getElementById("vocacao").addEventListener("change", atualizarCapacidade);
    document.getElementById("level").addEventListener("input", atualizarCapacidade);

</script>

</body>
</html>
