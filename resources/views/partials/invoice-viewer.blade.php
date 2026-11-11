<style>
    #nsInvoiceViewer { --iv-border: #d5dde6; --iv-muted: #64748b; --iv-text: #1e293b; --iv-soft: #f8fafc; --iv-green: #119744; font-family: 'Cairo', 'Segoe UI', Tahoma, sans-serif; }
    #nsInvoiceViewer .modal-dialog { max-width: min(1280px, 96vw); }
    #nsInvoiceViewer .modal-content { height: min(860px, 92vh); border: 0; border-radius: 10px; overflow: hidden; }
    #nsInvoiceViewer .iv-head { display: flex; align-items: center; gap: 12px; padding: 10px 16px; background: #17283d; color: #fff; }
    #nsInvoiceViewer .iv-title { font-size: 17px; font-weight: 800; margin: 0; }
    #nsInvoiceViewer .iv-chip { font-size: 12px; font-weight: 700; padding: 2px 10px; border-radius: 999px; background: rgba(255,255,255,.12); color: #e2e8f0; }
    #nsInvoiceViewer .iv-chip.is-warn { background: #fef3c7; color: #92400e; }
    #nsInvoiceViewer .iv-chip.is-danger { background: #fee2e2; color: #991b1b; }
    #nsInvoiceViewer .iv-readonly { font-size: 12px; color: #cbd5e1; display: inline-flex; align-items: center; gap: 6px; }
    #nsInvoiceViewer .iv-head-total { margin-right: auto; text-align: left; line-height: 1.1; }
    #nsInvoiceViewer .iv-head-total span { display: block; font-size: 11px; color: #94a3b8; font-weight: 700; }
    #nsInvoiceViewer .iv-head-total b { font-size: 22px; font-weight: 800; color: #fff; }
    #nsInvoiceViewer tbody tr:nth-child(even) td { background: #fafbfc; }
    #nsInvoiceViewer tbody tr:hover td { background: #f0faf4; }
    #nsInvoiceViewer .iv-body { flex: 1; min-height: 0; display: grid; grid-template-columns: minmax(0, 1fr) 290px; background: #eef2f5; gap: 10px; padding: 10px; }
    #nsInvoiceViewer .iv-main { display: flex; flex-direction: column; gap: 10px; min-height: 0; }
    #nsInvoiceViewer .iv-cards { display: grid; grid-template-columns: 1.5fr 1fr 1fr 1fr; gap: 8px; }
    #nsInvoiceViewer .iv-card { background: #fff; border: 1px solid var(--iv-border); border-radius: 8px; padding: 8px 12px; min-width: 0; }
    #nsInvoiceViewer .iv-card h6 { font-size: 11.5px; font-weight: 700; color: var(--iv-muted); margin: 0 0 3px; }
    #nsInvoiceViewer .iv-card .iv-strong { font-weight: 800; color: var(--iv-text); font-size: 14px; }
    #nsInvoiceViewer .iv-card .iv-line { font-size: 12.5px; color: #334155; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    #nsInvoiceViewer .iv-note { background: #fffbeb; border: 1px solid #fde68a; color: #78350f; border-radius: 8px; padding: 6px 12px; font-weight: 600; font-size: 13px; }
    #nsInvoiceViewer .iv-table-wrap { flex: 1; min-height: 0; overflow: auto; background: #fff; border: 1px solid var(--iv-border); border-radius: 8px; }
    #nsInvoiceViewer table { width: 100%; border-collapse: separate; border-spacing: 0; }
    #nsInvoiceViewer th { position: sticky; top: 0; background: #f1f5f9; color: #475569; font-size: 12.5px; font-weight: 700; padding: 9px 8px; text-align: center; border-bottom: 1px solid #cbd5e1; white-space: nowrap; }
    #nsInvoiceViewer td { padding: 9px 8px; text-align: center; font-size: 13.5px; font-weight: 600; color: var(--iv-text); border-bottom: 1px solid #edf1f5; }
    #nsInvoiceViewer td.iv-name { text-align: right; font-weight: 700; }
    #nsInvoiceViewer td.iv-cat { color: var(--iv-muted); font-size: 12.5px; }
    #nsInvoiceViewer td.iv-returned { color: #b45309; }
    #nsInvoiceViewer .iv-ext { font-size: 11px; color: #9a3412; background: #fff7ed; border-radius: 4px; padding: 0 5px; margin-inline-start: 4px; }
    #nsInvoiceViewer .iv-side { background: #fff; border: 1px solid var(--iv-border); border-radius: 8px; padding: 10px 12px; display: flex; flex-direction: column; gap: 2px; overflow: auto; }
    #nsInvoiceViewer .iv-side h6 { font-size: 13px; font-weight: 800; color: var(--iv-text); margin: 0 0 6px; padding-bottom: 6px; border-bottom: 1px solid #edf1f5; }
    #nsInvoiceViewer .iv-row { display: flex; justify-content: space-between; align-items: center; padding: 5px 0; font-size: 13px; font-weight: 600; color: #334155; }
    #nsInvoiceViewer .iv-row b { font-weight: 800; color: var(--iv-text); }
    #nsInvoiceViewer .iv-row.is-minus b { color: #b45309; }
    #nsInvoiceViewer .iv-sep { border-top: 1px dashed #d5dde6; margin: 4px 0; }
    #nsInvoiceViewer .iv-total { background: #ecfdf3; border: 1px solid #bfe5cd; border-radius: 8px; padding: 8px 10px; margin: 6px 0; }
    #nsInvoiceViewer .iv-total span { font-size: 12.5px; font-weight: 700; color: #0d7a36; }
    #nsInvoiceViewer .iv-total b { display: block; font-size: 24px; font-weight: 800; color: #0d7a36; }
    #nsInvoiceViewer .iv-profit b { color: #1d4ed8; }
    #nsInvoiceViewer .iv-foot { display: flex; align-items: center; gap: 8px; padding: 10px 16px; background: #fff; border-top: 1px solid var(--iv-border); flex-wrap: wrap; }
    #nsInvoiceViewer .iv-btn { height: 36px; padding: 0 14px; font-size: 13px; font-weight: 700; border-radius: 6px; display: inline-flex; align-items: center; gap: 7px; background: #fff; color: var(--iv-text); border: 1px solid #cbd5e1; }
    #nsInvoiceViewer .iv-btn:hover:not(:disabled) { background: var(--iv-soft); border-color: #94a3b8; }
    #nsInvoiceViewer .iv-btn:disabled { opacity: .45; }
    #nsInvoiceViewer .iv-btn i { color: var(--iv-muted); }
    #nsInvoiceViewer .iv-btn.is-primary { background: var(--iv-green); border-color: var(--iv-green); color: #fff; }
    #nsInvoiceViewer .iv-btn.is-primary i { color: #fff; }
    #nsInvoiceViewer .iv-btn.is-close { margin-right: auto; }
    #nsInvoiceViewer .iv-print { height: 44px; padding: 0 22px; font-size: 15px; font-weight: 800; border-radius: 8px; border: 0; background: var(--iv-green); color: #fff; display: inline-flex; align-items: center; gap: 10px; }
    #nsInvoiceViewer .iv-print:hover:not(:disabled) { background: #0d7a36; }
    #nsInvoiceViewer .iv-print:disabled { opacity: .7; }
    #nsInvoiceViewer .iv-print.is-done { background: #0d7a36; }
    #nsInvoiceViewer .iv-print kbd { background: rgba(255,255,255,.2); color: #fff; border-radius: 4px; padding: 1px 7px; font-size: 12px; font-family: Arial, sans-serif; box-shadow: none; }
    #nsInvoiceViewer .en-digit { font-family: 'Segoe UI', Arial, sans-serif !important; direction: ltr; unicode-bidi: isolate; font-variant-numeric: tabular-nums; }
    @media (max-width: 900px) {
        #nsInvoiceViewer .iv-body { grid-template-columns: 1fr; overflow: auto; }
        #nsInvoiceViewer .iv-cards { grid-template-columns: 1fr 1fr; }
        #nsInvoiceViewer .modal-content { height: auto; }
    }
</style>
<div class="modal fade" id="nsInvoiceViewer" tabindex="-1" aria-hidden="true" dir="rtl">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content d-flex flex-column">
            <div class="iv-head">
                <h5 class="iv-title" id="ivTitle">فاتورة</h5>
                <span id="ivChips" class="d-flex gap-2"></span>
                <span class="iv-readonly"><i class="fas fa-lock"></i> عرض فقط</span>
                <div class="iv-head-total"><span>صافي الفاتورة</span><b class="en-digit" id="ivHeadTotal">0</b></div>
                <button type="button" class="btn-close btn-close-white ms-0" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="iv-body">
                <div class="iv-main">
                    <div class="iv-cards" id="ivCards"></div>
                    <div id="ivNote"></div>
                    <div class="iv-table-wrap">
                        <table>
                            <thead><tr><th width="40">م</th><th>الصنف</th><th>التصنيف</th><th>الباركود</th><th width="80">الكمية</th><th width="80">المرتجع</th><th width="95">السعر</th><th width="105">الإجمالي</th></tr></thead>
                            <tbody id="ivItems"></tbody>
                        </table>
                    </div>
                </div>
                <aside class="iv-side" id="ivSummary"></aside>
            </div>
            <div class="iv-foot">
                <button type="button" class="iv-print" id="ivBtnPrint"><i class="fas fa-print"></i> <span>طباعة نسخة</span> <kbd>P</kbd></button>
                <button type="button" class="iv-btn" id="ivBtnReturn"><i class="fas fa-rotate-left"></i> مرتجع</button>
                <button type="button" class="iv-btn" id="ivBtnCustomer"><i class="fas fa-id-card"></i> حساب العميل</button>
                <button type="button" class="iv-btn d-none" id="ivBtnCost"><i class="fas fa-calculator"></i> تعديل التكلفة</button>
                <button type="button" class="iv-btn is-close" data-bs-dismiss="modal"><i class="fas fa-xmark"></i> إغلاق (Esc)</button>
            </div>
        </div>
    </div>
</div>
@includeWhen(empty($noReceiptKit), 'partials.receipt-kit')
<script src="/js/nextstore-invoice-viewer.js"></script>
