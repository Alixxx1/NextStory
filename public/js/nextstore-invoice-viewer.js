(function () {
    'use strict';
    const $ = id => document.getElementById(id);
    const esc = v => String(v ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const num = v => Number(v) || 0;
    const money = v => { const n = num(v); return (Number.isInteger(n) ? String(n) : n.toFixed(2)); };
    const qty = v => String(Number(num(v).toFixed(3)));
    let current = null;
    let options = {};

    function metrics(o) {
        const gross = num(o.grossTotal), service = num(o.service), disc = num(o.invoiceDiscount);
        const rounding = num(o.rounding);
        const net = gross + service - disc + rounding, returns = num(o.returnsValue), paid = num(o.paid), cost = num(o.cost);
        return { gross, service, disc, rounding, net, returns, paid, cost, itemDisc: num(o.itemDiscount), due: Math.max(0, net - returns - paid), profit: net - returns - cost };
    }

    function card(title, strong, lines) {
        return `<div class="iv-card"><h6>${title}</h6><div class="iv-strong">${strong}</div>${lines.filter(Boolean).map(l => `<div class="iv-line">${l}</div>`).join('')}</div>`;
    }

    function row(label, value, cls = '') {
        return `<div class="iv-row ${cls}"><span>${label}</span><b class="en-digit">${value}</b></div>`;
    }

    function render(o) {
        const m = metrics(o);
        const items = Array.isArray(o.items) ? o.items : [];
        const isCash = !o.customerId;
        $('ivTitle').innerHTML = `فاتورة رقم <span class="en-digit">#${esc(o.orderNo)}</span>`;
        $('ivHeadTotal').textContent = `${money(m.net)} ج.م`;
        $('ivChips').innerHTML = [
            `<span class="iv-chip">${esc(o.orderType || 'استلام')}</span>`,
            o.status === 'suspended' ? '<span class="iv-chip is-warn">معلقة</span>' : '',
            o.deleted ? '<span class="iv-chip is-danger">ملغاة</span>' : '',
            m.returns > 0 ? '<span class="iv-chip is-warn">عليها مرتجع</span>' : ''
        ].join('');
        $('ivCards').innerHTML = [
            card('العميل', `${esc(o.customerName || 'عميل نقدي')}${o.customerCode ? ` <span class="text-muted en-digit" style="font-size:12px">#${esc(o.customerCode)}</span>` : ''}`, [
                o.customerPhone ? `<i class="fas fa-phone ms-1 text-muted"></i><span class="en-digit">${esc(o.customerPhone)}</span>` : '',
                o.customerAddress ? `<i class="fas fa-location-dot ms-1 text-muted"></i>${esc(o.customerAddress)}${o.customerZone ? ` (${esc(o.customerZone)})` : ''}` : ''
            ]),
            card('الإنشاء', `<span class="en-digit">${esc(o.createdAt || '—')}</span>`, [`بواسطة: ${esc(o.createdBy || '—')}`, o.createdDevice && o.createdDevice !== '—' ? `الجهاز: ${esc(o.createdDevice)}` : '']),
            card('الإغلاق', o.closedAt ? `<span class="en-digit">${esc(o.closedAt)}</span>` : 'لم تُغلق', [o.confirmedBy ? `بواسطة: ${esc(o.confirmedBy)}` : '']),
            card('التوصيل والخزنة', esc(o.courier || 'بدون مندوب'), [o.courierPhone ? `تليفون الطيار: <span class="en-digit">${esc(o.courierPhone)}</span>` : '', `الخزنة: ${esc(o.safe || '—')}`, o.shift ? `الوردية: ${esc(o.shift)}` : ''])
        ].join('');
        $('ivNote').innerHTML = [
            o.deleted && o.cancelReason ? `<div class="iv-note" style="background:#fef2f2;border-color:#fecaca;color:#991b1b"><i class="fas fa-ban ms-1"></i> أُلغيت بواسطة ${esc(o.cancelledBy || '—')}: ${esc(o.cancelReason)}</div>` : '',
            o.note ? `<div class="iv-note"><i class="fas fa-note-sticky ms-1"></i> ${esc(o.note)}</div>` : ''
        ].join('');
        $('ivItems').innerHTML = items.length ? items.map((it, i) => `<tr>
                <td class="en-digit">${i + 1}</td>
                <td class="iv-name">${esc(it.name)}${it.is_external ? '<span class="iv-ext">خوارج</span>' : ''}</td>
                <td class="iv-cat">${esc(it.category_name || '—')}</td>
                <td class="en-digit text-muted">${esc(it.barcode || '—')}</td>
                <td class="en-digit">${qty(it.qty)}</td>
                <td class="en-digit ${num(it.returned_qty) > 0 ? 'iv-returned' : 'text-muted'}">${num(it.returned_qty) > 0 ? qty(it.returned_qty) : '—'}</td>
                <td class="en-digit">${money(it.price)}</td>
                <td class="en-digit fw-bold">${money(it.total || num(it.qty) * num(it.price))}</td>
            </tr>`).join('') : '<tr><td colspan="8" class="py-5 text-muted">لا توجد أصناف مسجلة في هذه الفاتورة</td></tr>';
        const units = items.reduce((s, it) => s + num(it.qty), 0);
        $('ivSummary').innerHTML = `
            <h6><i class="fas fa-receipt ms-1 text-muted"></i> ملخص الفاتورة</h6>
            ${row('عدد الأصناف', items.length)}
            ${row('عدد الوحدات', qty(units))}
            <div class="iv-sep"></div>
            ${row('الإجمالي', `${money(m.gross)} ج.م`)}
            ${m.itemDisc > 0 ? row('خصم الأصناف', `${money(m.itemDisc)} ج.م`, 'is-minus') : ''}
            ${m.disc > 0 ? row('خصم الفاتورة', `${money(m.disc)} ج.م`, 'is-minus') : ''}
            ${m.service > 0 ? row('رسوم التوصيل', `${money(m.service)} ج.م`) : ''}
            ${m.rounding ? row('فرق التقريب', `${m.rounding > 0 ? '+' : ''}${money(m.rounding)} ج.م`) : ''}
            <div class="iv-total"><span>صافي الفاتورة</span><b class="en-digit">${money(m.net)} ج.م</b></div>
            ${row('المدفوع', `${money(m.paid)} ج.م`)}
            ${m.due > 0 ? row('المتبقي (آجل)', `${money(m.due)} ج.م`, 'is-minus') : ''}
            ${m.returns > 0 ? row('المرتجع', `${money(m.returns)} ج.م`, 'is-minus') : ''}
            ${options.showProfits ? `<div class="iv-sep"></div>${row('التكلفة', `${money(m.cost)} ج.م`)}${row('الربح', `${money(m.profit)} ج.م`, 'iv-profit')}` : ''}`;
        $('ivBtnReturn').disabled = Boolean(o.deleted) || o.status === 'suspended';
        $('ivBtnCustomer').disabled = isCash;
        $('ivBtnCost').classList.toggle('d-none', typeof options.onEditCost !== 'function');
    }

    async function printCopy() {
        const o = current, btn = $('ivBtnPrint');
        if (!o || btn.disabled) return;
        const m = metrics(o);
        const label = (icon, text, done) => { btn.innerHTML = `<i class="fas ${icon}"></i> <span>${text}</span>${done ? '' : ' <kbd>P</kbd>'}`; btn.classList.toggle('is-done', !!done); };
        btn.disabled = true;
        label('fa-spinner fa-spin', 'جاري الطباعة...', true);
        try {
            await window.NextStoreReceipt.printSales({
                number: o.orderNo, date: o.createdAt, cashier: o.createdBy, customerCode: o.orderType === 'توصيل' ? o.customerCode : '', dispatchCode: o.orderType === 'توصيل' ? `d${o.orderNo}` : '',
                customerName: o.customerId ? o.customerName : '', customerPhone: o.customerPhone, customerAddress: o.customerAddress,
                items: (o.items || []).map(it => ({ name: it.name, unit: Number.isInteger(num(it.qty)) ? 'قطعة' : 'كيلو', price: it.price, quantity: it.qty, total: it.total || num(it.qty) * num(it.price) })),
                subtotal: m.gross, discount: m.disc, deliveryFee: m.service, total: m.net,
                totalBeforeRounding: m.net - m.rounding, rounding: m.rounding, paid: m.paid, due: m.due,
                customerBalanceBefore: null, customerBalanceAfter: null
            }, { titleText: 'نسخة فاتورة' });
            label('fa-check', 'اتبعتت للطابعة', true);
        } catch (error) {
            if (window.Swal) Swal.fire('تعذرت الطباعة', error.message || 'تحقق من إعدادات الطابعة.', 'error');
        }
        setTimeout(() => { btn.disabled = false; label('fa-print', 'طباعة نسخة'); }, 1500);
    }

    function modal() {
        return bootstrap.Modal.getOrCreateInstance($('nsInvoiceViewer'));
    }

    document.addEventListener('DOMContentLoaded', () => {
        $('ivBtnPrint').addEventListener('click', printCopy);
        $('ivBtnReturn').addEventListener('click', () => current && (window.location.href = `/pos/returns/partial?invoice=${encodeURIComponent(current.orderNo)}`));
        $('ivBtnCustomer').addEventListener('click', () => current && current.customerId && (window.location.href = `/customers/show?id=${encodeURIComponent(current.customerId)}`));
        $('ivBtnCost').addEventListener('click', () => current && options.onEditCost && options.onEditCost(current));
        // P (أو ح بالكيبورد العربي) يطبع نسخة وانت فاتح الفاتورة
        $('nsInvoiceViewer').addEventListener('keydown', e => {
            if (['p', 'P', 'ح'].includes(e.key) && !/INPUT|TEXTAREA/.test(e.target.tagName)) { e.preventDefault(); printCopy(); }
        });
        $('nsInvoiceViewer').addEventListener('shown.bs.modal', () => $('ivBtnPrint').focus());
    });

    window.NextStoreInvoiceViewer = {
        open(order, opts = {}) {
            if (!order) return;
            current = order;
            options = opts;
            render(order);
            modal().show();
        },
        refresh(order) {
            if (current && order && String(order.id) === String(current.id)) { current = order; render(order); }
        },
        hide() { modal().hide(); }
    };
})();
