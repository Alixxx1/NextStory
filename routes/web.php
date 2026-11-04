<?php

use Illuminate\Support\Facades\Route;

// استدعاء الكنترولرات الفعلية
use App\Http\Controllers\POS\PosSalesController;
use App\Http\Controllers\Inventory\InventoryItemController;
use App\Http\Controllers\Inventory\InventorySubController;
use App\Http\Controllers\Customers\CustomerController;
use App\Http\Controllers\Customers\IndexController;
use App\Http\Controllers\Customers\ShowController;
use App\Http\Controllers\Orders\OrderArchiveController;
use App\Http\Controllers\Returns\SalesReturnController;
use App\Http\Controllers\Purchases\PurchaseController;
use App\Http\Controllers\Suppliers\SupplierController;
use App\Http\Controllers\Expenses\ExpenseController;
use App\Http\Controllers\Reports\WorkReportController;
use App\Http\Controllers\Employees\EmployeeController;

/*
|--------------------------------------------------------------------------
| Web Routes - NextStore SaaS
|--------------------------------------------------------------------------
*/

// ================= الواجهة الرئيسية للبرنامج =================
Route::get('/', function () {
    return view('pos.archive');
})->name('home');


// ================= 1. قسم الكاشير والمبيعات (POS) =================
Route::prefix('pos')->group(function () {
    Route::get('/archive', function () {
        return view('pos.archive');
    })->name('pos.archive');

    Route::get('/sales', [PosSalesController::class, 'index'])->name('pos.sales');
    Route::get('/returns/partial', [SalesReturnController::class, 'partialView'])->name('pos.returns.partial');
});

// مسارات الـ API الخاصة بشاشة البيع (POS Sales)
Route::prefix('api/pos')->group(function () {
    Route::get('/bootstrap', [PosSalesController::class, 'bootstrapData']);
    Route::post('/zones/save', [PosSalesController::class, 'saveZone']);
    Route::post('/customers/save', [PosSalesController::class, 'saveCustomer']);
    Route::post('/invoices/store', [PosSalesController::class, 'storeInvoice']);
});


// ================= 2. قسم المخازن والتصنيع (Inventory) =================
Route::prefix('inventory')->group(function () {
    // شاشة أصناف المخزن الرئيسية
    Route::get('/items', [InventoryItemController::class, 'index'])->name('inventory.items');
    Route::get('/api/pos-items', [InventoryItemController::class, 'posItemsApi']);

    // الشاشات الفرعية المربوطة بقاعدة البيانات (التصنيفات، الوحدات، حركة الأصناف، التصنيع وسجله)
    Route::get('/categories', [InventorySubController::class, 'categoriesView'])->name('inventory.categories');
    Route::get('/units', [InventorySubController::class, 'unitsView'])->name('inventory.units');
    Route::get('/movement', [InventorySubController::class, 'movementView'])->name('inventory.movement');
    Route::get('/manufacturing', [InventorySubController::class, 'manufacturingView'])->name('inventory.manufacturing');
    Route::get('/manufacturing-log', [InventorySubController::class, 'manufacturingLogView'])->name('inventory.manufacturing.log');

    // باقي شاشات المخزن الفرعية (تُربط لاحقاً)
    Route::get('/adjustments', fn() => view('inventory.adjustments'));
    Route::get('/adjustments-log', fn() => view('inventory.adjustments-log'));
    Route::get('/adjustments-movement', fn() => view('inventory.adjustments-movement'));

    Route::get('/orders', fn() => view('inventory.orders'));
    Route::get('/orders-log', fn() => view('inventory.orders-log'));
    Route::get('/orders-movement', fn() => view('inventory.orders-log'));

    Route::get('/permissions', fn() => view('inventory.permissions'));
    Route::get('/permissions-log', fn() => view('inventory.permissions-log'));
    Route::get('/permissions-movement', fn() => view('inventory.permissions-log'));

      Route::get('/adjustments', [InventorySubController::class, 'adjustmentsView'])->name('inventory.adjustments');
    Route::get('/adjustments-log', [InventorySubController::class, 'adjustmentsLogView'])->name('inventory.adjustments.log');
    Route::get('/adjustments-movement', [InventorySubController::class, 'adjustmentsMovementView'])->name('inventory.adjustments.movement');

     Route::get('/orders', [InventorySubController::class, 'ordersView'])->name('inventory.orders');
    Route::get('/orders-log', [InventorySubController::class, 'ordersLogView'])->name('inventory.orders.log');
    Route::get('/orders-movement', [InventorySubController::class, 'ordersLogView'])->name('inventory.orders.movement');


     Route::get('/permissions', [InventorySubController::class, 'permissionsView'])->name('inventory.permissions');
    Route::get('/permissions-log', [InventorySubController::class, 'permissionsLogView'])->name('inventory.permissions.log');
    Route::get('/permissions-movement', [InventorySubController::class, 'permissionsLogView'])->name('inventory.permissions.movement');



});

// مسارات الـ API الخاصة بالمخزن والتصنيفات والوحدات وحركة الأصناف والتصنيع
Route::prefix('api/inventory')->group(function () {
    // 1) API أصناف المخزن
    Route::post('/items/save', [InventoryItemController::class, 'saveItem']);
    Route::post('/categories/quick', [InventoryItemController::class, 'quickCategory']);
    Route::post('/items/batch', [InventoryItemController::class, 'batchAction']);
    Route::get('/pos-items', [InventoryItemController::class, 'posItemsApi']);

    // 2) API التصنيفات
    Route::get('/categories/list', [InventorySubController::class, 'categoriesListApi']);
    Route::post('/categories/save', [InventorySubController::class, 'saveCategory']);
    Route::post('/categories/{id}/toggle-delete', [InventorySubController::class, 'toggleDeleteCategory']);

    // 3) API وحدات القياس
    Route::get('/units/list', [InventorySubController::class, 'unitsListApi']);
    Route::post('/units/save', [InventorySubController::class, 'saveUnit']);
    Route::delete('/units/{id}', [InventorySubController::class, 'deleteUnit']);
    Route::post('/units/reset-defaults', [InventorySubController::class, 'resetDefaultUnits']);

    // 4) API تقرير حركة الأصناف
    Route::get('/movements/report', [InventorySubController::class, 'movementsReportApi']);

    // 5) API التصنيع والعروض التجميعية وسجلها
    Route::get('/manufacturing/bootstrap', [InventorySubController::class, 'manufacturingBootstrapApi']);
    Route::post('/manufacturing/save', [InventorySubController::class, 'saveManufacturingBatch']);
    Route::delete('/manufacturing/{id}', [InventorySubController::class, 'deleteManufacturingBatch']);
    Route::post('/manufacturing/item-quick-update', [InventorySubController::class, 'quickUpdateItemFromManufacturing']);

    Route::get('/adjustments/bootstrap', [InventorySubController::class, 'adjustmentsBootstrapApi']);
    Route::post('/adjustments/save', [InventorySubController::class, 'saveAdjustment']);
    Route::post('/adjustments/{id}/toggle-delete', [InventorySubController::class, 'toggleDeleteAdjustment']);
    Route::post('/adjustments/zero-all-stock', [InventorySubController::class, 'zeroAllStock']);

    
     Route::get('/orders/bootstrap', [InventorySubController::class, 'ordersBootstrapApi']);
    Route::post('/orders/save', [InventorySubController::class, 'saveWarehouseOrder']);
    Route::post('/orders/{id}/toggle-delete', [InventorySubController::class, 'toggleDeleteWarehouseOrder']);
    Route::post('/orders/quick-item', [InventorySubController::class, 'quickCreateOrderItem']);


     Route::get('/permissions/bootstrap', [InventorySubController::class, 'permissionsBootstrapApi']);
    Route::post('/permissions/save', [InventorySubController::class, 'saveAdminPermission']);
    Route::post('/permissions/{id}/toggle-delete', [InventorySubController::class, 'toggleDeleteAdminPermission']);
});


// ================= 3. قسم العملاء ومناطق التوصيل (Customers) =================
Route::prefix('customers')->group(function () {
    Route::get('/', [IndexController::class, 'index'])->name('customers.index');
    Route::get('/show', [ShowController::class, 'show'])->name('customers.show');
    Route::get('/delivery-areas', [CustomerController::class, 'deliveryAreas'])->name('customers.delivery_areas');
    Route::get('/payment', [ShowController::class, 'paymentView'])->name('customers.payment');
    Route::get('/payments-report', [ShowController::class, 'paymentsReportView'])->name('customers.payments_report');
});

// مسارات الـ API الموحدة للعملاء ومناطق التوصيل وحساب العميل وسندات القبض
Route::prefix('api/customers')->name('api.customers.')->group(function () {
    // أ) API مناطق التوصيل (CustomerController)
    Route::get('/delivery-zones', [CustomerController::class, 'getDeliveryZones'])->name('zones.index');
    Route::post('/delivery-zones', [CustomerController::class, 'storeDeliveryZone'])->name('zones.store');
    Route::put('/delivery-zones/{id}', [CustomerController::class, 'updateDeliveryZone'])->name('zones.update');
    Route::delete('/delivery-zones/{id}', [CustomerController::class, 'destroyDeliveryZone'])->name('zones.destroy');

    // ب) API قائمة العملاء (IndexController)
    Route::get('/init-data', [IndexController::class, 'getInitData']);
    Route::post('/save', [IndexController::class, 'saveCustomer']);
    Route::post('/{id}/toggle-delete', [IndexController::class, 'toggleDeleteCustomer']);
    Route::post('/zones/quick', [IndexController::class, 'quickCreateZone']);

    // ج) API شاشات سند القبض وتقرير دفعات العملاء (ShowController)
    Route::get('/payments/bootstrap', [ShowController::class, 'paymentsBootstrap']);
    Route::post('/payments/save', [ShowController::class, 'savePaymentVoucher']);
    Route::delete('/payments/{id}', [ShowController::class, 'deletePaymentVoucher']);

    // د) API شاشة حساب العميل وكشف الحساب (ShowController)
    Route::get('/search-list', [ShowController::class, 'getCustomersList']);
    Route::get('/{id}/account-details', [ShowController::class, 'getAccountDetails']);
    Route::post('/{id}/quick-update', [ShowController::class, 'updateCustomerQuick']);
    Route::post('/{id}/transactions', [ShowController::class, 'storeTransaction']);
    Route::post('/{id}/notes', [ShowController::class, 'storeNote']);
});


// ================= 4. قسم أرشيف الطلبات (Orders Archive) =================
Route::prefix('orders')->group(function () {
    Route::get('/archive', [OrderArchiveController::class, 'index'])->name('orders.archive');
});

// مسارات الـ API لأرشيف الطلبات (/api/orders/...)
Route::prefix('api/orders')->name('api.orders.')->group(function () {
    Route::get('/archive', [OrderArchiveController::class, 'getOrders'])->name('index');
    Route::patch('/{id}/cost', [OrderArchiveController::class, 'updateCost'])->name('update_cost');
    Route::delete('/{id}', [OrderArchiveController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/restore', [OrderArchiveController::class, 'restore'])->name('restore');
});


// ================= 5. قسم المرتجعات (Returns) =================
Route::prefix('returns')->group(function () {
    Route::get('/log', [SalesReturnController::class, 'logView'])->name('returns.log');
    Route::get('/suppliers', [PurchaseController::class, 'supplierReturnsIndex'])->name('returns.suppliers');
    Route::get('/suppliers/create', [PurchaseController::class, 'supplierReturnCreate'])->name('returns.suppliers.create');
});

// مسارات الـ API لمرتجعات المبيعات (/api/returns/...)
Route::prefix('api/returns')->name('api.returns.')->group(function () {
    Route::get('/log', [SalesReturnController::class, 'getReturnsLog'])->name('log');
    Route::delete('/{returnId}', [SalesReturnController::class, 'deleteReturn'])->name('delete');
    Route::get('/invoice-details', [SalesReturnController::class, 'getInvoiceForReturn'])->name('invoice_details');
    Route::post('/partial/save', [SalesReturnController::class, 'savePartialReturn'])->name('partial_save');
});


// ================= 6. قسم المشتريات (Purchases) =================
Route::prefix('purchases')->name('purchases.')->group(function () {
    Route::get('/create', [PurchaseController::class, 'create'])->name('create');
    Route::get('/log', [PurchaseController::class, 'index'])->name('log');
    Route::get('/index', [PurchaseController::class, 'index'])->name('index');
});

// مسارات الـ API الخاصة بالمشتريات ومرتجعات الموردين (/api/purchases/...)
Route::prefix('api/purchases')->name('api.purchases.')->group(function () {
    Route::get('/bootstrap', [PurchaseController::class, 'bootstrapData'])->name('bootstrap');
    Route::get('/log', [PurchaseController::class, 'getInvoicesLog'])->name('log');
    Route::post('/save', [PurchaseController::class, 'saveInvoice'])->name('save');
    Route::delete('/{id}', [PurchaseController::class, 'deleteInvoice'])->name('delete');

    // مسارات مرتجعات المشتريات للموردين
    Route::get('/returns/bootstrap', [PurchaseController::class, 'supplierReturnBootstrapApi'])->name('returns.bootstrap');
    Route::get('/returns/log', [PurchaseController::class, 'getSupplierReturnsLogApi'])->name('returns.log');
    Route::post('/returns/save', [PurchaseController::class, 'saveSupplierReturn'])->name('returns.save');
    Route::delete('/returns/{id}', [PurchaseController::class, 'deleteSupplierReturn'])->name('returns.delete');
});


// ================= 7. قسم الموردين (Suppliers) =================
Route::prefix('suppliers')->name('suppliers.')->group(function () {
    Route::get('/', [SupplierController::class, 'index'])->name('index');
    Route::get('/show', [SupplierController::class, 'show'])->name('show');
    Route::get('/payments', [SupplierController::class, 'payments'])->name('payments');
});

// مسارات الـ API الخاصة بالموردين وكشف حسابهم وتقرير الدفعات (/api/suppliers/...)
Route::prefix('api/suppliers')->name('api.suppliers.')->group(function () {
    Route::get('/list', [SupplierController::class, 'listApi'])->name('list');
    Route::post('/save', [SupplierController::class, 'save'])->name('save');
    Route::delete('/{id}', [SupplierController::class, 'destroy'])->name('delete');

    // مسارات شاشة حساب المورد وتقرير الدفعات
    Route::get('/show-bootstrap', [SupplierController::class, 'showBootstrapApi'])->name('show.bootstrap');
    Route::get('/payments-report', [SupplierController::class, 'paymentsReportApi'])->name('payments.report');
    Route::post('/transactions/save', [SupplierController::class, 'saveTransaction'])->name('transactions.save');
    Route::delete('/transactions/{id}', [SupplierController::class, 'deleteTransaction'])->name('transactions.delete');
});


// ================= 8. قسم المصروفات (Expenses) =================
Route::prefix('expenses')->name('expenses.')->group(function () {
    Route::get('/', [ExpenseController::class, 'index'])->name('index');
    Route::get('/reports', [ExpenseController::class, 'reports'])->name('reports');
});

// مسارات الـ API الخاصة بالمصروفات وشجرة البنود (/api/expenses/...)
Route::prefix('api/expenses')->name('api.expenses.')->group(function () {
    Route::get('/bootstrap', [ExpenseController::class, 'bootstrapApi'])->name('bootstrap');
    Route::post('/tree/save', [ExpenseController::class, 'saveTree'])->name('tree.save');
    Route::post('/save', [ExpenseController::class, 'saveExpense'])->name('save');
    Route::delete('/{id}', [ExpenseController::class, 'destroy'])->name('delete');
});


// ================= 9. الإعدادات (Settings) =================
Route::get('/settings', fn() => view('settings.index'))->name('settings.index');



// ================= 10. قسم تقارير العمل وتقارير الموقع (Work Reports) =================
Route::prefix('reports')->name('reports.')->group(function () {
    // 1. ملخص الوردية
    Route::get('/shift', [WorkReportController::class, 'shiftSummaryView'])->name('shift');

    // 2. التقرير الشامل (تلخيص) + تقرير الدخل + ملخص الحسابات + تقارير الموقع
    Route::get('/comprehensive', [WorkReportController::class, 'comprehensiveView'])->name('comprehensive');
    Route::get('/income', fn () => redirect('/reports/comprehensive?tab=profits'))->name('income');
    Route::get('/accounts-summary', fn () => redirect('/reports/comprehensive?tab=customers'))->name('accounts');
    Route::get('/website', fn () => redirect('/reports/comprehensive?tab=website'))->name('website');
});

// مسارات الـ API الخاصة بتقارير العمل (/api/reports/...)
Route::prefix('api/reports')->name('api.reports.')->group(function () {
    Route::get('/comprehensive-data', [WorkReportController::class, 'comprehensiveDataApi'])->name('comprehensive.data');
    Route::get('/shift-data', [WorkReportController::class, 'shiftSummaryDataApi'])->name('shift.data');
});



// ================= 11. قسم شئون العاملين وإدارة الموظفين (Employees) =================
Route::prefix('employees')->name('employees.')->group(function () {
    Route::get('/create', [EmployeeController::class, 'createView'])->name('create');
    Route::get('/log', [EmployeeController::class, 'logView'])->name('log');
});

// مسارات الـ API الخاصة بالموظفين والرواتب وتصفية الحساب (/api/employees/...)
Route::prefix('api/employees')->name('api.employees.')->group(function () {
    Route::get('/bootstrap', [EmployeeController::class, 'bootstrapApi'])->name('bootstrap');
    Route::post('/save', [EmployeeController::class, 'saveEmployee'])->name('save');
    Route::post('/{id}/transactions', [EmployeeController::class, 'storeTransaction'])->name('transactions.store');
    Route::delete('/transactions/{txId}', [EmployeeController::class, 'deleteTransaction'])->name('transactions.delete');
    Route::post('/{id}/settle', [EmployeeController::class, 'settleAccount'])->name('settle');
    Route::post('/{id}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('toggle_status');
});