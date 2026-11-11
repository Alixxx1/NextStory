(function () {
    'use strict';
    const SKIP = /إجراء|إجراءات|تحكم|خيارات|عمليات|أدوات|actions?/i;
    const visible = el => !!(el && el.offsetParent !== null && getComputedStyle(el).visibility !== 'hidden');
    const text = el => (el.innerText || el.textContent || '').replace(/\s+/g, ' ').trim();

    // يحوّل جدول HTML ظاهر إلى أعمدة وصفوف نظيفة (بدون أعمدة الأزرار والعناصر المخفية)
    function readTable(table) {
        const headRow = table.tHead && table.tHead.rows[table.tHead.rows.length - 1];
        const heads = headRow ? Array.from(headRow.cells) : [];
        const keep = heads.map(th => visible(th) && !th.classList.contains('no-print') && !th.classList.contains('d-print-none') && !SKIP.test(text(th)) && text(th) !== '');
        const pick = cells => cells.filter((c, i) => (heads.length ? keep[i] : true) && !c.classList.contains('no-print') && !c.classList.contains('d-print-none'));
        const rows = Array.from(table.tBodies).flatMap(b => Array.from(b.rows))
            .filter(visible)
            .map(r => Array.from(r.cells))
            .filter(cells => cells.length === heads.length || !heads.length)
            .map(cells => pick(cells).map(text))
            .filter(r => r.some(Boolean));
        const foot = table.tFoot ? Array.from(table.tFoot.rows).map(r => text(r)).filter(Boolean).join(' | ') : '';
        const caption = table.caption ? text(table.caption) : '';
        return { columns: pick(heads).map(text), rows, foot, caption };
    }

    function collectTables(selector) {
        const tables = selector
            ? Array.from(document.querySelectorAll(selector))
            : Array.from(document.querySelectorAll('table')).filter(t => visible(t) && !t.closest('.modal, .swal2-container, .no-print, .d-print-none'));
        return tables.map(readTable).filter(t => t.rows.length || t.columns.length);
    }

    // جدول بديل للصفحات بدون جداول: أزواج (عنوان: قيمة)
    function collectPairs(selector, labelSel, valueSel) {
        const rows = Array.from(document.querySelectorAll(selector)).filter(visible).map(row => {
            const l = row.querySelector(labelSel), v = row.querySelector(valueSel);
            return l && v ? [text(l).replace(/:$/, ''), text(v)] : null;
        }).filter(Boolean);
        return rows.length ? [{ columns: ['البيان', 'القيمة'], rows, foot: '', caption: '' }] : [];
    }

    async function print(options = {}) {
        const cfg = window.NextStorePrintConfig || {};
        const t = cfg.template || {};
        const tables = options.tables || (options.pairs ? collectPairs(...options.pairs) : collectTables(options.table));
        if (!tables.length) {
            if (window.Swal) Swal.fire('لا توجد بيانات', 'لا يوجد جدول ظاهر للطباعة.', 'info');
            return;
        }
        const roll = t.paper === '80mm' || t.paper === '58mm';
        const html = window.NextStoreReceipt.page(t, cfg.settings || {}, {
            title: options.title || cfg.label || document.title,
            user: cfg.user,
            filters: options.filters || '',
            totals: options.totals || tables.map(x => x.foot).filter(Boolean).join(' | '),
            tables
        });
        try {
            await window.NextStorePrinter.silentPrint({
                html,
                paper: t.paper || 'a4',
                orientation: t.orientation,
                fontFamily: (cfg.settings || {}).print_font_family,
                fontSize: t.fontSize,
                margins: roll ? { top: 2, right: 2, bottom: 2, left: 2 } : { top: 10, right: 8, bottom: 10, left: 8 }
            });
        } catch (error) {
            if (window.Swal) Swal.fire('تعذرت الطباعة', error.message || 'تحقق من إعدادات الطابعة.', 'error');
        }
    }

    window.NextStorePagePrint = { print, collectTables, collectPairs };
})();
