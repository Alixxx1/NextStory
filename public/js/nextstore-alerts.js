/* تنبيهات أمنية: NSSecurity.report() بيسجل حدث مشبوه، ولو المستخدم مدير بيظهر له تنبيه مرة واحدة على أي شاشة */
(() => {
    const H = () => ({ 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' });
    window.NSSecurity = {
        report(type, details = {}, amount = null, ref = null) {
            try { fetch('/api/security/event', { method: 'POST', keepalive: true, headers: H(), body: JSON.stringify({ type, details, amount, ref }) }); } catch (e) {}
        },
    };

    const esc = v => String(v ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const shown = new Set();
    let box = null, timer = null;

    // 3 نغمات واضحة + وميض البرنامج في شريط المهام (Electron) عشان المدير يلاحظ حتى لو البرنامج متصغر
    function beep() {
        try {
            const c = new (window.AudioContext || window.webkitAudioContext)(), g = c.createGain();
            g.gain.value = 0.35; g.connect(c.destination);
            [0, 0.3, 0.6].forEach((t, i) => { const o = c.createOscillator(); o.type = 'square'; o.frequency.value = i === 2 ? 1175 : 880; o.connect(g); o.start(c.currentTime + t); o.stop(c.currentTime + t + 0.2); });
            setTimeout(() => c.close(), 1200);
        } catch (e) {}
        try { window.electronAPI?.attention?.(); } catch (e) {}
    }

    function render(list) {
        if (!box) {
            box = document.createElement('div');
            box.id = 'nsSecModal';
            box.style.cssText = 'position:fixed;top:12px;left:50%;transform:translateX(-50%);z-index:2147482000;width:min(520px,94vw);max-height:80vh;overflow:auto;background:#fff;border:2px solid #dc2626;border-radius:10px;font-family:Cairo,Tahoma,sans-serif;direction:rtl;box-shadow:0 8px 30px rgba(15,23,42,.3)';
            document.body.appendChild(box);
        }
        box.innerHTML = `<div style="background:#dc2626;color:#fff;padding:9px 14px;font-weight:800;display:flex;align-items:center;gap:8px"><i class="fas fa-shield-halved"></i> تنبيه أمني (${list.length})</div>
            <div style="padding:6px 14px">${list.map(a => `<div style="padding:9px 0;border-bottom:1px solid #e2e8f0">
                <div style="font-weight:800;color:#0f172a">${esc(a.title)}</div>
                <div style="font-size:13px;color:#475569;font-weight:600;margin-top:3px"><b>${esc(a.user || '—')}</b> · <span style="font-family:Arial;direction:ltr;unicode-bidi:isolate">${esc(String(a.at).slice(0, 16))}</span>${a.amount ? ` · القيمة <b style="font-family:Arial">${a.amount}</b> ج.م` : ''}${a.ref ? ` · رقم <b style="font-family:Arial">${esc(a.ref)}</b>` : ''}</div>
                ${Object.entries(a.details || {}).map(([k, v]) => `<div style="font-size:12.5px;color:#334155;margin-top:2px">${esc(isNaN(k) ? k + ': ' : '• ')}${esc(typeof v === 'object' ? JSON.stringify(v) : v)}</div>`).join('')}</div>`).join('')}</div>
            <div style="display:flex;gap:8px;padding:8px 14px 12px"><button id="nsSecClose" style="height:36px;padding:0 16px;border-radius:7px;border:0;background:#119744;color:#fff;font-weight:800;cursor:pointer">تم الاطلاع</button>
            <a href="/admin/security-alerts" style="height:36px;padding:0 16px;border-radius:7px;border:1px solid #cbd5e1;color:#1e293b;font-weight:800;text-decoration:none;display:inline-flex;align-items:center">سجل التنبيهات</a></div>`;
        box.querySelector('#nsSecClose').onclick = () => {
            const ids = list.map(a => a.id);
            fetch('/api/security/ack', { method: 'POST', headers: H(), body: JSON.stringify({ ids }) }).catch(() => {});
            box.remove(); box = null;
        };
    }

    let busy = false;
    async function poll() {
        if (busy) return;
        busy = true;
        try {
            const r = await fetch('/api/security/pending', { headers: { Accept: 'application/json' } });
            if (!r.ok) return;
            const j = await r.json();
            if (!j.manager) return clearInterval(timer);
            const fresh = j.alerts.filter(a => !shown.has(a.id));
            if (!fresh.length) return;
            fresh.forEach(a => shown.add(a.id));
            render(j.alerts); beep();
        } catch (e) {} finally { busy = false; }
    }

    // كل ثانيتين (حتى والبرنامج متصغر): أي حذف/تعديل من الكاشير يوصل للمدير في نفس اللحظة تقريباً
    if (!location.pathname.startsWith('/login')) { poll(); timer = setInterval(poll, 2000); }
})();
