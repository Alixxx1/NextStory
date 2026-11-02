<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>مرتجع مبيعات جزئي | NextStore SaaS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root { --primary: #119744; --dark: #2c3e50; --bg-color: #f4f6f9; }
        body {
            background-color: var(--bg-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh; overflow: hidden; display: flex; flex-direction: column; padding: 15px; gap: 15px;
        }
        .en-digit { font-family: Arial, sans-serif; direction: ltr; font-weight: bold; }

        /* الهيدر */
        .page-header {
            background: white; border-radius: 8px; padding: 15px 20px;
            display: flex; justify-content: space-between; align-items: center;
            border: 1px solid #dce1e6; box-shadow: 0 2px 5px rgba(0,0,0,0.02);
            border-right: 4px solid var(--primary);
        }

        /* مساحة العمل (نصفين) */
        .workspace { display: flex; gap: 15px; flex-grow: 1; overflow: hidden; }
        .tables-section { display: flex; flex-direction: column; flex-grow: 1; gap: 15px; overflow: hidden; }

        .grid-box {
            background: white; border-radius: 8px; border: 1px solid #dce1e6;
            display: flex; flex-direction: column; overflow: hidden; flex: 1;
        }
        .grid-title {
            background: var(--dark); color: white; padding: 10px 15px;
            margin: 0; font-size: 15px; font-weight: bold;
        }
        .grid-title.return-title { background: #dc3545; }

        .table-container { flex-grow: 1; overflow-y: auto; }
        .custom-table { width: 100%; border-collapse: collapse; }
        .custom-table th {
            background: #f8f9fa; color: #495057; position: sticky; top: 0;
            padding: 10px; font-size: 13px; text-align: center; border-bottom: 2px solid #dee2e6;
        }
        .custom-table td {
            padding: 8px; text-align: center; vertical-align: middle;
            border-bottom: 1px solid #eee; font-weight: bold; font-size: 14px;
        }
        .original-item { cursor: pointer; transition: 0.2s; }
        .original-item:hover { background-color: #eaf6ee; }
        .returned-item { background-color: #f8d7da !important; cursor: pointer; }

        /* الملخص الجانبي */
        .summary-section {
            width: 350px; background: white; border-radius: 8px; border: 1px solid #dce1e6;
            display: flex; flex-direction: column; padding: 20px;
        }
        .summary-box {
            background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px;
            padding: 15px; margin-bottom: 15px;
        }
        .summary-box label { font-size: 14px; color: #6c757d; font-weight: bold; display: block; margin-bottom: 5px;}
        .summary-box .value { font-size: 24px; color: var(--dark); font-weight: 900; font-family: monospace;}

        .return-value-box { background: #fff3cd; border-color: #ffe69c; }
        .return-value-box .value { color: #dc3545; }

        .btn-action { width: 100%; font-weight: bold; font-size: 16px; padding: 12px; border-radius: 6px; margin-bottom: 10px;}
    </style>
</head>
<body>

    <div class="page-header">
        <div>
            <h4 class="m-0 fw-bold"><i class="fas fa-reply text-danger me-2"></i> مرتجع جزئي للفاتورة</h4>
            <span class="text-muted fw-bold d-block mt-1">
                العميل: <span class="text-primary" id="return-customer-name">-</span> |
                رقم الفاتورة: <span class="en-digit text-dark" id="return-invoice-number">-</span>
            </span>
        </div>
        <button class="btn btn-outline-danger fw-bold" id="close-partial-return"><i class="fas fa-times me-1"></i> إغلاق (Esc)</button>
    </div>

    <div class="workspace">
        {{-- قسم الجداول --}}
        <div class="tables-section">
            {{-- 1. جدول الأصناف الأصلية --}}
            <div class="grid-box">
                <h5 class="grid-title"><i class="fas fa-box-open me-2"></i> أصناف الفاتورة (اضغط مرتين على الصنف لإرجاعه)</h5>
                <div class="table-container">
                    <table class="custom-table" id="originalTable">
                        <thead>
                            <tr>
                                <th width="40%">اسم الصنف</th>
                                <th width="15%">سعر الوحدة</th>
                                <th width="15%">الكمية المباعة</th>
                                <th width="15%">الكمية المرتجعة</th>
                                <th width="15%">الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            {{-- 2. جدول المرتجعات --}}
            <div class="grid-box">
                <h5 class="grid-title return-title"><i class="fas fa-undo-alt me-2"></i> الأصناف المسترجعة (اضغط مرتين على السطر لإلغائه من المرتجع الحالي)</h5>
                <div class="table-container">
                    <table class="custom-table" id="returnedTable">
                        <thead>
                            <tr>
                                <th width="40%">اسم الصنف</th>
                                <th width="20%">سعر الوحدة</th>
                                <th width="20%">الكمية المسترجعة</th>
                                <th width="20%">قيمة المرتجع</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- قسم الملخص --}}
        <div class="summary-section">
            <div class="summary-box">
                <label>قيمة الفاتورة الأصلية:</label>
                <div class="value en-digit" id="original-invoice-total">0.00 ج.م</div>
            </div>

            <div class="summary-box return-value-box shadow-sm">
                <label>قيمة المرتجع الحالية:</label>
                <div class="value en-digit" id="total-return-value">0.00 ج.م</div>
            </div>

            <div class="summary-box">
                <label>قيمة الفاتورة بعد المرتجع:</label>
                <div class="value en-digit text-success" id="total-after-return">0.00 ج.م</div>
            </div>

            <div class="mt-auto">
                <button class="btn btn-success btn-action shadow-sm" id="btn-save-return">
                    <i class="fas fa-check-circle me-1"></i> تنفيذ المرتجع وطباعة
                </button>
                <button class="btn btn-secondary btn-action" id="btn-print-updated">
                    <i class="fas fa-print me-1"></i> طباعة الفاتورة المحدثة
                </button>
            </div>
        </div>
    </div>

    {{-- مودال إدخال كمية المرتجع --}}
    <div class="modal fade" id="returnQtyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white py-2">
                    <h6 class="modal-title fw-bold">إرجاع صنف</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <h6 id="modal-item-name" class="fw-bold mb-3">اسم الصنف</h6>
                    <label class="form-label text-muted">الكمية المراد إرجاعها (بحد أقصى <span id="modal-max-qty"></span>):</label>
                    <input type="number" id="modal-return-qty" class="form-control form-control-lg text-center fw-bold en-digit mb-3" min="0.001" step="any">
                    <input type="hidden" id="modal-item-id">
                    <input type="hidden" id="modal-item-price">
                    <button class="btn btn-danger w-100 fw-bold" id="btn-confirm-qty">تأكيد (Enter)</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    $(document).ready(function() {
        'use strict';

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const returnParams = new URLSearchParams(window.location.search);
        const requestedInvoiceNo = returnParams.get('invoice') || '';
        const requestedReturnId = returnParams.get('return_id') || 'new';

        let invoice = null;
        let originalTotal = 0;
        let editingTransaction = null;

        function itemQuantity(items, itemId) {
            return (items || []).reduce((sum, item) => String(item.id) === String(itemId) ? sum + (Number(item.quantity) || 0) : sum, 0);
        }

        // 1. جلب بيانات الفاتورة من قاعدة البيانات الحقيقية
        async function loadInvoiceFromDatabase() {
            if (!requestedInvoiceNo) {
                showFatalError('يرجى تحديد رقم الفاتورة المطلوب إرجاعها.');
                return;
            }

            try {
                const res = await fetch(`/api/returns/invoice-details?invoice=${encodeURIComponent(requestedInvoiceNo)}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const out = await res.json().catch(() => ({}));
                if (!res.ok || !out.invoice) {
                    throw new Error(out.message || 'تعذر تحميل الفاتورة من قاعدة البيانات.');
                }

                invoice = out.invoice;
                initializeReturnScreen();
            } catch (error) {
                console.error('تعذر تحميل الفاتورة للمرتجع الجزئي:', error);
                showFatalError(error.message);
            }
        }

        function showFatalError(message) {
            $('body').prepend(`
                <div class="alert alert-danger fw-bold mb-0 d-flex justify-content-between align-items-center" role="alert">
                    <span>تعذر تحميل الفاتورة: ${$('<div>').text(message).html()}</span>
                    <a href="/orders/archive" class="btn btn-sm btn-outline-danger fw-bold">العودة للأرشيف</a>
                </div>
            `);
            $('#btn-save-return, #btn-print-updated').prop('disabled', true);
        }

        function initializeReturnScreen() {
            originalTotal = Number(invoice.total) || 0;
            $('#return-customer-name').text(invoice.customerName || 'عميل نقدي');
            $('#return-invoice-number').text(invoice.number);
            $('#original-invoice-total').text(originalTotal.toFixed(2) + ' ج.م');
            $('#total-after-return').text(originalTotal.toFixed(2) + ' ج.م');

            const returnTransactions = Array.isArray(invoice.returnTransactions) ? invoice.returnTransactions : [];
            editingTransaction = requestedReturnId === 'new'
                ? null
                : returnTransactions.find(t => (String(t.id) === String(requestedReturnId) || String(t.dbId) === String(requestedReturnId)) && !t.deleted) || null;

            if (requestedReturnId !== 'new' && !editingTransaction) {
                $('body').prepend('<div class="alert alert-warning fw-bold mb-0" role="alert">لم يتم العثور على حركة المرتجع المطلوبة. يمكنك تسجيل مرتجع جديد الآن.</div>');
            }

            const otherTransactions = returnTransactions.filter(t => t !== editingTransaction && !t.deleted);

            $('#originalTable tbody').empty();
            $('#returnedTable tbody').empty();

            invoice.items.forEach((item, index) => {
                const itemId = String(item.id || `item-${index + 1}`);
                const quantity = Number(item.quantity) || 0;
                const price = Number(item.price) || 0;
                const returnedBeforeEdit = otherTransactions.reduce((sum, t) => sum + itemQuantity(t.items, itemId), 0);
                const currentReturnQuantity = editingTransaction ? itemQuantity(editingTransaction.items, itemId) : 0;
                const returnedQuantity = returnedBeforeEdit + currentReturnQuantity;
                const availableForThisReturn = Math.max(0, Number((quantity - returnedBeforeEdit).toFixed(3)));

                const row = $('<tr>').addClass('original-item')
                    .attr({
                        'data-id': itemId,
                        'data-name': item.name,
                        'data-price': price,
                        'data-max-qty': availableForThisReturn,
                        'data-current-qty': currentReturnQuantity,
                        'data-base-returned': returnedBeforeEdit
                    });

                if (returnedQuantity >= quantity) {
                    row.css('opacity', '0.5');
                }

                row.append($('<td>').text(item.name));
                row.append($('<td>').addClass('en-digit').text(price.toFixed(2)));
                row.append($('<td>').addClass('en-digit max-qty').text(quantity));
                row.append($('<td>').addClass('en-digit text-danger returned-qty').text(returnedQuantity));
                row.append($('<td>').addClass('en-digit').text((Number(item.total) || quantity * price).toFixed(2)));

                $('#originalTable tbody').append(row);
            });

            const selectedReturnItems = editingTransaction ? editingTransaction.items : [];
            selectedReturnItems.forEach(item => {
                const itemId = String(item.id);
                const originalItem = invoice.items.find((invItem, idx) => String(invItem.id || `item-${idx + 1}`) === itemId);
                if (originalItem && Number(item.quantity) > 0) {
                    addReturnedItemRow(itemId, originalItem.name, Number(originalItem.price), Number(item.quantity));
                }
            });

            calculateTotals();
        }

        // فتح المودال عند الضغط مرتين على الصنف الأصلي
        $(document).on('dblclick', '.original-item', function() {
            let id = $(this).attr('data-id');
            let name = $(this).attr('data-name');
            let price = parseFloat($(this).attr('data-price'));
            let maxQty = parseFloat($(this).attr('data-max-qty'));
            let alreadyInCurrentReturn = parseFloat($('#returnedTable tr').filter(function() {
                return String($(this).attr('data-return-id')) === String(id);
            }).find('.r-qty').text()) || 0;
            let availableToReturn = Number((maxQty - alreadyInCurrentReturn).toFixed(3));

            if (availableToReturn <= 0) {
                Swal.fire({ toast: true, position: 'top-start', icon: 'info', title: 'تم إرجاع كامل كمية هذا الصنف بالفعل', showConfirmButton: false, timer: 1800 });
                return;
            }

            $('#modal-item-id').val(id);
            $('#modal-item-name').text(name);
            $('#modal-item-price').val(price);
            $('#modal-max-qty').text(availableToReturn);
            $('#modal-return-qty').attr('max', availableToReturn).val(Math.min(1, availableToReturn));

            $('#returnQtyModal').modal('show');
            setTimeout(() => $('#modal-return-qty').trigger('focus').trigger('select'), 250);
        });

        // إلغاء صنف من جدول المرتجعات الحالية عند الضغط عليه مرتين
        $(document).on('dblclick', '#returnedTable tbody tr.returned-item', function() {
            const id = String($(this).attr('data-return-id'));
            const removedQty = parseFloat($(this).find('.r-qty').text()) || 0;
            $(this).remove();

            const originalRow = $('.original-item').filter(function() {
                return String($(this).attr('data-id')) === id;
            });

            if (originalRow.length) {
                const baseRet = parseFloat(originalRow.attr('data-base-returned')) || 0;
                originalRow.find('.returned-qty').text(baseRet);
                originalRow.attr('data-current-qty', 0);
                originalRow.css('opacity', '1');
            }
            calculateTotals();
        });

        $('#btn-confirm-qty').on('click', confirmReturn);
        $('#modal-return-qty').on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                confirmReturn();
            }
        });

        function confirmReturn() {
            let id = $('#modal-item-id').val();
            let name = $('#modal-item-name').text();
            let price = parseFloat($('#modal-item-price').val());
            let returnQty = parseFloat($('#modal-return-qty').val());
            let maxQty = parseFloat($('#modal-max-qty').text());

            if (returnQty <= 0 || returnQty > (maxQty + 0.0001) || isNaN(returnQty)) {
                Swal.fire({ icon: 'warning', title: 'كمية غير صحيحة', text: `أقصى كمية متاحة للإرجاع هي (${maxQty})`, confirmButtonColor: '#dc3545' });
                return;
            }

            let originalRow = $('.original-item').filter(function() {
                return String($(this).attr('data-id')) === String(id);
            });
            let currentReturned = parseFloat(originalRow.find('.returned-qty').text()) || 0;
            const updatedReturned = Number((currentReturned + returnQty).toFixed(3));
            const currentReturnQty = Number(((parseFloat(originalRow.attr('data-current-qty')) || 0) + returnQty).toFixed(3));

            originalRow.find('.returned-qty').text(updatedReturned);
            originalRow.attr('data-current-qty', currentReturnQty);

            if (updatedReturned >= (parseFloat(originalRow.attr('data-max-qty')) + (parseFloat(originalRow.attr('data-base-returned')) || 0))) {
                originalRow.css('opacity', '0.5');
            }

            addReturnedItemRow(id, name, price, returnQty);
            $('#returnQtyModal').modal('hide');
            calculateTotals();
        }

        function addReturnedItemRow(id, name, price, quantity) {
            let existingReturnRow = $('#returnedTable tr').filter(function() {
                return String($(this).attr('data-return-id')) === String(id);
            });
            if (existingReturnRow.length) {
                let updatedQuantity = Number(((parseFloat(existingReturnRow.find('.r-qty').text()) || 0) + quantity).toFixed(3));
                existingReturnRow.find('.r-qty').text(updatedQuantity);
                existingReturnRow.find('.r-total').text((updatedQuantity * price).toFixed(2));
                return;
            }

            const row = $('<tr>').addClass('returned-item').attr('data-return-id', id);
            row.append($('<td>').text(name));
            row.append($('<td>').addClass('en-digit').text(price.toFixed(2)));
            row.append($('<td>').addClass('en-digit r-qty').text(quantity));
            row.append($('<td>').addClass('en-digit fw-bold text-danger r-total').text((quantity * price).toFixed(2)));
            $('#returnedTable tbody').append(row);
        }

        function calculateTotals() {
            let totalReturn = 0;
            $('.r-total').each(function() {
                totalReturn += parseFloat($(this).text()) || 0;
            });

            $('#total-return-value').text(totalReturn.toFixed(2) + ' ج.م');
            $('#total-after-return').text(Math.max(0, originalTotal - totalReturn).toFixed(2) + ' ج.م');
        }

        // 2. حفظ المرتجع في قاعدة البيانات الحقيقية
        $('#btn-save-return').on('click', async function() {
            if (!invoice) return;

            const returnedRows = $('#returnedTable tbody tr').map(function() {
                const row = $(this);
                return {
                    id: String(row.attr('data-return-id')),
                    quantity: Number(row.find('.r-qty').text()),
                    total: Number(row.find('.r-total').text())
                };
            }).get();

            if (!returnedRows.length) {
                Swal.fire({ icon: 'warning', title: 'تنبيه', text: 'يرجى اختيار صنف واحد على الأقل لإرجاعه (اضغط مرتين على الصنف من الجدول العلوي).', confirmButtonColor: '#119744' });
                return;
            }

            try {
                $('#btn-save-return').prop('disabled', true);
                const res = await fetch('/api/returns/partial/save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        invoiceNumber: invoice.number,
                        returnId: editingTransaction ? editingTransaction.id : 'new',
                        items: returnedRows
                    })
                });

                const out = await res.json().catch(() => ({}));
                if (!res.ok) {
                    throw new Error(out.message || 'تعذر حفظ المرتجع في قاعدة البيانات.');
                }

                invoice = out.invoice;

                const printConfirm = await Swal.fire({
                    icon: 'success',
                    title: editingTransaction ? 'تم تحديث بيانات المرتجع بنجاح' : `تم تسجيل المرتجع (${out.returnId}) وإعادة الكميات للمخزن`,
                    text: 'هل تريد طباعة إشعار المرتجع الآن؟',
                    showCancelButton: true,
                    confirmButtonColor: '#119744',
                    confirmButtonText: 'نعم، طباعة',
                    cancelButtonText: 'العودة للسجل'
                });

                if (printConfirm.isConfirmed) {
                    window.print();
                }

                returnToPreviousPage();
            } catch (error) {
                console.error('تعذر حفظ المرتجع الجزئي:', error);
                Swal.fire({ icon: 'error', title: 'خطأ في الحفظ', text: error.message, confirmButtonColor: '#dc3545' });
            } finally {
                $('#btn-save-return').prop('disabled', false);
            }
        });

        $('#btn-print-updated').on('click', () => window.print());

        function returnToPreviousPage() {
            if (window.history.length > 1 && document.referrer) {
                window.history.back();
            } else {
                window.location.href = '/orders/archive';
            }
        }

        $('#close-partial-return').on('click', returnToPreviousPage);

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape' && !$('#returnQtyModal').hasClass('show') && !Swal.isVisible()) {
                returnToPreviousPage();
            }
        });

        loadInvoiceFromDatabase();
    });
    </script>
</body>
</html>
