(function () {
    'use strict';
    const esc = v => String(v ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const money = v => Number(v || 0).toFixed(2).replace(/\.00$/, '');
    const qty = v => String(Number(Number(v || 0).toFixed(3)));
    const ones = ['', 'واحد', 'اثنان', 'ثلاثة', 'أربعة', 'خمسة', 'ستة', 'سبعة', 'ثمانية', 'تسعة', 'عشرة', 'أحد عشر', 'اثنا عشر', 'ثلاثة عشر', 'أربعة عشر', 'خمسة عشر', 'ستة عشر', 'سبعة عشر', 'ثمانية عشر', 'تسعة عشر'];
    const tens = ['', '', 'عشرون', 'ثلاثون', 'أربعون', 'خمسون', 'ستون', 'سبعون', 'ثمانون', 'تسعون'];
    const hund = ['', 'مائة', 'مائتان', 'ثلاثمائة', 'أربعمائة', 'خمسمائة', 'ستمائة', 'سبعمائة', 'ثمانمائة', 'تسعمائة'];

    function below1000(n) {
        const p = [];
        if (n >= 100) p.push(hund[Math.floor(n / 100)]);
        n %= 100;
        if (n >= 20) p.push(n % 10 ? `${ones[n % 10]} و${tens[Math.floor(n / 10)]}` : tens[Math.floor(n / 10)]);
        else if (n) p.push(ones[n]);
        return p.join(' و');
    }

    function words(n) {
        n = Math.floor(Math.abs(n));
        if (!n) return 'صفر';
        const m = Math.floor(n / 1e6), th = Math.floor((n % 1e6) / 1000), r = n % 1000, p = [];
        if (m) p.push(m === 1 ? 'مليون' : m === 2 ? 'مليونان' : `${below1000(m)} ${m <= 10 ? 'ملايين' : 'مليون'}`);
        if (th) p.push(th === 1 ? 'ألف' : th === 2 ? 'ألفان' : `${below1000(th)} ${th <= 10 ? 'آلاف' : 'ألف'}`);
        if (r) p.push(below1000(r));
        return p.join(' و');
    }

    // QR حاد كـ SVG من نص/لينك (الصور المرفوعة كانت بتطلع مشوهة على الطابعة الحرارية)
    function qr(text, mm) {
        const lib = window.qrcode;
        if (!text || !lib) return '';
        lib.stringToBytes = lib.stringToBytesFuncs['UTF-8'];
        const q = lib(0, 'M');
        q.addData(text, 'Byte');
        q.make();
        const n = q.getModuleCount();
        let d = '';
        for (let r = 0; r < n; r++) for (let c = 0; c < n; c++) if (q.isDark(r, c)) d += `M${c} ${r}h1v1h-1z`;
        return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="-2 -2 ${n + 4} ${n + 4}" shape-rendering="crispEdges" style="display:block;width:${mm}mm;height:${mm}mm;margin:0 auto"><rect x="-2" y="-2" width="${n + 4}" height="${n + 4}" fill="#fff"/><path d="${d}" fill="#000"/></svg>`;
    }

    // رقم تليفون لوحده يتحول tel: عشان الموبايل يتصل على طول
    const qrData = v => (/^\+?[\d\s-]{7,}$/.test(v = String(v || '').trim()) ? `tel:${v.replace(/[\s-]/g, '')}` : v);

    function qrBlock(s, paper) {
        if (!s.show_qr) return '';
        const mode = s.qr_mode || 'both';
        const pay = String(s.instapay_value || '').trim();
        const items = [];
        if (mode !== 'pay' && (s.qr_link || s.receipt_qr)) items.push(['link', esc(s.qr_link_label || '')]);
        if (mode !== 'link' && pay) items.push(['pay', `InstaPay${/^https?:/i.test(pay) ? '' : `<br><span dir="ltr">${esc(pay)}</span>`}`]);
        if (!items.length) return '';
        const mm = paper === '58mm' ? (items.length > 1 ? 21 : 26) : (items.length > 1 ? 27 : 32);
        const code = k => (k === 'pay' ? qr(pay, mm) : s.qr_link ? qr(qrData(s.qr_link), mm)
            : `<img src="${esc(s.receipt_qr)}" alt="QR" style="display:block;width:${mm}mm;height:${mm}mm;object-fit:contain;margin:0 auto">`);
        return `<div style="display:flex;justify-content:center;gap:5mm;margin:5px 0;text-align:center">${items.map(([k, label]) => `<div style="width:${mm + 4}mm">${code(k)}${label ? `<div style="font-size:.85em;font-weight:700;line-height:1.25;overflow-wrap:anywhere;margin-top:2px">${label}</div>` : ''}</div>`).join('')}</div>`;
    }

    const tafqeet = total => `فقط ${words(total)} جنيهاً مصرياً لا غير`;

    // d: بيانات الفاتورة، s: إعدادات النظام، t: قالب فاتورة المبيعات
    function sales(d, s, t) {
        const paper = t.paper || s.receipt_paper || '80mm';
        const wide = paper === 'a4' || paper === 'a5';
        const bf = (size, bold) => `font-size:${size}px;font-weight:${bold ? 800 : 400}`;
        const line = s.table_borders === 'none' ? 'border:0' : s.table_borders === 'full' ? 'border:1px solid #000' : 'border:0;border-bottom:1px solid #000';
        const box = 'border:1px solid #000';
        const logo = t.showLogo !== false && s.show_logo && s.receipt_logo
            ? `<img src="${esc(s.receipt_logo)}" alt="" style="max-width:${wide ? 100 : 70}%;max-height:${wide ? 90 : 80}px;object-fit:contain">` : '';
        const phones = [s.shop_phone1, s.shop_phone2, s.complaint_phone].filter(Boolean).map(esc);
        // فاتورة التوصيل: الباركود الوحيد فيها هو باركود الطيار (d + رقم الطلب)، ورقم الطلب مكتوب نص بس
        const barcode = d.dispatchCode || (t.showBarcode !== false && s.show_invoice_barcode)
            ? `<svg class="rcp-barcode" data-code="${esc(d.dispatchCode || '')}" style="display:block;margin:4px auto;max-width:100%"></svg>` : '';
        const cust = s.show_customer && t.showCounterparty !== false;
        const cashier = s.show_cashier && t.showCashier !== false;
        const showBal = s.show_customer_balance && d.customerBalanceBefore != null && d.customerBalanceAfter != null;
        const title = t.titleText || 'فاتورة مبيعات';
        const items = d.items || [];
        const disc = s.show_discount_column;
        const th = (c, w) => `<th style="${line};padding:3px 2px;text-align:center;${w ? `width:${w}` : ''}">${c}</th>`;
        const td = (c, st = '') => `<td style="${line};padding:3px 2px;text-align:center;${st}">${c}</td>`;
        const rnd = Number(d.rounding) || 0;
        // سطر خفيف لقيمة الفاتورة قبل التقريب حتى لا يُقرأ بدلاً من الإجمالي
        const preRound = rnd ? `<tr><td colspan="2" style="${box};padding:1px 6px;text-align:center;font-size:.82em;font-weight:400;color:#555">قبل التقريب ${money(d.totalBeforeRounding)} ج.م &nbsp;(تقريب ${rnd > 0 ? '+' : ''}${money(rnd)})</td></tr>` : '';
        const totalLabel = d.totalLabel || 'إجمالي الفاتورة';
        const extra = (d.extraRows || []).map(([label, value]) => `<tr><td style="${box};padding:3px 6px;text-align:center">${esc(value)}</td><td style="${box};padding:3px 6px;font-weight:700;text-align:right">${esc(label)}</td></tr>`).join('');
        const tot = (label, val, big) => `<tr><td style="${box};padding:3px 6px;text-align:center;${big ? 'font-weight:800;font-size:1.15em' : ''}">${val}</td><td style="${box};padding:3px 6px;width:34%;font-weight:700;text-align:right">${label}</td></tr>`;
        const wrap = inner => `<article dir="rtl" style="font-family:${esc(s.print_font_family || 'Tahoma')},Arial,sans-serif;font-size:${Number(s.print_font_size) || 12}px;font-weight:${s.print_bold ? 700 : 400};color:#000;padding:${wide ? 8 : 2}px;${s.receipt_frame ? 'border:1px solid #000;' : ''}">${inner}</article>`;
        const footer = `${s.receipt_penultimate_line ? `<div style="text-align:center;${bf(s.font_size_notes, s.bold_notes)}">${esc(s.receipt_penultimate_line)}</div>` : ''}
            ${t.footerText ? `<div style="text-align:center">${esc(t.footerText)}</div>` : ''}
            ${qrBlock(s, paper)}
            <div style="text-align:center;margin-top:6px;${bf(s.font_size_footer, s.bold_footer)}">${esc(s.receipt_final_line || '')}</div>`;

        if (wide) {
            const lbl = (a, b) => b ? `<div style="${bf(s.font_size_order, s.bold_order)}">${a} ${esc(b)}</div>` : '';
            const body = items.map((it, i) => `<tr>${td(i + 1)}${td(esc(it.name), 'text-align:right;padding-inline:6px')}${td(money(it.price))}${td(qty(it.quantity))}${disc ? td(money(it.discount || 0)) : ''}${td(money(it.total))}</tr>`).join('');
            return wrap(`
                <div style="display:grid;grid-template-columns:1fr 130px 1fr;gap:8px;align-items:start;margin-bottom:8px">
                    <div style="text-align:right">
                        ${t.showShopInfo !== false ? `<div style="${bf(s.font_size_shop, s.bold_shop)}">${esc(s.shop_name)}</div>
                        ${s.shop_address ? `<div style="${bf(s.font_size_address, s.bold_address)}">${esc(s.shop_address)}</div>` : ''}
                        ${phones.map(p => `<div style="${bf(s.font_size_details + 2, true)}">${p}</div>`).join('')}` : ''}
                    </div>
                    <div style="text-align:center">${logo}</div>
                    <div style="text-align:right">
                        <div style="${bf(s.font_size_order, true)}">${esc(title)}</div>
                        ${lbl('التاريخ', d.date)}${lbl('رقم الطلب', d.number)}${d.customerCode ? lbl('كود العميل', d.customerCode) : ''}${cust ? lbl('العميل', d.customerName) + lbl('التليفون', d.customerPhone) + lbl('', d.customerAddress) : ''}
                        ${t.headerText ? `<div>${esc(t.headerText)}</div>` : ''}
                    </div>
                </div>
                ${barcode}
                ${t.showItems !== false ? `<table style="width:100%;border-collapse:collapse;margin-bottom:8px"><thead><tr>${th('م', '28px')}${th('الصنف')}${th('السعر', '60px')}${th('الكمية', '55px')}${disc ? th('الخصم', '50px') : ''}${th('الإجمالي', '70px')}</tr></thead><tbody>${body}</tbody></table>` : ''}
                ${t.showTotals !== false ? `<table style="width:56%;border-collapse:collapse;margin-inline-end:auto">
                    ${cashier ? tot('الكاشير', esc(d.cashier)) : ''}
                    ${Number(d.discount) > 0 ? tot('الخصم', money(d.discount)) : ''}
                    ${Number(d.deliveryFee) > 0 ? tot('خدمة التوصيل', money(d.deliveryFee)) : ''}
                    ${preRound}${tot(totalLabel, `${money(d.total)} ج.م`, true)}${extra}
                    <tr><td colspan="2" style="${box};padding:3px 6px;text-align:center">${tafqeet(d.total)}</td></tr>
                    ${d.paid != null ? tot('المدفوع', money(d.paid)) + tot('المتبقي', money(d.due)) : ''}
                    ${showBal ? tot('حساب قديم', `* مدين * ${money(d.customerBalanceBefore)}`) + tot('تحديث الحساب', `* مدين * ${money(d.customerBalanceAfter)}`) : ''}
                </table>` : ''}
                ${footer}`);
        }

        // رول 80/58: كله جدول واحد مقفول بخطوط طول وعرض، والعناوين ما تتكسرش، والأرقام في سطر واحد
        const g = 'border:1px solid #000;padding:2px 3px;line-height:1.35';
        const L = 'font-weight:700;white-space:nowrap;width:1%;background:#eee';
        const V = 'text-align:center;white-space:nowrap';
        const tbl = rows => rows ? `<table style="width:100%;border-collapse:collapse;margin-bottom:4px">${rows}</table>` : '';
        const kv = (a, b, big) => b !== '' && b != null ? `<tr><td style="${g};${L}">${a}</td><td style="${g};${V};${big ? 'font-weight:800;font-size:1.2em' : ''}">${b}</td></tr>` : '';
        // صفين صغيرين جنب بعض في سطر واحد بدل سطرين
        const pair = (a, x, b, y) => `<tr><td style="${g};${L}">${a}</td><td style="${g};${V}">${x}</td><td style="${g};${L}">${b}</td><td style="${g};${V}">${y}</td></tr>`;
        const span = (a, b, big) => `<tr><td style="${g};${L}">${a}</td><td colspan="3" style="${g};${V};${big ? 'font-weight:800;font-size:1.2em' : ''}">${b}</td></tr>`;
        const cols = disc ? [['صنف', ''], ['وحدة', '12%'], ['سعر', '15%'], ['كمية', '12%'], ['خصم', '11%'], ['إجمالي', '18%']] : [['صنف', ''], ['وحدة', '13%'], ['سعر', '17%'], ['كمية', '13%'], ['إجمالي', '20%']];
        const num = c => `<td style="${g};${V};overflow:hidden">${c}</td>`;
        const body = items.map(it => `<tr><td style="${g};text-align:right;overflow-wrap:break-word">${esc(it.name)}</td>${num(esc(it.unit || 'قطعة'))}${num(money(it.price))}${num(qty(it.quantity))}${disc ? num(money(it.discount || 0)) : ''}${num(money(it.total))}</tr>`).join('');
        const cur = v => `${money(v)} ج.م`;
        const hasBal = showBal && (Number(d.customerBalanceBefore) || Number(d.customerBalanceAfter));
        const small = [
            Number(d.discount) > 0 || Number(d.deliveryFee) > 0 ? ['المجموع', money(d.subtotal)] : null,
            Number(d.discount) > 0 ? ['الخصم', money(d.discount)] : null,
            Number(d.deliveryFee) > 0 ? ['التوصيل', money(d.deliveryFee)] : null,
            ...(Number(d.due) > 0 ? [['المدفوع', money(d.paid)], ['المتبقي', money(d.due)]] : []),
            ...(hasBal ? [['حساب قديم', money(d.customerBalanceBefore)], ['الحساب الآن', money(d.customerBalanceAfter)]] : [])
        ].filter(Boolean);
        let smallRows = '';
        for (let i = 0; i < small.length; i += 2) smallRows += small[i + 1] ? pair(...small[i], ...small[i + 1]) : span(...small[i]);
        const extraRows = (d.extraRows || []).map(([label, value]) => span(esc(label), esc(value))).join('');
        const preRoundRow = rnd ? `<tr><td colspan="4" style="${g};text-align:center;font-size:.8em">قبل التقريب ${money(d.totalBeforeRounding)} (تقريب ${rnd > 0 ? '+' : ''}${money(rnd)})</td></tr>` : '';
        const barcodeSvg = barcode.replace('display:block;margin:4px auto;max-width:100%', 'display:block;flex:1 1 0;min-width:0;max-width:60%;height:42px');
        return wrap(`
            <div style="text-align:center">
                ${logo.replace(`max-height:${wide ? 90 : 80}px`, 'max-height:55px')}
                ${t.showShopInfo !== false ? `<div style="border-top:1.5px solid #000;margin-top:3px;${bf(s.font_size_shop, s.bold_shop)}">${esc(s.shop_name)}</div>
                ${s.shop_address ? `<div style="${bf(s.font_size_address, s.bold_address)}">${esc(s.shop_address)}</div>` : ''}
                ${phones.length ? `<div style="${bf(s.font_size_details, true)}">${phones.join(' - ')}</div>` : ''}` : ''}
                <div style="border-top:1.5px solid #000;border-bottom:1.5px solid #000;margin:3px 0;padding:1px;${bf(s.font_size_order, true)}">${esc(title)}</div>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;gap:6px;margin-bottom:3px">
                <div style="font-weight:800;white-space:nowrap;flex:none">رقم الطلب <span style="font-size:1.25em">${esc(d.number)}</span>${d.customerCode ? `<div>كود العميل <span style="font-size:1.25em">${esc(d.customerCode)}</span></div>` : ''}</div>${barcodeSvg}
            </div>
            ${tbl(kv('التاريخ', esc(d.date)) + (cashier ? kv('كاشير', esc(d.cashier || '')) : '')
                + (cust ? kv('العميل', esc(d.customerName || '')) + kv('تليفون', esc(d.customerPhone || '')) + kv('العنوان', d.customerAddress ? `<span style="white-space:normal">${esc(d.customerAddress)}</span>` : '') : ''))}
            ${t.headerText ? `<div style="text-align:center">${esc(t.headerText)}</div>` : ''}
            ${t.showItems !== false ? `<table style="width:100%;border-collapse:collapse;table-layout:fixed;margin-bottom:4px"><thead><tr style="background:#ddd;font-size:.9em">${cols.map(([c, w]) => `<th style="${g};white-space:nowrap;overflow:hidden;${w ? `width:${w}` : ''}">${c}</th>`).join('')}</tr></thead><tbody>${body}</tbody></table>` : ''}
            ${t.showTotals !== false ? tbl(smallRows + preRoundRow + span(esc(totalLabel), cur(d.total), true) + extraRows) : ''}
            ${footer}`);
    }

    // يبني الرسيت ويطبعه بإعدادات الطابعة المحفوظة (window.NextStoreReceiptConfig.settings)
    async function printSales(data, overrides = {}) {
        const s = (window.NextStoreReceiptConfig || {}).settings || {};
        const t = { ...((s.print_templates || {}).sales_invoice || {}), ...overrides };
        const holder = document.createElement('div');
        holder.innerHTML = sales(data, s, t);
        barcode(holder, data.number);
        return window.NextStorePrinter.silentPrint({
            html: holder.innerHTML, paper: t.paper || s.receipt_paper || '80mm', fontFamily: s.print_font_family, fontSize: s.print_font_size,
            printerName: s.printer_primary, backupPrinterName: s.printer_backup, useBackupOnFailure: s.printer_backup_auto,
            comPort: s.com_port, pageWidth: s.page_width_px, savePrintImages: s.save_print_images,
            margins: { left: s.margin_left, right: s.margin_right, top: s.margin_top, bottom: s.margin_bottom }, settings: s
        });
    }

    function barcode(root, number) {
        root.querySelectorAll('.rcp-barcode').forEach(svg => {
            const rider = svg.dataset.code;
            if (window.JsBarcode) window.JsBarcode(svg, rider || String(number), { format: 'CODE128', width: 1.6, height: 34, displayValue: true, fontSize: rider ? 12 : 10, fontOptions: rider ? 'bold' : '', margin: 6, ...(rider ? { text: `طيار ${number}` } : {}) });
        });
    }

    // طباعة الصفحات (عملاء، موردين، تقارير...): c إعداد الصفحة، doc = {title, user, filters, columns, rows, totals}
    function page(c, s, doc) {
        const now = new Date();
        const pad = n => String(n).padStart(2, '0');
        const stamp = `${now.getFullYear()}/${pad(now.getMonth() + 1)}/${pad(now.getDate())} ${pad(now.getHours())}:${pad(now.getMinutes())}`;
        const narrow = c.paper === '80mm' || c.paper === '58mm';
        const cell = 'border:1px solid #000;padding:4px 5px;text-align:right';
        const tables = (doc.tables || [{ columns: doc.columns, rows: doc.rows }]).map(tb => `
            ${tb.caption ? `<div style="font-weight:800;margin:8px 0 3px">${esc(tb.caption)}</div>` : ''}
            <table style="width:100%;border-collapse:collapse;margin-bottom:8px">
                <thead><tr>${(tb.columns || []).map(col => `<th style="${cell};background:#e5e7eb">${esc(col)}</th>`).join('')}</tr></thead>
                <tbody>${(tb.rows || []).map((r, i) => `<tr style="${c.zebra && i % 2 ? 'background:#f3f4f6' : ''}">${r.map(v => `<td style="${cell}">${esc(v)}</td>`).join('')}</tr>`).join('')}</tbody>
            </table>`).join('');
        const logo = c.showLogo && s.receipt_logo ? `<img src="${esc(s.receipt_logo)}" alt="" style="max-height:60px;max-width:120px;object-fit:contain">` : '';
        const shop = c.showShopInfo ? `<div style="font-weight:800;font-size:1.35em">${esc(s.shop_name)}</div>${s.shop_address ? `<div>${esc(s.shop_address)}</div>` : ''}${s.shop_phone1 ? `<div>${esc([s.shop_phone1, s.shop_phone2].filter(Boolean).join(' - '))}</div>` : ''}` : '';
        const meta = `${c.showDateTime ? `<div>التاريخ: ${stamp}</div>` : ''}${c.showUser && doc.user ? `<div>المستخدم: ${esc(doc.user)}</div>` : ''}`;
        return `<article dir="rtl" style="font-family:${esc(s.print_font_family || 'Tahoma')},Arial,sans-serif;font-size:${Number(c.fontSize) || 12}px;color:#000">
            <div style="display:${narrow ? 'block;text-align:center' : 'grid;grid-template-columns:1fr auto 1fr;gap:10px;align-items:center'};border-bottom:2px solid #000;padding-bottom:6px;margin-bottom:8px">
                <div style="text-align:${narrow ? 'center' : 'right'}">${shop}</div><div style="text-align:center">${logo}</div><div style="text-align:${narrow ? 'center' : 'left'}">${meta}</div>
            </div>
            <div style="text-align:center;font-weight:800;font-size:1.25em;margin-bottom:6px">${esc(c.titleText || doc.title || '')}</div>
            ${c.showFilters && doc.filters ? `<div style="margin-bottom:6px">${esc(doc.filters)}</div>` : ''}
            ${tables}
            ${c.showTotals && doc.totals ? `<div style="margin-top:8px;font-weight:800;border:1px solid #000;padding:5px">${esc(doc.totals)}</div>` : ''}
            ${c.showSignature ? '<div style="display:flex;justify-content:space-between;margin-top:30px"><span>توقيع المستلم: ............</span><span>توقيع المسؤول: ............</span></div>' : ''}
            ${c.footerText ? `<div style="text-align:center;margin-top:10px">${esc(c.footerText)}</div>` : ''}
        </article>`;
    }

    // كشف تسليم الطيار: الطلبات اللي خرجت معاه وقيمتها وعمولته والصافي المطلوب منه (نفس مقاس ورق فاتورة المبيعات)
    function riderSlip(run, s, paper) {
        const wide = paper === 'a4' || paper === 'a5', tiny = paper === '58mm'; // رول 58: خط أصغر والعناوين تلف
        const g = `border:1px solid #000;padding:${tiny ? '2px' : '3px 4px'};line-height:1.35`;
        const L = `font-weight:700;${tiny ? '' : 'white-space:nowrap;'}background:#eee`;
        const fs = Number(s.print_font_size) || 12;
        const net = Number(run.net) || 0;
        const kv = (a, b, big) => `<tr><td style="${g};${L}">${a}</td><td style="${g};text-align:center;white-space:nowrap;${big ? 'font-weight:800;font-size:1.25em' : ''}">${b}</td></tr>`;
        const cols = wide ? ['#', 'رقم الطلب', 'العميل', 'العنوان', 'الفاتورة', 'هيحصّل', 'العمولة'] : ['طلب', 'العميل', 'تحصيل', 'عمولة'];
        const rows = (run.orders || []).map((o, i) => (wide
            ? [i + 1, `#${o.orderNo}`, o.customerName, o.customerAddress || '', money(o.amount), money(o.collect ?? o.amount), money(o.commission)]
            : [`#${o.orderNo}`, o.customerName, money(o.collect ?? o.amount), money(o.commission)])
            .map((v, j) => `<td style="${g};${j === (wide ? 2 : 1) || (wide && j === 3) ? 'text-align:right' : 'text-align:center;white-space:nowrap'}">${esc(v)}</td>`).join(''));
        return `<article dir="rtl" style="font-family:${esc(s.print_font_family || 'Tahoma')},Arial,sans-serif;font-size:${tiny ? Math.min(fs, 10) : fs}px;color:#000;padding:${wide ? 8 : 2}px">
            <div style="text-align:center;font-weight:800;font-size:1.2em">${esc(s.shop_name || '')}</div>
            <div style="text-align:center;font-weight:800;border-top:1.5px solid #000;border-bottom:1.5px solid #000;margin:4px 0;padding:2px">كشف تسليم طيار رقم ${esc(run.runNo)}</div>
            <table style="width:100%;border-collapse:collapse;margin-bottom:4px">
                ${kv('الطيار', esc(run.riderName) + (run.riderPhone ? ` - ${esc(run.riderPhone)}` : ''))}${kv('التاريخ', esc(String(run.createdAt || '').slice(0, 16)))}${run.createdBy ? kv('سلّمه', esc(run.createdBy)) : ''}
            </table>
            <table style="width:100%;border-collapse:collapse;margin-bottom:4px;${wide ? '' : 'table-layout:fixed'}">
                <thead><tr style="background:#ddd">${cols.map((c, j) => `<th style="${g};white-space:nowrap;${wide || j === 1 ? '' : `width:${[17, 0, 21, 18][j]}%`}">${c}</th>`).join('')}</tr></thead>
                <tbody>${rows.map(r => `<tr>${r}</tr>`).join('')}</tbody>
            </table>
            <table style="width:${wide ? '56%' : '100%'};border-collapse:collapse;margin-inline-end:auto">
                ${kv('عدد الطلبات', run.ordersCount)}${kv('إجمالي الفواتير', money(run.ordersTotal))}${kv('المطلوب تحصيله', money(run.collectTotal))}${kv('عمولة الطيار', money(run.commission))}
                ${kv(net >= 0 ? 'الصافي المطلوب منه' : 'المستحق للطيار', `${money(Math.abs(net))} ج.م`, true)}
            </table>
            ${run.notes ? `<div style="margin-top:4px">ملاحظات: ${esc(run.notes)}</div>` : ''}
            <div style="display:flex;justify-content:space-between;margin-top:${wide ? 30 : 16}px"><span>توقيع الطيار: ..........</span><span>المسؤول: ..........</span></div>
        </article>`;
    }

    async function printRiderSlip(run) {
        const s = (window.NextStoreReceiptConfig || {}).settings || {};
        const paper = ((s.print_templates || {}).sales_invoice || {}).paper || s.receipt_paper || '80mm';
        return window.NextStorePrinter.silentPrint({
            html: riderSlip(run, s, paper), paper, fontFamily: s.print_font_family, fontSize: s.print_font_size,
            printerName: s.printer_primary, backupPrinterName: s.printer_backup, useBackupOnFailure: s.printer_backup_auto,
            comPort: s.com_port, pageWidth: s.page_width_px, savePrintImages: s.save_print_images,
            margins: { left: s.margin_left, right: s.margin_right, top: s.margin_top, bottom: s.margin_bottom }, settings: s
        });
    }

    window.NextStoreReceipt = { qr, sales, page, printSales, barcode, tafqeet, riderSlip, printRiderSlip };
})();
