<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Mapa público de alertas y daños viales reportados por la comunidad en GeoVía.">
    <title>Mapa de alertas | GeoVía</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <style>
        :root{font-family:Figtree,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;color:#102a32;background:#f3f7f7;font-synthesis:none}
        *{box-sizing:border-box}body{margin:0;min-height:100vh}.topbar{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:15px clamp(16px,4vw,48px);background:#07343e;color:#fff;box-shadow:0 4px 18px #062d3520}.brand{display:flex;align-items:center;gap:12px;font-weight:800;font-size:1.25rem;letter-spacing:-.03em}.brand-mark{display:grid;place-items:center;width:42px;height:42px;border-radius:14px;background:#14b887;color:#07343e;font-size:1.35rem}.brand small{display:block;margin-top:2px;color:#b9d5d8;font-size:.72rem;font-weight:500;letter-spacing:.02em}.top-actions{display:flex;align-items:center;gap:10px}.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:0;border-radius:12px;padding:11px 15px;font:inherit;font-size:.9rem;font-weight:700;text-decoration:none;cursor:pointer;transition:transform .15s,background .15s}.btn:hover{transform:translateY(-1px)}.btn-light{background:#fff;color:#07343e}.btn-primary{background:#087f68;color:white}.page{max-width:1600px;margin:auto;padding:24px clamp(14px,3vw,36px) 36px}.intro{display:flex;align-items:flex-end;justify-content:space-between;gap:18px;margin-bottom:20px}.eyebrow{margin:0 0 7px;color:#087f68;font-size:.75rem;font-weight:800;text-transform:uppercase;letter-spacing:.13em}.intro h1{margin:0;color:#102a32;font-size:clamp(1.7rem,3vw,2.5rem);line-height:1.1;letter-spacing:-.045em}.intro p{max-width:660px;margin:10px 0 0;color:#587078;line-height:1.6}.live{display:inline-flex;align-items:center;gap:8px;padding:9px 12px;border:1px solid #d8e7e5;border-radius:999px;background:#fff;color:#36575d;font-size:.8rem;font-weight:700;white-space:nowrap}.live-dot{width:8px;height:8px;border-radius:50%;background:#12a879;box-shadow:0 0 0 4px #12a87920}.stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-bottom:16px}.stat{display:flex;align-items:center;gap:13px;padding:16px 18px;border:1px solid #e0e9e8;border-radius:16px;background:#fff;box-shadow:0 4px 16px #153c4210}.stat-icon{display:grid;place-items:center;width:42px;height:42px;flex:0 0 auto;border-radius:13px;background:#eaf7f2;color:#087f68;font-size:1.2rem}.stat strong{display:block;font-size:1.5rem;line-height:1.1;letter-spacing:-.03em}.stat span{display:block;margin-top:4px;color:#60777d;font-size:.82rem}.workspace{display:grid;grid-template-columns:minmax(0,1fr) 320px;gap:16px;align-items:stretch}.map-card,.side-card{overflow:hidden;border:1px solid #dfe9e8;border-radius:18px;background:white;box-shadow:0 8px 28px #153c420c}.map-head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:16px 18px;border-bottom:1px solid #edf2f1}.map-head h2,.side-card h2{margin:0;font-size:1rem;letter-spacing:-.02em}.map-head p{margin:4px 0 0;color:#71858a;font-size:.8rem}.map-controls{display:flex;gap:8px}.small-btn{border:1px solid #d8e4e3;border-radius:10px;padding:8px 10px;background:white;color:#315159;font:inherit;font-size:.8rem;font-weight:700;cursor:pointer}.small-btn:hover{background:#f0f8f6}.map-wrap{position:relative}#mapa{height:min(67vh,720px);min-height:470px;width:100%;background:#e8efed}.map-status{position:absolute;z-index:500;left:14px;bottom:14px;max-width:min(440px,calc(100% - 28px));padding:10px 13px;border:1px solid #dbe7e5;border-radius:12px;background:#ffffffed;color:#34545b;font-size:.8rem;box-shadow:0 4px 14px #153c421c}.map-status.error{color:#a33535}.side-card{padding:18px}.side-card+.side-card{margin-top:14px}.side-card h2{margin-bottom:12px}.legend{display:grid;gap:11px}.legend-item{display:flex;align-items:center;gap:10px;color:#48636a;font-size:.88rem}.legend-dot{width:12px;height:12px;flex:0 0 auto;border-radius:50%;box-shadow:0 0 0 3px color-mix(in srgb,var(--c) 14%,transparent)}.hint{padding:13px;border-radius:12px;background:#eff8f5;color:#41645e;font-size:.85rem;line-height:1.55}.hint strong{display:block;margin-bottom:3px;color:#087f68}.recent-list{display:grid;gap:8px;max-height:330px;overflow:auto}.recent-item{display:flex;width:100%;align-items:flex-start;gap:10px;padding:11px;border:1px solid #e5edec;border-radius:12px;background:#fff;text-align:left;cursor:pointer}.recent-item:hover{border-color:#91cbb9;background:#f7fcfa}.recent-pin{width:10px;height:10px;margin-top:5px;flex:0 0 auto;border-radius:50%;background:var(--c)}.recent-item strong{display:block;color:#18383f;font-size:.86rem}.recent-item span{display:block;margin-top:3px;color:#70858a;font-size:.77rem;line-height:1.4}.empty{padding:14px;border:1px dashed #d8e5e3;border-radius:12px;color:#70858a;font-size:.85rem;line-height:1.5}.footer{margin-top:16px;color:#71858a;font-size:.78rem;line-height:1.6}.leaflet-popup-content-wrapper{border-radius:14px}.popup-title{margin:0 0 5px;font-size:1rem;font-weight:800}.popup-state{display:inline-block;margin-bottom:7px;padding:4px 8px;border-radius:999px;background:var(--bg);color:var(--c);font-size:.72rem;font-weight:800}.popup-desc{max-width:230px;margin:0;color:#526a70;font-size:.82rem;line-height:1.5}.popup-date{margin-top:8px;color:#7b8d91;font-size:.73rem}.popup-photo{display:block;width:100%;max-height:130px;margin:8px 0;border-radius:8px;object-fit:cover}
        @media(max-width:980px){.workspace{grid-template-columns:minmax(0,1fr)}.side-column{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}.side-card+.side-card{margin-top:0}.recent-card{grid-column:1/-1}.recent-list{grid-template-columns:repeat(2,minmax(0,1fr));max-height:none}}
        @media(max-width:620px){.topbar{padding:12px 14px}.brand{font-size:1.1rem}.brand-mark{width:38px;height:38px}.top-actions .btn{padding:10px 11px;font-size:.8rem}.page{padding:20px 12px 28px}.intro{align-items:flex-start;flex-direction:column}.live{font-size:.75rem}.stats{gap:8px}.stat{align-items:flex-start;gap:8px;padding:12px 9px;border-radius:13px}.stat-icon{width:30px;height:30px;border-radius:9px;font-size:.95rem}.stat strong{font-size:1.25rem}.stat span{font-size:.7rem;line-height:1.3}.map-head{align-items:flex-start;padding:14px;flex-direction:column}.map-controls{width:100%}.small-btn{flex:1}#mapa{height:60vh;min-height:390px}.side-column{grid-template-columns:minmax(0,1fr)}.recent-list{grid-template-columns:minmax(0,1fr)}.recent-card{grid-column:auto}.map-status{font-size:.73rem}}
    </style>
</head>
<body>
<header class="topbar">
    <a class="brand" href="{{ url('/') }}" style="color:inherit;text-decoration:none">
        <span class="brand-mark" aria-hidden="true">⌖</span>
        <span>GeoVía<small>Alertas viales de la comunidad</small></span>
    </a>
    <div class="top-actions">
        @auth
            <a class="btn btn-light" href="{{ route('dashboard') }}">Mi panel</a>
        @else
            <a class="btn btn-light" href="{{ route('login') }}">Iniciar sesión</a>
        @endauth
        @auth
            <a class="btn btn-primary" href="{{ route('reportes.create') }}">＋ Reportar daño</a>
        @endauth
    </div>
</header>
<main class="page">
    <section class="intro">
        <div>
            <p class="eyebrow">Mapa comunitario</p>
            <h1>Conduce atento. La comunidad te alerta.</h1>
            <p>Explora los daños viales reportados, consulta su estado y ubica los puntos que requieren atención. Las alertas activas se actualizan automáticamente.</p>
        </div>
        <div class="live"><span class="live-dot"></span><span id="actualizado">Conectando con GeoVía…</span></div>
    </section>
    <section class="stats" aria-label="Resumen de alertas">
        <div class="stat"><div class="stat-icon" aria-hidden="true">⌖</div><div><strong id="total-activos">—</strong><span>Alertas activas</span></div></div>
        <div class="stat"><div class="stat-icon" style="background:#fff5df;color:#a66b00" aria-hidden="true">◷</div><div><strong id="total-pendientes">—</strong><span>Pendientes de revisión</span></div></div>
        <div class="stat"><div class="stat-icon" style="background:#eaf2ff;color:#2860b4" aria-hidden="true">✓</div><div><strong id="total-trabajo">—</strong><span>Validados o en reparación</span></div></div>
    </section>
    <section class="workspace">
        <div class="map-card">
            <div class="map-head">
                <div><h2>Mapa de alertas activas</h2><p>Selecciona un marcador para consultar los detalles del daño.</p></div>
                <div class="map-controls"><button type="button" class="small-btn" id="btn-ubicacion">◎ Mi ubicación</button><button type="button" class="small-btn" id="btn-recargar">↻ Actualizar</button></div>
            </div>
            <div class="map-wrap"><div id="mapa" role="application" aria-label="Mapa público de alertas viales"></div><div id="estado-mapa" class="map-status" role="status">Cargando alertas públicas…</div></div>
        </div>
        <aside class="side-column">
            <section class="side-card">
                <h2>¿Qué significa cada color?</h2>
                <div class="legend">
                    <div class="legend-item"><span class="legend-dot" style="--c:#d97706"></span>Pendiente de revisión</div>
                    <div class="legend-item"><span class="legend-dot" style="--c:#2563eb"></span>Validado por la municipalidad</div>
                    <div class="legend-item"><span class="legend-dot" style="--c:#ea580c"></span>En reparación</div>
                </div>
            </section>
            <section class="side-card">
                <div class="hint"><strong>Una comunidad más segura</strong>Si encuentras un bache o un peligro vial, reportarlo ayuda a que la municipalidad lo conozca y le dé seguimiento.</div>
                @auth
                    <a class="btn btn-primary" style="width:100%;margin-top:12px" href="{{ route('reportes.create') }}">＋ Crear reporte</a>
                @else
                    <a class="btn btn-primary" style="width:100%;margin-top:12px" href="{{ route('login') }}">Inicia sesión para reportar</a>
                @endauth
            </section>
            <section class="side-card recent-card">
                <h2>Alertas más recientes</h2>
                <div id="lista-recientes" class="recent-list"><div class="empty">Estamos cargando los reportes.</div></div>
            </section>
        </aside>
    </section>
    <p class="footer">GeoVía muestra únicamente reportes activos habilitados para consulta pública. La ubicación del visitante no se solicita automáticamente; solo se utiliza si selecciona “Mi ubicación”. Mapa © OpenStreetMap contributors.</p>
</main>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
(() => {
    'use strict';
    const apiUrl = @json($urlReportes);
    const centro = [@json($mapa['latitud']), @json($mapa['longitud'])];
    const zoomInicial = @json($mapa['zoom']);
    const colores = { PENDIENTE: '#d97706', VALIDADO: '#2563eb', 'EN REPARACION': '#ea580c' };
    const etiquetas = { PENDIENTE: 'Pendiente de revisión', VALIDADO: 'Validado', 'EN REPARACION': 'En reparación' };
    const mapa = L.map('mapa', { zoomControl: false }).setView(centro, zoomInicial);
    L.control.zoom({ position: 'bottomright' }).addTo(mapa);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a>'
    }).addTo(mapa);

    const capaMarcadores = L.layerGroup().addTo(mapa);
    const estadoMapa = document.getElementById('estado-mapa');
    const listaRecientes = document.getElementById('lista-recientes');
    let reportes = [];
    let marcadorUsuario = null;
    let circuloUsuario = null;

    function escapar(valor) {
        return String(valor ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }
    function fechaBonita(iso) {
        if (!iso) return 'Fecha no disponible';
        const p = String(iso).split('-').map(Number);
        if (p.length !== 3 || p.some(Number.isNaN)) return iso;
        return new Intl.DateTimeFormat('es-GT', {day:'numeric', month:'short', year:'numeric'}).format(new Date(p[0], p[1]-1, p[2]));
    }
    function colorEstado(estado) { return colores[estado] || '#64748b'; }
    function etiquetaEstado(estado) { return etiquetas[estado] || estado || 'Estado no disponible'; }
    function popup(reporte) {
        const c = colorEstado(reporte.estado);
        const foto = reporte.foto ? `<img class="popup-photo" src="${escapar(reporte.foto)}" alt="Fotografía del daño" loading="lazy" onerror="this.remove()">` : '';
        return `<div style="--c:${c};--bg:${c}18"><span class="popup-state">${escapar(etiquetaEstado(reporte.estado))}</span><h3 class="popup-title">${escapar(reporte.tipo)}</h3><p style="margin:0 0 6px;color:#647b80;font-size:.78rem">${escapar(reporte.via || 'Vía sin nombre')}</p>${foto}<p class="popup-desc">${escapar(reporte.descripcion || 'Sin descripción adicional.')}</p><div class="popup-date">Reportado: ${escapar(fechaBonita(reporte.fecha))} · #${escapar(reporte.id)}</div></div>`;
    }
    function pintarLista() {
        const recientes = [...reportes].sort((a,b) => String(b.fecha || '').localeCompare(String(a.fecha || '')) || b.id-a.id).slice(0, 8);
        if (!recientes.length) {
            listaRecientes.innerHTML = '<div class="empty">No hay alertas activas registradas por el momento.</div>';
            return;
        }
        listaRecientes.innerHTML = recientes.map(r => `<button type="button" class="recent-item" data-id="${Number(r.id)}" style="--c:${colorEstado(r.estado)}"><span class="recent-pin"></span><span><strong>${escapar(r.tipo)}</strong><span>${escapar(r.via || 'Vía sin nombre')}<br>${escapar(etiquetaEstado(r.estado))} · ${escapar(fechaBonita(r.fecha))}</span></span></button>`).join('');
    }
    function pintarResumen() {
        document.getElementById('total-activos').textContent = reportes.length;
        document.getElementById('total-pendientes').textContent = reportes.filter(r => r.estado === 'PENDIENTE').length;
        document.getElementById('total-trabajo').textContent = reportes.filter(r => ['VALIDADO','EN REPARACION'].includes(r.estado)).length;
    }
    function pintarMarcadores() {
        capaMarcadores.clearLayers();
        reportes.forEach(r => {
            const lat = Number(r.lat), lng = Number(r.lng);
            if (!Number.isFinite(lat) || !Number.isFinite(lng) || Math.abs(lat)>90 || Math.abs(lng)>180) return;
            const c = colorEstado(r.estado);
            const icon = L.divIcon({
                className: 'gv-marker-wrap',
                html: `<span style="display:grid;place-items:center;width:34px;height:34px;border:3px solid white;border-radius:50% 50% 50% 8px;transform:rotate(-45deg);background:${c};box-shadow:0 3px 10px #102a3250"><span style="transform:rotate(45deg);color:white;font-weight:900;font-size:16px">${r.estado === 'PENDIENTE' ? '!' : r.estado === 'VALIDADO' ? '✓' : '↻'}</span></span>`,
                iconSize: [34,34], iconAnchor: [17,31], popupAnchor: [0,-29]
            });
            L.marker([lat,lng], {icon, title: `${r.tipo} · ${etiquetaEstado(r.estado)}`, alt: `${r.tipo}, ${etiquetaEstado(r.estado)}`})
                .bindPopup(popup(r), {maxWidth:280})
                .addTo(capaMarcadores);
        });
    }
    async function cargar(mostrarError = true) {
        estadoMapa.classList.remove('error');
        estadoMapa.textContent = 'Actualizando reportes…';
        try {
            const respuesta = await fetch(apiUrl, {headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}, cache:'no-store'});
            if (!respuesta.ok) throw new Error(`HTTP ${respuesta.status}`);
            const json = await respuesta.json();
            if (!json || !Array.isArray(json.data)) throw new Error('Formato de respuesta no válido');
            reportes = json.data;
            pintarMarcadores(); pintarLista(); pintarResumen();
            const momento = new Intl.DateTimeFormat('es-GT',{hour:'2-digit',minute:'2-digit'}).format(new Date());
            document.getElementById('actualizado').textContent = `Actualizado a las ${momento}`;
            estadoMapa.textContent = reportes.length ? `${reportes.length} alerta(s) activa(s) en el mapa.` : 'No hay alertas activas para mostrar. Si detectas un daño, repórtalo a GeoVía.';
            if (!reportes.length) estadoMapa.style.maxWidth = 'min(520px, calc(100% - 28px))';
        } catch (error) {
            console.error('No fue posible cargar las alertas públicas:', error);
            if (mostrarError) {
                estadoMapa.classList.add('error');
                estadoMapa.textContent = 'No se pudieron cargar los reportes. Verifica tu conexión e inténtalo de nuevo.';
            }
        }
    }
    document.getElementById('btn-recargar').addEventListener('click', () => cargar(true));
    document.getElementById('btn-ubicacion').addEventListener('click', () => {
        if (!('geolocation' in navigator)) { estadoMapa.textContent = 'Este navegador no permite obtener tu ubicación.'; return; }
        estadoMapa.textContent = 'Solicitando permiso de ubicación…';
        navigator.geolocation.getCurrentPosition(pos => {
            const ll = [pos.coords.latitude, pos.coords.longitude];
            if (marcadorUsuario) mapa.removeLayer(marcadorUsuario);
            if (circuloUsuario) mapa.removeLayer(circuloUsuario);
            marcadorUsuario = L.circleMarker(ll,{radius:7,color:'#fff',weight:3,fillColor:'#0f766e',fillOpacity:1}).addTo(mapa).bindPopup('Tu ubicación actual');
            circuloUsuario = L.circle(ll,{radius:pos.coords.accuracy,color:'#0f766e',weight:1,fillOpacity:.08,interactive:false}).addTo(mapa);
            mapa.flyTo(ll,16); marcadorUsuario.openPopup();
            estadoMapa.textContent = 'Mostrando tu ubicación. Solo se usa en este navegador.';
        }, err => {
            estadoMapa.textContent = err.code === 1 ? 'Permiso denegado. Puedes seguir explorando el mapa sin compartir tu ubicación.' : 'No pudimos obtener tu ubicación. Inténtalo de nuevo o mueve el mapa.';
        }, {enableHighAccuracy:true, timeout:12000, maximumAge:30000});
    });
    listaRecientes.addEventListener('click', e => {
        const button = e.target.closest('[data-id]');
        if (!button) return;
        const r = reportes.find(x => Number(x.id) === Number(button.dataset.id));
        if (!r) return;
        mapa.flyTo([Number(r.lat),Number(r.lng)],17);
        capaMarcadores.eachLayer(marker => {
            const ll = marker.getLatLng();
            if (Math.abs(ll.lat-Number(r.lat)) < 1e-8 && Math.abs(ll.lng-Number(r.lng)) < 1e-8) marker.openPopup();
        });
    });
    cargar();
    window.setInterval(() => { if (!document.hidden) cargar(false); }, 60000);
    document.addEventListener('visibilitychange', () => { if (!document.hidden) cargar(false); });
})();
</script>
</body>
</html>
