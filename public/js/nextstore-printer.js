(function () {
    'use strict';

    const printableWidth = paper => (paper === '58mm' ? 48 : 72);

    // height بالملّي: لورق الرسيت نقيس طول المحتوى بعد الرسم حتى يخرج PDF بطول الفاتورة فقط
    function printCss(options, height) {
        const sheet = { a4: 'A4', a5: 'A5' }[options.paper];
        const m = options.margins || {};
        const [top, right, bottom, left] = [m.top, m.right, m.bottom, m.left].map(v => Number(v) || 0);
        // الطابعة الحرارية بتطبع 72مم من رول 80 (و48 من 58)، فالصفحة نفسها بعرض المنطقة المطبوعة عشان تتسنتر وما تتقطعش
        const roll = printableWidth(options.paper);
        const orient = options.orientation === 'landscape' ? ' landscape' : '';
        const pageSize = sheet ? sheet + orient : `${roll}mm ${height ? Math.ceil(height) + 14 + 'mm' : 'auto'}`;
        return `@page { size: ${pageSize}; margin: ${sheet ? `${top}mm ${right}mm ${bottom}mm ${left}mm` : 0}; }
            html, body { margin: 0; padding: 0; background: #fff; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            body { width: ${sheet ? 'auto' : `${roll}mm`}; ${sheet ? '' : `padding: ${top}mm ${right}mm ${bottom}mm ${left}mm; overflow: hidden;`} font-family: ${JSON.stringify(options.fontFamily || 'Tahoma')}; font-size: ${Number(options.fontSize) || 12}px; color: #000; direction: rtl; }
            * { box-sizing: border-box; } img, svg { max-width: 100%; }
            table { page-break-inside: auto; } tr { page-break-inside: avoid; }`;
    }

    window.NextStorePrinter = {
        async getPrinters() {
            const api = window.electronAPI;
            if (api && typeof api.getPrinters === 'function') {
                return await api.getPrinters();
            }
            const response = await fetch('/api/settings/printers', {
                headers: { Accept: 'application/json' }
            });
            if (!response.ok) throw new Error('تعذر جلب قائمة الطابعات.');
            const data = await response.json();
            return data.printers || [];
        },

        async silentPrint(options) {
            const api = window.electronAPI;
            options = { ...options, html: String(options.html || '').replace(/src="\/(?!\/)/g, `src="${location.origin}/`) };
            if (api && typeof api.silentPrint === 'function') {
                const payload = {
                    html: options.html,
                    printerName: options.printerName || '',
                    paper: options.paper || '80mm',
                    css: printCss(options),
                    settings: options.settings || {},
                    backupPrinterName: options.backupPrinterName || '',
                    useBackupOnFailure: Boolean(options.useBackupOnFailure),
                    comPort: options.comPort || '',
                    pageWidth: options.pageWidth || 576,
                    savePrintImages: Boolean(options.savePrintImages)
                };
                try {
                    return await api.silentPrint(payload);
                } catch (error) {
                    if (!payload.useBackupOnFailure || !payload.backupPrinterName) throw error;
                    return api.silentPrint({ ...payload, printerName: payload.backupPrinterName });
                }
            }

            const frame = document.createElement('iframe');
            frame.title = 'NextStore print';
            frame.style.cssText = 'position:fixed;left:-10000px;top:0;width:1000px;height:1400px;border:0;';
            document.body.appendChild(frame);
            const doc = frame.contentDocument;
            doc.open();
            doc.write(`<!doctype html><html><head><meta charset="utf-8"><style id="ns-page">${printCss(options)}</style></head><body>${options.html || ''}</body></html>`);
            doc.close();
            // صورة تالفة (لوجو/QR) لا يجب أن توقف الطباعة
            await Promise.all(Array.from(doc.images, img => (img.decode ? img.decode() : Promise.resolve()).catch(() => img.remove())));
            if (!['a4', 'a5'].includes(options.paper)) {
                doc.getElementById('ns-page').textContent = printCss(options, doc.body.scrollHeight * 25.4 / 96);
            }
            frame.contentWindow.onafterprint = () => setTimeout(() => frame.remove(), 500);
            frame.contentWindow.focus();
            frame.contentWindow.print();
            return { mode: 'browser-dialog' };
        }
    };
})();
