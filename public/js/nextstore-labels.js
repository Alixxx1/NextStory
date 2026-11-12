/* NextStore - استيكرات الباركود (أصناف / موظفين / موازين): مقاسات الرول الحراري وشيتات A4، الرسم والطباعة.
   NSLabels.build(list, presetId, opts) ← HTML جاهز بالباركود؛ NSLabels.print(list, presetId, opts) يطبع على طابعة الاستيكر. */
window.NSLabels = (() => {
    const PRESETS = [
        { id: '38x25', name: 'رول 38 × 25 مم', w: 38, h: 25 },
        { id: '40x30', name: 'رول 40 × 30 مم', w: 40, h: 30 },
        { id: '50x25', name: 'رول 50 × 25 مم', w: 50, h: 25 },
        { id: '50x30', name: 'رول 50 × 30 مم', w: 50, h: 30 },
        { id: '58x40', name: 'رول 58 × 40 مم', w: 58, h: 40 },
        { id: 'card', name: 'كارت موظف 86 × 54 مم', w: 86, h: 54 },
        { id: 'a4-65', name: 'شيت A4 — 65 استيكر (38 × 21)', w: 38.1, h: 21.2, sheet: { cols: 5, rows: 13, top: 10.7, right: 4.7, gx: 2.5, gy: 0 } },
        { id: 'a4-24', name: 'شيت A4 — 24 استيكر (70 × 37)', w: 70, h: 37, sheet: { cols: 3, rows: 8, top: 0.5, right: 0, gx: 0, gy: 0 } },
    ];
    const esc = v => String(v ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const money = v => (Math.round((Number(v) || 0) * 100) / 100).toFixed(2).replace(/\.00$/, '');
    const preset = id => PRESETS.find(p => p.id === id) || PRESETS[0];

    // EAN-13 لو الباركود 13 رقم برقم تحقق صحيح، وإلا CODE128 (بيقبل أي حروف وأرقام)
    function ean13Check(d12) {
        const s = [...d12].reduce((a, c, i) => a + Number(c) * (i % 2 ? 3 : 1), 0);
        return String((10 - (s % 10)) % 10);
    }
    const formatOf = code => (/^\d{13}$/.test(code) && ean13Check(code.slice(0, 12)) === code[12] ? 'EAN13' : 'CODE128');

    // استيكر واحد: d = {top, title, sub, price, code, footer}
    function label(d, p, o) {
        const small = p.h < 26;
        const fs = Number(o.fontPx) || (small ? 9 : 11);
        const bcH = Math.max(14, Math.round(p.h * (small ? 0.42 : 0.4) * 3.78 - (o.showText === false ? 0 : 8)));
        return `<div class="nsl" style="width:${p.w}mm;height:${p.h}mm;padding:1mm 1.5mm;overflow:hidden;display:flex;flex-direction:column;align-items:center;justify-content:space-between;text-align:center;font-family:Tahoma,Arial,sans-serif;font-size:${fs}px;line-height:1.15;color:#000;direction:rtl">
            ${o.showShop && d.top ? `<div style="font-weight:700;font-size:.85em;white-space:nowrap;overflow:hidden;max-width:100%">${esc(d.top)}</div>` : ''}
            ${o.showName !== false && d.title ? `<div style="font-weight:800;max-width:100%;overflow:hidden;display:-webkit-box;-webkit-line-clamp:${small ? 1 : 2};-webkit-box-orient:vertical">${esc(d.title)}</div>` : ''}
            ${d.sub ? `<div style="font-size:.85em;white-space:nowrap">${esc(d.sub)}</div>` : ''}
            ${d.code ? `<svg class="nsl-bc" data-code="${esc(d.code)}" data-h="${bcH}" data-text="${o.showText === false ? 0 : 1}" style="max-width:100%;height:auto"></svg>` : ''}
            ${o.showPrice !== false && d.price != null ? `<div style="font-weight:800;font-size:1.25em;white-space:nowrap;font-family:Arial,sans-serif">${money(d.price)} ج.م</div>` : ''}
            ${d.footer ? `<div style="font-size:.8em;white-space:nowrap">${esc(d.footer)}</div>` : ''}
        </div>`;
    }

    function drawBarcodes(root) {
        root.querySelectorAll('svg.nsl-bc').forEach(svg => {
            const code = svg.dataset.code;
            try {
                JsBarcode(svg, code, { format: formatOf(code), height: Number(svg.dataset.h) || 30, width: 1.4, margin: 0, fontSize: 11, textMargin: 0, displayValue: svg.dataset.text !== '0', flat: true });
            } catch (e) {
                JsBarcode(svg, code, { format: 'CODE128', height: Number(svg.dataset.h) || 30, width: 1.4, margin: 0, fontSize: 11, displayValue: svg.dataset.text !== '0' });
            }
        });
        return root;
    }

    // list = [{data, copies}] ← HTML: رول = كل استيكر صفحة، شيت = شبكة على A4 مع تخطي أول N خانة (من شيت مستعمل قبل كده)
    function build(list, presetId, o = {}) {
        const p = preset(presetId);
        const labels = list.flatMap(x => Array(Math.max(0, Math.min(500, Number(x.copies) || 0))).fill(x.data)).map(d => label(d, p, o));
        let html;
        if (p.sheet) {
            const s = p.sheet, per = s.cols * s.rows, cells = [...Array(Math.min(Number(o.skip) || 0, per - 1)).fill(''), ...labels];
            html = Array.from({ length: Math.ceil(cells.length / per) }, (_, i) => `<div style="width:210mm;height:296mm;padding:${s.top}mm ${s.right}mm 0;display:grid;grid-template-columns:repeat(${s.cols},${p.w}mm);grid-auto-rows:${p.h}mm;column-gap:${s.gx}mm;row-gap:${s.gy}mm;justify-content:center;align-content:start;${i ? 'page-break-before:always' : ''}">
                ${cells.slice(i * per, (i + 1) * per).map(c => c || `<div></div>`).join('')}</div>`).join('');
        } else {
            html = labels.map((l, i) => `<div style="${i ? 'page-break-before:always;' : ''}">${l}</div>`).join('');
        }
        const holder = document.createElement('div');
        holder.innerHTML = html;
        return { html: drawBarcodes(holder).innerHTML, count: labels.length, preset: p };
    }

    async function print(list, presetId, o = {}) {
        const b = build(list, presetId, o);
        if (!b.count) throw new Error('مفيش استيكرات للطباعة');
        await window.NextStorePrinter.silentPrint({ html: b.html, printerName: o.printer || '', ...(b.preset.sheet ? { paper: 'a4', margins: {} } : { label: { w: b.preset.w, h: b.preset.h } }) });
        return b.count;
    }

    return { PRESETS, preset, build, print, label, drawBarcodes, ean13Check, formatOf, money, esc };
})();
