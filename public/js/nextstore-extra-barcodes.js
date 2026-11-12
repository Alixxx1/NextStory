/* NextStore - باركودات إضافية لنفس الصنف (مثلاً 30 ريحة لنفس المزيل بنفس السعر والمخزون).
   بتتخزن في sub_units كصفوف factor=1 و price=0 فالباركود يتقري في البيع كأنه باركود الصنف نفسه.
   NSExtraBarcodes(box, { taken(code) => اسم الصنف اللي واخد الباركود أو null }) ← { get(), set(list), split(subUnits) } */
window.NSExtraBarcodes = function (box, opts) {
    const digits = v => String(v ?? '').replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d)).trim();
    const esc = v => String(v).replace(/[&<>"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));
    let list = [];
    if (!document.getElementById("xb-style")) document.head.insertAdjacentHTML("beforeend", `<style id="xb-style">
        .xb-box { border: 1px solid #d5dde6; border-radius: 8px; padding: 8px 10px; margin-bottom: 10px; background: #f8fafc; }
        .xb-head { display: flex; justify-content: space-between; align-items: center; font-size: 13px; color: #17283d; margin-bottom: 6px; }
        .xb-count { font-size: 12px; font-weight: 700; color: #64748b; }
        .xb-row { display: flex; align-items: center; gap: 10px; }
        .xb-row .xb-input { flex: 1; height: 36px; }
        .xb-msg { font-size: 12.5px; font-weight: 700; white-space: nowrap; }
        .xb-msg.ok { color: #0d7a36; } .xb-msg.bad { color: #dc2626; }
        .xb-chips { display: flex; flex-wrap: wrap; gap: 5px; margin-top: 7px; max-height: 96px; overflow-y: auto; }
        .xb-chips:empty { display: none; }
        .xb-chip { background: #fff; border: 1px solid #cbd5e1; border-radius: 6px; padding: 1px 4px 1px 8px; font-family: Arial, sans-serif; font-weight: 700; font-size: 13px; direction: ltr; }
        .xb-chip button { border: 0; background: none; color: #dc2626; font-weight: 800; cursor: pointer; padding: 0 4px; }
    </style>`);
    box.innerHTML = `<div class="xb-head"><b>باركودات إضافية لنفس الصنف</b><span class="xb-count"></span></div>
        <div class="xb-row"><input type="text" class="ns-control xb-input" placeholder="اضرب الباركود (أو اكتبه واضغط Enter) — كل ريحة/شكل بباركودها" autocomplete="off"><span class="xb-msg"></span></div>
        <div class="xb-chips"></div>`;
    const input = box.querySelector('.xb-input'), msg = box.querySelector('.xb-msg');
    const say = (text, bad) => { msg.textContent = text; msg.className = 'xb-msg ' + (bad ? 'bad' : 'ok'); };
    function draw() {
        box.querySelector('.xb-count').textContent = list.length ? `${list.length} باركود` : 'نفس السعر ونفس المخزون';
        box.querySelector('.xb-chips').innerHTML = list.map((b, i) => `<span class="xb-chip">${esc(b)}<button type="button" data-i="${i}" title="حذف">×</button></span>`).join('');
    }
    input.addEventListener('keydown', e => {
        if (e.key !== 'Enter') return;
        e.preventDefault(); e.stopPropagation();
        const code = digits(input.value);
        input.value = '';
        if (!code) return;
        const owner = list.includes(code) ? 'نفس الصنف (متسجل فوق)' : opts.taken(code);
        if (owner) return say(`${code} مسجل على: ${owner}`, true);
        list.push(code); draw(); say(`اتضاف ${code}`, false);
    });
    box.querySelector('.xb-chips').addEventListener('click', e => { const b = e.target.closest('[data-i]'); if (b) { list.splice(+b.dataset.i, 1); draw(); } });
    const isExtra = u => u && +u.factor === 1 && !+u.price && u.barcode;
    draw();
    return {
        get: unit => list.map(b => ({ name: unit || 'قطعة', factor: 1, barcode: b, price: 0 })),
        set(units) { list = (units || []).filter(isExtra).map(u => String(u.barcode)); msg.textContent = ''; draw(); },
        packsOnly: units => (units || []).filter(u => !isExtra(u)),
    };
};
