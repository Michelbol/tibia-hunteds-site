<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8"/>
    <title>Timeline 24h - Players Online</title>
    <style>
        body { background:#111; color:#eee; font-family: Arial, sans-serif; padding:16px; }
        .container { max-width:1100px; margin:0 auto; }
        svg { width:100%; background:#0f0f0f; border-radius:6px; }
        .row-label { fill:#ddd; font-size:12px; }
        .hour-label { fill:#888; font-size:11px; }
        .block { rx:3; ry:3; opacity:0.95; }
        .tooltip {
            position: absolute; pointer-events:none;
            background:#222;color:#fff;padding:6px;border-radius:4px;border:1px solid #444;
            font-size:12px; display:none;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Timeline 24h — Períodos online - {{ $day }}</h2>
    <div id="chartWrap" style="position:relative;">
        <svg id="timeline" height="360" viewBox="0 0 1000 360" preserveAspectRatio="xMinYMin meet"></svg>
        <div id="tooltip" class="tooltip"></div>
    </div>
    <p style="color:#aaa;font-size:13px">Eixo horizontal = 24 horas (00:00 — 23:59). Clique em um bloco para copiar comando.</p>
</div>

<script>
    const sampleData = {!! $info !!};

    // --- Config ---
    const svg = document.getElementById('timeline');
    const tooltip = document.getElementById('tooltip');
    const width = 1000;
    const leftMargin = 140;
    const rightMargin = 20;
    const timelineWidth = width - leftMargin - rightMargin;
    const rowHeight = 36;
    const paddingTop = 30;
    const hours = 24;

    // converter hora (0..24) para X
    function hourToX(h) { return leftMargin + (h/24)*timelineWidth; }
    function isoToHourFraction(iso){
        const d = new Date(iso);
        // hora decimal no dia UTC (0..24)
        return d.getUTCHours() + d.getUTCMinutes()/60 + d.getUTCSeconds()/3600;
    }
    // corta sessões para o período do dia [00:00,24:00) da data base
    function clampSessionToDay(startIso, endIso, dayIsoBase) {
        // dayIsoBase = '2025-08-09' (YYYY-MM-DD)
        const dayStart = new Date(dayIsoBase + "T00:00:00Z");
        const dayEnd = new Date(dayIsoBase + "T23:59:59Z");
        const s = new Date(startIso), e = new Date(endIso || startIso);
        const start = s < dayStart ? dayStart : s;
        const end = e > dayEnd ? dayEnd : e;
        if (end <= start) return null;
        return { start: start.toISOString(), end: end.toISOString() };
    }

    function render(data, dayBase) {
        let size = data.length;
        svg.setAttribute('height', size * 41.25);
        svg
        .innerHTML = '';
        // background
        const defs = `<defs>
    <linearGradient id="g1" x1="0" x2="1"><stop offset="0" stop-color="#233"/><stop offset="1" stop-color="#1a1a2b"/></linearGradient>
  </defs>`;
        svg.insertAdjacentHTML('beforeend', defs);
        // hour lines & labels
        for (let h=0; h<=24; h+=2) {
            const x = hourToX(h);
            svg.insertAdjacentHTML('beforeend', `<line x1="${x}" y1="${paddingTop}" x2="${x}" y2="${paddingTop + data.length*rowHeight}" stroke="#2a2a2a" stroke-width="1"/>`);
            svg.insertAdjacentHTML('beforeend', `<text x="${x}" y="${paddingTop-8}" class="hour-label" text-anchor="middle">${String(h).padStart(2,'0')}:00</text>`);
        }

        // rows
        data.forEach((p, idx) => {
            const y = paddingTop + idx*rowHeight;
            // label
            svg.insertAdjacentHTML('beforeend', `<text x="8" y="${y+rowHeight/2+5}" class="row-label">${p.name}</text>`);
            // background stripe
            svg.insertAdjacentHTML('beforeend', `<rect x="${leftMargin}" y="${y+4}" width="${timelineWidth}" height="${rowHeight-8}" fill="#111" rx="4"></rect>`);

            // render sessions (clamp to day)
            p.sessions.forEach(s => {
                const cs = clampSessionToDay(s.start, s.end, dayBase);
                if (!cs) return;
                const startH = isoToHourFraction(cs.start);
                const endH = isoToHourFraction(cs.end);
                const x = hourToX(startH);
                const w = hourToX(endH) - x;
                const color = p.color || '#4caf50';
                const rectId = `block-${idx}-${Math.random().toString(36).slice(2,8)}`;
                svg.insertAdjacentHTML('beforeend',
                    `<rect id="${rectId}" class="block" x="${x}" y="${y+8}" width="${Math.max(2,w)}" height="${rowHeight-16}" fill="${color}"></rect>`);
                // attach event listeners (delegation alternative not available for inline)
                setTimeout(()=> {
                    const rect = document.getElementById(rectId);
                    rect.addEventListener('mousemove', (ev)=>{
                        tooltip.style.display='block';
                        tooltip.style.left = (ev.pageX + 12) + 'px';
                        tooltip.style.top = (ev.pageY + 12) + 'px';
                        tooltip.innerHTML = `<strong>${p.name}</strong><br> ${new Date(cs.start).toUTCString()} → ${new Date(cs.end).toUTCString()}`;
                    });
                    rect.addEventListener('mouseleave', ()=> tooltip.style.display='none');
                    rect.addEventListener('click', ()=> {
                        navigator.clipboard.writeText(`exiva "${p.name}"`);
                        alert(`Copiado: exiva "${p.name}"`);
                    });
                },10);
            });
        });

        // border frame
        svg.insertAdjacentHTML('beforeend', `<rect x="${leftMargin}" y="${paddingTop}" width="${timelineWidth}" height="${data.length*rowHeight}" fill="none" stroke="#222" rx="6"/>`);
    }

    // set colors for clarity
    sampleData[0].color = '#ff6b6b'; sampleData[1].color = '#f1c40f'; sampleData[2].color = '#4caf50';

    render(sampleData, '{{$day}}');

</script>
</body>
</html>
