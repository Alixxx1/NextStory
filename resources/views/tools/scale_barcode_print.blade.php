<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>باركود الموازين | NextStore</title>
    <link href="/vendor/fonts/fonts.css" rel="stylesheet">
    <link rel="stylesheet" href="/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="/css/nextstore-erp.css">
    <link rel="stylesheet" href="/css/nextstore-reports.css">
    <link rel="stylesheet" href="/css/nextstore-labels.css">
</head>
<body class="ns-erp rp-page">
    <header class="ns-header">
        <h1><i class="fa-solid fa-weight-scale" style="color:#119744"></i> باركود الموازين</h1>
        <a href="/tools/barcode-print" class="btn-top-link"><i class="fa-solid fa-barcode"></i> باركود الأصناف</a>
        <button type="button" class="btn-exit" onclick="NextStoreNav.requestClose()"><i class="fa-solid fa-xmark"></i> إغلاق (Esc)</button>
    </header>

    <div class="lb-grid">
        {{-- 1) الصنف والوزن --}}
        <section class="rp-card">
            <div class="rp-title">الصنف الموزون <small>(بالكيلو/اللتر)</small></div>
            <div class="lb-pad"><input type="text" id="search" class="lb-input" placeholder="ابحث باسم الصنف أو الباركود (F3)" autocomplete="off"></div>
            <div class="rp-body" id="results"></div>
            <div class="lb-pad lb-settings" style="border-top:1px solid #e2e8f0">
                <div id="picked" class="rp-note" style="margin:0">اختار صنف من القائمة</div>
                <label>الوزن (كيلو) <input type="text" inputmode="decimal" id="weight" class="lb-input en-digit" placeholder="مثلاً 0.250"></label>
                <label>أو المبلغ (ج.م) <input type="text" inputmode="decimal" id="amount" class="lb-input en-digit" placeholder="مثلاً 50"></label>
                <label>عدد النسخ <input type="number" id="copies" class="lb-input en-digit" min="1" max="500" value="1"></label>
                <button type="button" class="lb-print" id="btnAdd" style="height:38px"><i class="fa-solid fa-plus"></i> إضافة للقائمة (Enter)</button>
            </div>
        </section>

        {{-- 2) قائمة الطباعة --}}
        <section class="rp-card">
            <div class="rp-title">قائمة الطباعة <small id="countText"></small></div>
            <div class="rp-body">
                <table class="rp-table">
                    <thead><tr><th>الصنف</th><th>الوزن</th><th>سعر الكيلو</th><th>القيمة</th><th>الباركود</th><th style="width:100px">النسخ</th><th style="width:40px"></th></tr></thead>
                    <tbody id="queue"></tbody>
                </table>
            </div>
            <footer class="rp-foot"><span class="rp-note">الباركود بيتقري في شاشة البيع على طول بالصنف ووزنه (2 + كود الصنف 6 أرقام + الوزن بالجرام).</span>
                <button type="button" class="btn-top-link" id="btnClear"><i class="fa-solid fa-trash"></i> مسح القائمة</button></footer>
        </section>

        {{-- 3) الاستيكر --}}
        <section class="rp-card">
            <div class="rp-title">الاستيكر</div>
            <div class="rp-body lb-pad lb-settings">
                <label>المقاس <select id="preset" class="lb-input"></select></label>
                <label id="skipWrap">ابدأ بعد (خانات مستعملة في الشيت) <input type="number" id="skip" class="lb-input en-digit" min="0" value="0"></label>
                <label>الطابعة <select id="printer" class="lb-input"><option value="">الطابعة الافتراضية</option></select></label>
                <div class="lb-checks">
                    <label><input type="checkbox" id="showShop" checked> اسم المحل</label>
                    <label><input type="checkbox" id="showDate" checked> تاريخ التعبئة</label>
                </div>
                <div class="lb-preview-wrap"><div class="lb-preview-title">معاينة</div><div id="preview" class="lb-preview"></div></div>
            </div>
            <footer class="rp-foot"><button type="button" class="lb-print" id="btnPrint"><i class="fa-solid fa-print"></i> طباعة <span id="printCount"></span> (F10)</button></footer>
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
        const SHOP = @json($shop), items = @json($items), STORE = 'ns.scaleLabels';
        const num = v => parseFloat(String(v || '').replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d)).replace(/[^\d.]/g, '')) || 0;
        // كود الميزان = الباركود الرقمي للصنف (6 أرقام بالكتير)، زي ما شاشة البيع بتقراه
        const code6 = i => (/^\d{1,12}$/.test(i.barcode) && String(parseInt(i.barcode, 10)).length <= 6) ? String(parseInt(i.barcode, 10)).padStart(6, '0') : null;
        const ean = (i, kg) => { const d12 = '2' + code6(i) + String(Math.round(kg * 1000)).padStart(5, '0'); return d12 + NSLabels.ean13Check(d12); };
        let hits = [], picked = null, queue = [], cfg = { preset: '40x30', showShop: true, showDate: true };
        try { cfg = { ...cfg, ...JSON.parse(localStorage.getItem(STORE) || '{}') }; } catch (e) {}

        $('preset').innerHTML = NSLabels.PRESETS.filter(p => p.id !== 'card').map(p => `<option value="${p.id}">${p.name}</option>`).join('');
        $('preset').value = cfg.preset; $('showShop').checked = cfg.showShop !== false; $('showDate').checked = cfg.showDate !== false;
        const today = new Date().toLocaleDateString('en-GB');
        const data = r => ({ top: SHOP, title: r.name, sub: `${r.kg.toFixed(3)} كجم × ${money(r.price)}`, price: r.total, code: r.code, footer: $('showDate').checked ? `تعبئة ${today}` : '' });
        const opts = () => ({ showShop: $('showShop').checked, showName: true, showPrice: true, showText: true, skip: $('skip').value, printer: $('printer').value });

        function renderResults() {
            const q = $('search').value.trim();
            hits = NSSearch.sort(items.filter(i => !q || NSSearch.norm(`${i.name} ${i.barcode}`).includes(NSSearch.norm(q))), q, i => ({ code: i.barcode, name: i.name })).slice(0, 150);
            $('results').innerHTML = hits.map((i, n) => `<div class="lb-hit ${picked && picked.id === i.id ? 'on' : ''}" data-i="${n}"><div><b>${esc(i.name)}</b></div>
                <div class="lb-hit-meta"><span class="en-digit">${esc(i.barcode || '—')}</span><span class="en-digit">${money(i.price)} / ${esc(i.unit)}</span>${code6(i) ? '' : '<span class="lb-warn">الباركود لازم رقم 6 أرقام بالكتير</span>'}</div></div>`).join('')
                || `<div class="rp-empty">${items.length ? 'مفيش نتيجة' : 'مفيش أصناف بالكيلو أو اللتر'}</div>`;
        }

        function pick(i) {
            if (!i) return;
            if (!code6(i)) return Swal.fire({ icon: 'warning', title: 'باركود الصنف مش مناسب للميزان', text: 'باركود الميزان بياخد كود الصنف 6 أرقام بالكتير. غيّر باركود الصنف من المخزن لرقم قصير (مثلاً 158) وجرب تاني.', confirmButtonColor: '#119744' });
            picked = i; renderResults();
            $('picked').innerHTML = `<b>${esc(i.name)}</b> — كود الميزان <b class="en-digit">${code6(i)}</b> — <b class="en-digit">${money(i.price)}</b> ج.م/${esc(i.unit)}`;
            $('weight').focus(); $('weight').select();
        }

        function addRow() {
            if (!picked) return Swal.fire({ icon: 'info', title: 'اختار الصنف الأول', confirmButtonColor: '#119744' });
            let kg = num($('weight').value);
            const amount = num($('amount').value);
            if (!kg && amount && picked.price) kg = Math.round(amount / picked.price * 1000) / 1000; // المبلغ ← الوزن لأقرب جرام
            if (kg <= 0 || kg >= 100) return Swal.fire({ icon: 'warning', title: 'اكتب وزن صحيح', text: 'من 0.001 لحد 99.999 كيلو.', confirmButtonColor: '#119744' });
            queue.push({ name: picked.name, kg, price: picked.price, total: Math.round(kg * picked.price * 100) / 100, code: ean(picked, kg), copies: Math.max(1, Math.min(500, parseInt($('copies').value) || 1)) });
            $('weight').value = ''; $('amount').value = ''; $('copies').value = 1;
            renderQueue(); $('weight').focus();
        }

        function renderQueue() {
            $('queue').innerHTML = queue.map((r, i) => `<tr><td style="text-align:right"><b>${esc(r.name)}</b></td><td class="en-digit">${r.kg.toFixed(3)}</td><td class="en-digit">${money(r.price)}</td>
                <td class="en-digit"><b>${money(r.total)}</b></td><td class="en-digit">${r.code}</td>
                <td><input type="number" min="0" max="500" class="lb-input en-digit lb-copies" data-i="${i}" value="${r.copies}"></td>
                <td><button type="button" class="lb-del" data-del="${i}"><i class="fa-solid fa-xmark"></i></button></td></tr>`).join('')
                || '<tr><td colspan="7" class="rp-empty">اختار الصنف واكتب الوزن أو المبلغ</td></tr>';
            const n = queue.reduce((s, r) => s + r.copies, 0);
            $('countText').textContent = queue.length ? `(${n} استيكر)` : '';
            $('printCount').textContent = n ? `${n} استيكر` : '';
            preview();
        }

        function preview() {
            const p = NSLabels.preset($('preset').value);
            $('skipWrap').style.display = p.sheet ? '' : 'none';
            const r = queue[0] || (picked && { name: picked.name, kg: 0.25, price: picked.price, total: picked.price / 4, code: ean(picked, 0.25) });
            if (!r) { $('preview').innerHTML = ''; return; }
            const b = NSLabels.build([{ data: data(r), copies: 1 }], p.sheet ? '40x30' : p.id, opts());
            $('preview').innerHTML = `<div style="transform:scale(${Math.min(1, 250 / ((p.sheet ? 40 : p.w) * 3.78))});transform-origin:top center;background:#fff;border:1px dashed #94a3b8">${b.html}</div>`;
        }

        async function print() {
            const list = queue.filter(r => r.copies > 0);
            if (!list.length) return Swal.fire({ icon: 'info', title: 'القائمة فاضية', confirmButtonColor: '#119744' });
            $('btnPrint').disabled = true;
            try {
                const n = await NSLabels.print(list.map(r => ({ data: data(r), copies: r.copies })), $('preset').value, opts());
                Swal.fire({ toast: true, position: 'bottom-start', icon: 'success', title: `اتبعت ${n} استيكر للطباعة`, showConfirmButton: false, timer: 2200 });
            } catch (e) { Swal.fire('تعذرت الطباعة', e.message || 'راجع الطابعة', 'error'); } finally { $('btnPrint').disabled = false; }
        }

        $('search').oninput = renderResults;
        $('search').onkeydown = e => { if (e.key === 'Enter') { e.preventDefault(); const c = e.target.value.trim(); pick(items.find(i => i.barcode === c) || hits[0]); } };
        $('results').onclick = e => { const h = e.target.closest('[data-i]'); if (h) pick(hits[h.dataset.i]); };
        ['weight', 'amount', 'copies'].forEach(id => $(id).onkeydown = e => { if (e.key === 'Enter') { e.preventDefault(); addRow(); } });
        $('weight').oninput = () => { if ($('weight').value) $('amount').value = ''; };
        $('amount').oninput = () => { if ($('amount').value) $('weight').value = ''; };
        $('btnAdd').onclick = addRow;
        $('queue').oninput = e => { const i = e.target.dataset.i; if (i != null) { queue[i].copies = Math.max(0, Math.min(500, parseInt(e.target.value) || 0)); $('printCount').textContent = `${queue.reduce((s, r) => s + r.copies, 0)} استيكر`; } };
        $('queue').onclick = e => { const d = e.target.closest('[data-del]'); if (d) { queue.splice(d.dataset.del, 1); renderQueue(); } };
        $('btnClear').onclick = () => { queue = []; renderQueue(); };
        ['preset', 'showShop', 'showDate', 'printer'].forEach(id => $(id).addEventListener('change', () => {
            cfg = { preset: $('preset').value, showShop: $('showShop').checked, showDate: $('showDate').checked, printer: $('printer').value };
            try { localStorage.setItem(STORE, JSON.stringify(cfg)); } catch (e) {}
            preview();
        }));
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
        renderResults(); renderQueue(); $('search').focus();
    })();
    </script>
    <script src="/js/nextstore-nav.js"></script>
    <script src="/js/nextstore-alerts.js"></script>
</body>
</html>
