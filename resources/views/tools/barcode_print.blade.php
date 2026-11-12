<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>طباعة الباركود | NextStore</title>
    <link href="/vendor/fonts/fonts.css" rel="stylesheet">
    <link rel="stylesheet" href="/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="/css/nextstore-erp.css">
    <link rel="stylesheet" href="/css/nextstore-reports.css">
    <link rel="stylesheet" href="/css/nextstore-labels.css">
</head>
<body class="ns-erp rp-page">
    <header class="ns-header">
        <h1><i class="fa-solid fa-barcode" style="color:#119744"></i> طباعة الباركود</h1>
        <a href="/tools/scale-barcode-print" class="btn-top-link"><i class="fa-solid fa-weight-scale"></i> باركود الموازين</a>
        <button type="button" class="btn-exit" onclick="NextStoreNav.requestClose()"><i class="fa-solid fa-xmark"></i> إغلاق (Esc)</button>
    </header>

    <div class="lb-grid">
        {{-- 1) اختيار الأصناف أو الموظفين --}}
        <section class="rp-card">
            <nav class="rp-tabs" id="srcTabs">
                <button type="button" data-src="items" class="active">الأصناف</button>
                <button type="button" data-src="employees">الموظفين</button>
            </nav>
            <div class="lb-pad"><input type="text" id="search" class="lb-input" placeholder="ابحث بالاسم أو اضرب الباركود (F3)" autocomplete="off"></div>
            <div class="rp-body" id="results"></div>
            <footer class="rp-foot"><span class="rp-note">Enter يضيف أول نتيجة · دبل كليك يضيف</span></footer>
        </section>

        {{-- 2) قائمة الطباعة --}}
        <section class="rp-card">
            <div class="rp-title">قائمة الطباعة <small id="countText"></small></div>
            <div class="rp-body">
                <table class="rp-table">
                    <thead><tr><th>البيان</th><th>الباركود</th><th>السعر</th><th style="width:110px">عدد النسخ</th><th style="width:40px"></th></tr></thead>
                    <tbody id="queue"></tbody>
                </table>
            </div>
            <footer class="rp-foot">
                <button type="button" class="btn-top-link" id="btnStock" title="عدد النسخ = الرصيد الحالي لكل صنف"><i class="fa-solid fa-boxes-stacked"></i> النسخ = الرصيد</button>
                <button type="button" class="btn-top-link" id="btnClear"><i class="fa-solid fa-trash"></i> مسح القائمة</button>
            </footer>
        </section>

        {{-- 3) إعدادات الاستيكر والمعاينة --}}
        <section class="rp-card">
            <div class="rp-title">الاستيكر</div>
            <div class="rp-body lb-pad lb-settings">
                <label>المقاس <select id="preset" class="lb-input"></select></label>
                <label id="skipWrap">ابدأ بعد (خانات مستعملة في الشيت) <input type="number" id="skip" class="lb-input en-digit" min="0" value="0"></label>
                <label>الطابعة <select id="printer" class="lb-input"><option value="">الطابعة الافتراضية</option></select></label>
                <div class="lb-checks">
                    <label><input type="checkbox" id="showShop" checked> اسم المحل</label>
                    <label><input type="checkbox" id="showName" checked> الاسم</label>
                    <label><input type="checkbox" id="showPrice" checked> السعر</label>
                    <label><input type="checkbox" id="showText" checked> رقم الباركود</label>
                </div>
                <label>حجم الخط <input type="number" id="fontPx" class="lb-input en-digit" min="7" max="16" placeholder="تلقائي"></label>
                <div class="lb-preview-wrap"><div class="lb-preview-title">معاينة</div><div id="preview" class="lb-preview"></div></div>
            </div>
            <footer class="rp-foot">
                <button type="button" class="lb-print" id="btnPrint"><i class="fa-solid fa-print"></i> طباعة <span id="printCount"></span> (F10)</button>
            </footer>
        </section>
    </div>

    <script src="/vendor/jsbarcode/JsBarcode.all.min.js"></script>
    <script src="/vendor/sweetalert2/sweetalert2.all.min.js"></script>
    <script src="/js/nextstore-printer.js"></script>
    <script src="/js/nextstore-labels.js"></script>
    <script src="/js/nextstore-search.js"></script>
    <script>
    (() => {
        const $ = id => document.getElementById(id), { esc, money } = NSLabels;
        const SHOP = @json($shop);
        // كل صنف وعبواته (علبة/كرتونة) سطور منفصلة بباركودها وسعرها
        const items = @json($items).flatMap(i => [
            { key: 'i' + i.id, kind: 'item', name: i.name, code: i.barcode, price: i.price, stock: i.qty, sub: '' },
            ...i.packs.map(u => ({ key: 'p' + i.id + u.barcode, kind: 'item', name: i.name, code: u.barcode, price: Number(u.price) || i.price * u.factor, stock: Math.floor(i.qty / (u.factor || 1)), sub: Number(u.factor) > 1 ? `${u.name} ×${u.factor}` : '' })),
        ]).filter(x => x.code);
        const employees = @json($employees).map(e => ({ key: 'e' + e.id, kind: 'employee', name: e.name, code: e.code, price: null, sub: e.job || '', phone: e.phone }));
        const STORE = 'ns.labels';
        let src = 'items', queue = [], hits = [];
        let cfg = { preset: '38x25', showShop: true, showName: true, showPrice: true, showText: true };
        try { cfg = { ...cfg, ...JSON.parse(localStorage.getItem(STORE) || '{}') }; } catch (e) {}

        $('preset').innerHTML = NSLabels.PRESETS.map(p => `<option value="${p.id}">${p.name}</option>`).join('');
        const opts = () => ({ showShop: $('showShop').checked, showName: $('showName').checked, showPrice: $('showPrice').checked, showText: $('showText').checked, fontPx: $('fontPx').value, skip: $('skip').value, printer: $('printer').value });
        const data = x => x.kind === 'employee'
            ? { top: SHOP, title: x.name, sub: [x.sub, x.code].filter(Boolean).join(' · '), code: x.code, footer: x.phone || '' }
            : { top: SHOP, title: x.name, sub: x.sub, price: x.price, code: x.code };

        function saveCfg() {
            cfg = { preset: $('preset').value, ...opts() };
            delete cfg.skip;
            try { localStorage.setItem(STORE, JSON.stringify(cfg)); } catch (e) {}
        }
        function loadCfg() {
            $('preset').value = cfg.preset;
            ['showShop', 'showName', 'showPrice', 'showText'].forEach(k => $(k).checked = cfg[k] !== false);
            $('fontPx').value = cfg.fontPx || '';
        }

        function renderResults() {
            const q = $('search').value.trim(), list = src === 'items' ? items : employees;
            hits = NSSearch.sort(list.filter(x => !q || NSSearch.norm(`${x.name} ${x.code} ${x.sub}`).includes(NSSearch.norm(q))), q, x => ({ code: x.code, name: x.name })).slice(0, 150);
            $('results').innerHTML = hits.map((x, i) => `<div class="lb-hit" data-i="${i}"><div><b>${esc(x.name)}</b>${x.sub ? ` <small>${esc(x.sub)}</small>` : ''}</div>
                <div class="lb-hit-meta"><span class="en-digit">${esc(x.code)}</span>${x.price != null ? `<span class="en-digit">${money(x.price)}</span>` : ''}${x.stock != null ? `<span>رصيد <b class="en-digit">${money(x.stock)}</b></span>` : ''}</div></div>`).join('')
                || `<div class="rp-empty">${list.length ? 'مفيش نتيجة' : (src === 'items' ? 'مفيش أصناف ليها باركود' : 'مفيش موظفين على رأس العمل')}</div>`;
        }

        function add(x, copies = 1) {
            if (!x) return;
            const row = queue.find(r => r.key === x.key);
            row ? row.copies += copies : queue.push({ ...x, copies });
            renderQueue();
        }

        function renderQueue() {
            $('queue').innerHTML = queue.map((r, i) => `<tr><td style="text-align:right"><b>${esc(r.name)}</b>${r.sub ? `<div class="rp-note" style="margin:0">${esc(r.sub)}</div>` : ''}</td>
                <td class="en-digit">${esc(r.code)}</td><td class="en-digit">${r.price != null ? money(r.price) : '—'}</td>
                <td><input type="number" min="0" max="500" class="lb-input en-digit lb-copies" data-i="${i}" value="${r.copies}"></td>
                <td><button type="button" class="lb-del" data-del="${i}"><i class="fa-solid fa-xmark"></i></button></td></tr>`).join('')
                || '<tr><td colspan="5" class="rp-empty">اختار من القائمة اللي على اليمين</td></tr>';
            const n = queue.reduce((s, r) => s + (Number(r.copies) || 0), 0);
            $('countText').textContent = queue.length ? `(${queue.length} صنف · ${n} استيكر)` : '';
            $('printCount').textContent = n ? `${n} استيكر` : '';
            preview();
        }

        function preview() {
            const p = NSLabels.preset($('preset').value), first = queue[0] || (src === 'items' ? items[0] : employees[0]);
            $('skipWrap').style.display = p.sheet ? '' : 'none';
            if (!first) { $('preview').innerHTML = ''; return; }
            const b = NSLabels.build([{ data: data(first), copies: 1 }], p.sheet ? '38x25' : p.id, opts());
            const scale = Math.min(1, 250 / (p.w * 3.78));
            $('preview').innerHTML = `<div style="transform:scale(${scale});transform-origin:top center;background:#fff;border:1px dashed #94a3b8">${p.sheet ? NSLabels.build([{ data: data(first), copies: 1 }], p.id, { ...opts(), skip: 0 }).html.replace(/height:296mm/, `height:${p.h + 2}mm`).replace(/grid-template-columns:repeat\(\d+,/, 'grid-template-columns:repeat(1,') : b.html}</div>`;
        }

        async function print() {
            const list = queue.filter(r => Number(r.copies) > 0);
            if (!list.length) return Swal.fire({ icon: 'info', title: 'القائمة فاضية', text: 'ضيف أصناف وحدد عدد النسخ.', confirmButtonColor: '#119744' });
            const total = list.reduce((s, r) => s + Number(r.copies), 0);
            if (total > 300) {
                const ok = await Swal.fire({ icon: 'question', title: `هتطبع ${total} استيكر`, showCancelButton: true, confirmButtonText: 'طباعة', cancelButtonText: 'رجوع', confirmButtonColor: '#119744' });
                if (!ok.isConfirmed) return;
            }
            $('btnPrint').disabled = true;
            try {
                const n = await NSLabels.print(list.map(r => ({ data: data(r), copies: r.copies })), $('preset').value, opts());
                Swal.fire({ toast: true, position: 'bottom-start', icon: 'success', title: `اتبعت ${n} استيكر للطباعة`, showConfirmButton: false, timer: 2200 });
            } catch (e) {
                Swal.fire('تعذرت الطباعة', e.message || 'راجع الطابعة', 'error');
            } finally { $('btnPrint').disabled = false; }
        }

        // ---- الأحداث ----
        $('srcTabs').onclick = e => { const b = e.target.closest('[data-src]'); if (!b) return; src = b.dataset.src; document.querySelectorAll('#srcTabs button').forEach(x => x.classList.toggle('active', x === b));
            if (src === 'employees' && $('preset').value !== 'card' && !queue.length) $('preset').value = 'card';
            renderResults(); preview(); $('search').focus(); };
        $('search').oninput = renderResults;
        $('search').onkeydown = e => { if (e.key !== 'Enter') return; e.preventDefault();
            const code = e.target.value.trim(), exact = (src === 'items' ? items : employees).find(x => x.code === code);
            add(exact || hits[0]); e.target.value = ''; renderResults(); };
        $('results').ondblclick = e => { const h = e.target.closest('[data-i]'); if (h) add(hits[h.dataset.i]); };
        $('results').onclick = e => { const h = e.target.closest('[data-i]'); if (h) { document.querySelectorAll('.lb-hit.on').forEach(x => x.classList.remove('on')); h.classList.add('on'); } };
        $('queue').oninput = e => { const i = e.target.dataset.i; if (i != null) { queue[i].copies = Math.max(0, Math.min(500, parseInt(e.target.value) || 0)); const n = queue.reduce((s, r) => s + r.copies, 0); $('printCount').textContent = n ? `${n} استيكر` : ''; } };
        $('queue').onclick = e => { const d = e.target.closest('[data-del]'); if (d) { queue.splice(d.dataset.del, 1); renderQueue(); } };
        $('btnStock').onclick = () => { queue.forEach(r => { if (r.stock != null) r.copies = Math.max(0, Math.min(500, Math.ceil(r.stock))); }); renderQueue(); };
        $('btnClear').onclick = () => { queue = []; renderQueue(); };
        ['preset', 'showShop', 'showName', 'showPrice', 'showText', 'fontPx', 'printer'].forEach(id => $(id).addEventListener('change', () => { saveCfg(); preview(); }));
        $('skip').oninput = preview;
        $('btnPrint').onclick = print;
        document.addEventListener('keydown', e => {
            if (e.key === 'F10') { e.preventDefault(); print(); }
            if (e.key === 'F3') { e.preventDefault(); $('search').focus(); $('search').select(); }
        });

        window.NextStorePrinter.getPrinters().then(list => {
            $('printer').innerHTML = '<option value="">الطابعة الافتراضية</option>' + list.map(n => `<option>${esc(n)}</option>`).join('');
            if (cfg.printer && list.includes(cfg.printer)) $('printer').value = cfg.printer;
        }).catch(() => {});
        window.nsDirty = () => queue.length > 0;
        loadCfg(); renderResults(); renderQueue(); $('search').focus();
    })();
    </script>
    <script src="/js/nextstore-nav.js"></script>
    <script src="/js/nextstore-alerts.js"></script>
</body>
</html>
