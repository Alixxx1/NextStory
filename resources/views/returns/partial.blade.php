<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مرتجع مبيعات جزئي | NexStore SaaS</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #119744;
            --dark: #2c3e50;
            --bg-color: #f4f6f9;
        }

        body {
            background-color: var(--bg-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            padding: 15px;
            gap: 15px;
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
        .workspace {
            display: flex; gap: 15px; flex-grow: 1; overflow: hidden;
        }

        .tables-section {
            display: flex; flex-direction: column; flex-grow: 1; gap: 15px; overflow: hidden;
        }

        .grid-box {
            background: white; border-radius: 8px; border: 1px solid #dce1e6;
            display: flex; flex-direction: column; overflow: hidden; flex: 1;
        }

        .grid-title {
            background: var(--dark); color: white; padding: 10px 15px; margin: 0; font-size: 15px; font-weight: bold;
        }
        .grid-title.return-title { background: #dc3545; }

        .table-container { flex-grow: 1; overflow-y: auto; }
        .custom-table { width: 100%; border-collapse: collapse; }
        .custom-table th { background: #f8f9fa; color: #495057; position: sticky; top: 0; padding: 10px; font-size: 13px; text-align: center; border-bottom: 2px solid #dee2e6;}
        .custom-table td { padding: 8px; text-align: center; vertical-align: middle; border-bottom: 1px solid #eee; font-weight: bold; font-size: 14px;}

        .original-item { cursor: pointer; transition: 0.2s; }
        .original-item:hover { background-color: #eaf6ee; }
        .returned-item { background-color: #f8d7da !important; }

        /* الملخص الجانبي */
        .summary-section {
            width: 350px; background: white; border-radius: 8px; border: 1px solid #dce1e6;
            display: flex; flex-direction: column; padding: 20px;
        }

        .summary-box {
            background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px; padding: 15px; margin-bottom: 15px;
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
            <span class="text-muted fw-bold d-block mt-1">العميل: <span class="text-primary" id="return-customer-name">-</span> | رقم الفاتورة: <span class="en-digit text-dark" id="return-invoice-number">-</span></span>
        </div>
        <button class="btn btn-outline-danger fw-bold" id="close-partial-return"><i class="fas fa-times me-1"></i> إغلاق (Esc)</button>
    </div>

    <div class="workspace">

        {{-- قسم الجداول --}}
        <div class="tables-section">

            {{-- 1. جدول الأصناف الأصلية --}}
            <div class="grid-box">
                <h5 class="grid-title"><i class="fas fa-box-open me-2"></i> أصناف الفاتورة (اضغط مرتين على الصنف لارجاعه)</h5>
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
                <h5 class="grid-title return-title"><i class="fas fa-undo-alt me-2"></i> الأصناف المسترجعة</h5>
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
                        <tbody>
                            {{-- يتم الإضافة هنا عن طريق الجافاسكريبت --}}
                        </tbody>
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
                <button class="btn btn-success btn-action shadow-sm" id="btn-save-return"><i class="fas fa-check-circle me-1"></i> تنفيذ المرتجع وطباعة</button>
                <button class="btn btn-secondary btn-action" id="btn-print-updated"><i class="fas fa-print me-1"></i> طباعة الفاتورة المحدثة</button>
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
                    <input type="number" id="modal-return-qty" class="form-control form-control-lg text-center fw-bold en-digit mb-3" min="0.1" step="any">
                    <input type="hidden" id="modal-item-id">
                    <input type="hidden" id="modal-item-price">
                    <button class="btn btn-danger w-100 fw-bold" id="btn-confirm-qty">تأكيد (Enter)</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            const selectedInvoiceStorageKey = 'nexstore-pos-return-invoice';
            const returnParams = new URLSearchParams(window.location.search);
            const requestedReturnId = returnParams.get('return_id') || 'new';
            let invoice;
            let originalTotal = 0;

            try {
                const storedInvoice = sessionStorage.getItem(selectedInvoiceStorageKey);
                invoice = storedInvoice ? JSON.parse(storedInvoice) : null;
                if (!invoice || typeof invoice.number !== 'string' || !Array.isArray(invoice.items) ||
                    typeof invoice.total !== 'number' || !invoice.items.every(item =>
                        item && typeof item.name === 'string' && Number(item.quantity) > 0 &&
                        Number.isFinite(Number(item.price))
                    ) || returnParams.get('invoice') !== invoice.number) {
                    throw new Error('بيانات الفاتورة المختارة غير موجودة أو غير صالحة.');
                }
            } catch (error) {
                console.error('تعذر تحميل الفاتورة للمرتجع الجزئي.', error);
                $('body').prepend(`<div class="alert alert-danger fw-bold mb-0" role="alert">تعذر تحميل الفاتورة: ${error.message} <a href="/" class="alert-link">العودة للأرشيف</a></div>`);
                $('#btn-save-return, #btn-print-updated').prop('disabled', true);
                return;
            }

            originalTotal = invoice.total;
            $('#return-customer-name').text(invoice.customerName || 'عميل نقدي');
            $('#return-invoice-number').text(invoice.number);
            $('#original-invoice-total').text(originalTotal.toFixed(2) + ' ج.م');
            $('#total-after-return').text(originalTotal.toFixed(2) + ' ج.م');

            let returnTransactions = Array.isArray(invoice.returnTransactions)
                ? invoice.returnTransactions.map(transaction => ({ ...transaction, items: Array.isArray(transaction.items) ? transaction.items : [] }))
                : [];
            if (!returnTransactions.length && Array.isArray(invoice.returnedItems) && invoice.returnedItems.length) {
                returnTransactions = [{
                    id: `RET-${invoice.number}-legacy`,
                    items: invoice.returnedItems,
                    total: invoice.returnedItems.reduce((sum, item) => sum + (Number(item.total) || 0), 0),
                    createdAt: invoice.returnedAt || invoice.createdAt,
                    notes: invoice.returnStatus === 'full' ? 'مرتجع كلي' : 'مرتجع جزئي'
                }];
            }
            const editingTransaction = requestedReturnId === 'new'
                ? null
                : returnTransactions.find(transaction => String(transaction.id) === requestedReturnId && !transaction.deleted) || null;
            if (requestedReturnId !== 'new' && !editingTransaction) {
                console.error('تعذر العثور على حركة المرتجع المطلوب تعديلها.', requestedReturnId);
                $('body').prepend('<div class="alert alert-warning fw-bold mb-0" role="alert">لم يتم العثور على حركة المرتجع. يمكنك إضافة مرتجع جديد من سجل المرتجعات.</div>');
            }

            function itemQuantity(items, itemId) {
                return items.reduce((sum, item) => String(item.id) === String(itemId)
                    ? sum + (Number(item.quantity) || 0)
                    : sum, 0);
            }

            const otherTransactions = returnTransactions.filter(transaction => transaction !== editingTransaction && !transaction.deleted);
            invoice.items.forEach((item, index) => {
                const itemId = String(item.id || `item-${index + 1}`);
                const quantity = Number(item.quantity);
                const price = Number(item.price);
                const returnedBeforeEdit = otherTransactions.reduce((sum, transaction) =>
                    sum + itemQuantity(transaction.items, itemId), 0);
                const currentReturnQuantity = editingTransaction ? itemQuantity(editingTransaction.items, itemId) : 0;
                const returnedQuantity = returnedBeforeEdit + currentReturnQuantity;
                const availableForThisReturn = Math.max(0, quantity - returnedBeforeEdit);
                const row = $('<tr>').addClass('original-item')
                    .attr({
                        'data-id': itemId,
                        'data-name': item.name,
                        'data-price': price,
                        'data-max-qty': availableForThisReturn,
                        'data-current-qty': currentReturnQuantity,
                        'data-base-returned': returnedBeforeEdit
                    });
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
                const originalItem = invoice.items.find((invoiceItem, index) =>
                    String(invoiceItem.id || `item-${index + 1}`) === itemId
                );
                if (originalItem && Number(item.quantity) > 0) {
                    addReturnedItemRow(itemId, originalItem.name, Number(originalItem.price), Number(item.quantity));
                }
            });
            calculateTotals();

            // فتح المودال عند الضغط مرتين على الصنف الأصلي
            $('.original-item').on('dblclick', function() {
                let id = $(this).data('id');
                let name = $(this).data('name');
                let price = parseFloat($(this).data('price'));
                let maxQty = parseFloat($(this).data('max-qty'));
                let alreadyInCurrentReturn = parseFloat($('#returnedTable tr').filter(function() {
                    return String($(this).attr('data-return-id')) === String(id);
                }).find('.r-qty').text()) || 0;
                let availableToReturn = maxQty - alreadyInCurrentReturn;

                if(availableToReturn <= 0) return; // تم ارجاع الكمية كلها

                $('#modal-item-id').val(id);
                $('#modal-item-name').text(name);
                $('#modal-item-price').val(price);
                $('#modal-max-qty').text(availableToReturn);
                $('#modal-return-qty').attr('max', availableToReturn).val(1);

                $('#returnQtyModal').modal('show');
                setTimeout(() => $('#modal-return-qty').focus().select(), 300);
            });

            // تأكيد الإرجاع (بالماوس أو الانتر)
            $('#btn-confirm-qty').click(confirmReturn);
            $('#modal-return-qty').keydown(function(e) { if(e.key === 'Enter') confirmReturn(); });

            function confirmReturn() {
                let id = $('#modal-item-id').val();
                let name = $('#modal-item-name').text();
                let price = parseFloat($('#modal-item-price').val());
                let returnQty = parseFloat($('#modal-return-qty').val());
                let maxQty = parseFloat($('#modal-max-qty').text());

                if(returnQty <= 0 || returnQty > maxQty || isNaN(returnQty)) {
                    alert('كمية غير صحيحة!');
                    return;
                }

                // تحديث الجدول الأصلي
                let originalRow = $('.original-item').filter(function() { return String($(this).data('id')) === String(id); });
                let currentReturned = parseFloat(originalRow.find('.returned-qty').text());
                const updatedReturned = currentReturned + returnQty;
                const currentReturnQty = (parseFloat(originalRow.data('current-qty')) || 0) + returnQty;
                originalRow.find('.returned-qty').text(updatedReturned);
                originalRow.data('current-qty', currentReturnQty);
                if(updatedReturned >= parseFloat(originalRow.data('max-qty')) + (parseFloat(originalRow.data('base-returned')) || 0)) {
                    originalRow.css('opacity', '0.5'); // شكل بهتان لو رجع كل الكمية
                }

                addReturnedItemRow(id, name, price, returnQty);

                $('#returnQtyModal').modal('hide');
                calculateTotals();
            }

            function addReturnedItemRow(id, name, price, quantity) {
                let existingReturnRow = $('#returnedTable tr').filter(function() {
                    return String($(this).attr('data-return-id')) === String(id);
                });
                if(existingReturnRow.length) {
                    let updatedQuantity = (parseFloat(existingReturnRow.find('.r-qty').text()) || 0) + quantity;
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

            // حساب الإجماليات
            function calculateTotals() {
                let totalReturn = 0;
                $('.r-total').each(function() {
                    totalReturn += parseFloat($(this).text());
                });

                $('#total-return-value').text(totalReturn.toFixed(2) + ' ج.م');
                $('#total-after-return').text((originalTotal - totalReturn).toFixed(2) + ' ج.م');
            }

            $('#btn-save-return').on('click', function() {
                if (requestedReturnId !== 'new' && !editingTransaction) {
                    window.alert('حركة المرتجع المطلوب تعديلها غير موجودة. ارجع إلى السجل واختر مرتجعًا محفوظًا أو ابدأ مرتجعًا جديدًا.');
                    return;
                }
                const returnedRows = $('#returnedTable tbody tr').map(function() {
                    const row = $(this);
                    return {
                        id: String(row.attr('data-return-id')),
                        quantity: Number(row.find('.r-qty').text()),
                        total: Number(row.find('.r-total').text())
                    };
                }).get();
                if (!returnedRows.length) {
                    window.alert('اختر صنفاً واحداً على الأقل لإرجاعه.');
                    return;
                }

                try {
                    const invoiceStorageKey = 'nexstore-pos-invoices';
                    const invoices = JSON.parse(localStorage.getItem(invoiceStorageKey) || '[]');
                    const savedInvoice = invoices.find(item => item.number === invoice.number);
                    if (!savedInvoice) throw new Error('لم يتم العثور على الفاتورة الأصلية في السجل.');
                    let savedTransactions = Array.isArray(savedInvoice.returnTransactions) && savedInvoice.returnTransactions.length
                        ? savedInvoice.returnTransactions.map(transaction => ({
                            ...transaction,
                            items: Array.isArray(transaction.items) ? transaction.items : []
                        }))
                        : returnTransactions;
                    const transactionId = editingTransaction
                        ? String(editingTransaction.id)
                        : `RET-${invoice.number}-${Date.now()}`;
                    const savedTransaction = {
                        id: transactionId,
                        items: returnedRows,
                        total: returnedRows.reduce((sum, item) => sum + item.total, 0),
                        cashRefund: returnedRows.reduce((sum, item) => sum + item.total, 0),
                        accountCredit: 0,
                        createdAt: new Date().toISOString(),
                        notes: editingTransaction ? (editingTransaction.notes || 'تعديل مرتجع جزئي') : 'مرتجع جزئي',
                        user: editingTransaction ? (editingTransaction.user || 'مدير الفرع') : 'مدير الفرع'
                    };
                    const existingIndex = savedTransactions.findIndex(transaction => String(transaction.id) === transactionId);
                    if (existingIndex === -1) savedTransactions.push(savedTransaction);
                    else savedTransactions[existingIndex] = savedTransaction;

                    const aggregateItems = new Map();
                    savedTransactions.forEach(transaction => {
                        if (transaction.deleted) return;
                        (transaction.items || []).forEach(item => {
                            const current = aggregateItems.get(String(item.id)) || 0;
                            aggregateItems.set(String(item.id), current + (Number(item.quantity) || 0));
                        });
                    });
                    savedInvoice.returnTransactions = savedTransactions;
                    savedInvoice.returnedItems = Array.from(aggregateItems.entries()).map(([id, quantity]) => {
                        const originalItem = invoice.items.find((item, index) =>
                            String(item.id || `item-${index + 1}`) === id
                        );
                        const price = Number(originalItem && originalItem.price) || 0;
                        return { id, quantity, total: quantity * price };
                    });
                    const allItemsReturned = invoice.items.every((item, index) => {
                        const itemId = String(item.id || `item-${index + 1}`);
                        const returned = savedInvoice.returnedItems.find(returnItem => returnItem.id === itemId);
                        return returned && returned.quantity >= Number(item.quantity);
                    });
                    savedInvoice.returnStatus = allItemsReturned ? 'full' : 'partial';
                    savedInvoice.returnedAt = new Date().toISOString();
                    savedInvoice.returnedTotal = savedInvoice.returnedItems.reduce((sum, item) => sum + item.total, 0);
                    localStorage.setItem(invoiceStorageKey, JSON.stringify(invoices));
                    invoice.returnTransactions = savedTransactions;
                    invoice.returnedItems = savedInvoice.returnedItems;
                    invoice.returnStatus = savedInvoice.returnStatus;
                    window.alert(editingTransaction ? 'تم تحديث بيانات المرتجع بنجاح.' : 'تم تسجيل المرتجع الجديد بنجاح.');
                    if (window.confirm('هل تريد طباعة فاتورة المرتجع؟')) window.print();
                } catch (error) {
                    console.error('تعذر حفظ المرتجع الجزئي.', error);
                    window.alert(`تعذر حفظ المرتجع الجزئي: ${error.message}`);
                }
            });

            $('#btn-print-updated').on('click', () => window.print());

            function returnToPreviousPage() {
                if (window.history.length > 1) window.history.back();
                else window.location.href = '/';
            }

            $('#close-partial-return').on('click', returnToPreviousPage);
            document.addEventListener('keydown', event => {
                if (event.key === 'Escape') returnToPreviousPage();
            });
        });
    </script>
</body>
</html>
