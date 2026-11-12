<?php

use Illuminate\Support\Facades\Route;

// استدعاء الكنترولرات الفعلية
use App\Http\Controllers\POS\PosSalesController;
use App\Http\Controllers\POS\DeliveryRunController;
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
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\SecurityAlertController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SystemUpdateController;
use App\Support\SystemSettings;

/*
|--------------------------------------------------------------------------
| Web Routes - NextStore SaaS
|--------------------------------------------------------------------------
*/

// ================= 0. بوابة تسجيل الدخول الإجبارية والصفحة الرئيسية =================
Route::get('/login', [AdminAuthController::class, 'loginView'])->name('login');
Route::post('/api/auth/login', [AdminAuthController::class, 'loginSubmit'])->name('api.auth.login');
Route::post('/api/auth/logout', [AdminAuthController::class, 'logout'])->name('api.auth.logout');
Route::get('/logout', [AdminAuthController::class, 'logout'])->name('logout');
Route::post('/api/auth/authorize-manager', [AdminAuthController::class, 'authorizeManagerAction'])->middleware('throttle:10,1');
Route::post('/api/auth/close-shift', [AdminAuthController::class, 'closeShiftAndSwitch']);

// النسخ الاحتياطي والتصفير: تتحقق بنفسها من صلاحية المدير وترجع 401/403
Route::get('/admin/backup/download', [AdminAuthController::class, 'downloadEncryptedBackup'])->name('admin.backup.download');
Route::post('/admin/backup/restore', [AdminAuthController::class, 'restoreEncryptedBackup'])->middleware('throttle:3,1')->name('admin.backup.restore');
Route::post('/admin/system/factory-reset', [AdminAuthController::class, 'factoryZeroReset'])->middleware('throttle:3,1')->name('admin.system.reset');

// كل الصفحات والـ APIs التالية تتطلب تسجيل الدخول
Route::middleware('pos.auth')->group(function () {

// الصفحة الرئيسية: إذا لم يسجل دخول يفتح نافذة تسجيل الدخول، وإذا سجل دخول يفتح شاشة المبيعات فوراً!
Route::get('/', function () {
    if (!session()->has('pos_user')) {
        return redirect('/login');
    }
    return redirect('/pos/archive');
})->name('home');

// ================= 1. قسم الكاشير والمبيعات (POS) =================
Route::prefix('pos')->group(function () {
    Route::get('/archive', [PosSalesController::class, 'archive'])->name('pos.archive');

    Route::get('/sales', [PosSalesController::class, 'index'])->name('pos.sales');
    Route::get('/returns/partial', [SalesReturnController::class, 'partialView'])->name('pos.returns.partial');
});

Route::prefix('api/pos')->group(function () {
    Route::get('/bootstrap', [PosSalesController::class, 'bootstrapData']);
    Route::post('/zones/save', [PosSalesController::class, 'saveZone']);
    Route::post('/customers/save', [PosSalesController::class, 'saveCustomer']);
    Route::get('/suspended', [\App\Http\Controllers\POS\SuspendedInvoiceController::class, 'index']);
    Route::post('/suspended', [\App\Http\Controllers\POS\SuspendedInvoiceController::class, 'store']);
    Route::delete('/suspended/{clientId}', [\App\Http\Controllers\POS\SuspendedInvoiceController::class, 'destroy']);
    Route::post('/invoices/store', [PosSalesController::class, 'storeInvoice'])->middleware('pos.auth:pos.sales');
    Route::get('/invoices/next-number', [PosSalesController::class, 'nextNumber']);
    Route::get('/riders', [DeliveryRunController::class, 'riders']);
    Route::post('/riders/save', [DeliveryRunController::class, 'saveRider'])->middleware('pos.auth:delivery.riders');
    Route::get('/dispatch/order', [DeliveryRunController::class, 'order'])->middleware('pos.auth:pos.sales');
    Route::get('/dispatch/pending', [DeliveryRunController::class, 'pending'])->middleware('pos.auth:pos.sales');
    Route::get('/dispatch/runs', [DeliveryRunController::class, 'runs'])->middleware('pos.auth:delivery.riders');
    Route::post('/dispatch', [DeliveryRunController::class, 'store'])->middleware(['pos.auth:pos.sales', 'throttle:20,1']);
});

// ================= 2. قسم المخازن والتصنيع والجرد (Inventory) =================
Route::prefix('inventory')->group(function () {
    Route::get('/items', [InventoryItemController::class, 'index'])->name('inventory.items');
    Route::get('/api/pos-items', [InventoryItemController::class, 'posItemsApi']);
    Route::get('/items/{id}/movements', [InventoryItemController::class, 'movements']);

    Route::get('/categories', [InventorySubController::class, 'categoriesView'])->name('inventory.categories');
    Route::get('/units', [InventorySubController::class, 'unitsView'])->name('inventory.units');
    Route::get('/movement', [InventorySubController::class, 'movementView'])->name('inventory.movement');
    Route::get('/stocktaking', [InventorySubController::class, 'stocktakingView'])->name('inventory.stocktaking');

    Route::get('/manufacturing', [InventorySubController::class, 'manufacturingView'])->name('inventory.manufacturing');
    Route::get('/manufacturing-log', [InventorySubController::class, 'manufacturingLogView'])->name('inventory.manufacturing.log');

    Route::get('/adjustments', [InventorySubController::class, 'adjustmentsView'])->name('inventory.adjustments');
    Route::get('/adjustments-log', [InventorySubController::class, 'adjustmentsLogView'])->name('inventory.adjustments.log');
    Route::get('/adjustments-movement', [InventorySubController::class, 'adjustmentsMovementView'])->name('inventory.adjustments.movement');

    Route::get('/orders', [InventorySubController::class, 'ordersView'])->name('inventory.orders');
    Route::get('/orders-log', [InventorySubController::class, 'ordersLogView'])->name('inventory.orders.log');
    Route::get('/orders-movement', [InventorySubController::class, 'ordersLogView'])->name('inventory.orders.movement');

    Route::get('/permissions', [InventorySubController::class, 'permissionsView'])->name('inventory.permissions');
    Route::get('/permissions-log', [InventorySubController::class, 'permissionsLogView'])->name('inventory.permissions.log');
    Route::get('/permissions-movement', [InventorySubController::class, 'permissionsLogView'])->name('inventory.permissions.movement');
        Route::get('/import-items', [InventorySubController::class, 'importItemsView'])->name('inventory.import_items');

});

Route::prefix('api/inventory')->middleware('pos.auth:inventory.items|inventory.categories|inventory.movement|inventory.stocktaking|inventory.manufacturing|inventory.adjustments|inventory.orders,write')->group(function () {
    Route::post('/import-items/process', [InventorySubController::class, 'importItemsProcessApi']);
    Route::post('/items/{id}/quick-update-pos', [InventoryItemController::class, 'quickUpdateFromPos']);
    Route::post('/items/save', [InventoryItemController::class, 'saveItem']);
    Route::post('/items/{id}/units', [InventoryItemController::class, 'addUnit']);
    Route::post('/items/merge', [InventoryItemController::class, 'mergeItems']);
    Route::post('/categories/quick', [InventoryItemController::class, 'quickCategory']);
    Route::post('/items/batch', [InventoryItemController::class, 'batchAction']);
    Route::get('/pos-items', [InventoryItemController::class, 'posItemsApi']);

    Route::get('/categories/list', [InventorySubController::class, 'categoriesListApi']);
    Route::post('/categories/save', [InventorySubController::class, 'saveCategory']);
    Route::post('/categories/{id}/toggle-delete', [InventorySubController::class, 'toggleDeleteCategory']);

    Route::get('/units/list', [InventorySubController::class, 'unitsListApi']);
    Route::post('/units/save', [InventorySubController::class, 'saveUnit']);
    Route::delete('/units/{id}', [InventorySubController::class, 'deleteUnit']);
    Route::post('/units/reset-defaults', [InventorySubController::class, 'resetDefaultUnits']);

    Route::get('/movements/report', [InventorySubController::class, 'movementsReportApi']);
    Route::get('/movements/ledger/{id}', [InventorySubController::class, 'movementLedgerApi']);

    Route::get('/manufacturing/bootstrap', [InventorySubController::class, 'manufacturingBootstrapApi']);
    Route::post('/manufacturing/save', [InventorySubController::class, 'saveManufacturingBatch']);
    Route::delete('/manufacturing/{id}', [InventorySubController::class, 'deleteManufacturingBatch']);
    Route::post('/manufacturing/item-quick-update', [InventorySubController::class, 'quickUpdateItemFromManufacturing']);

    Route::get('/adjustments/bootstrap', [InventorySubController::class, 'adjustmentsBootstrapApi']);
    Route::post('/adjustments/save', [InventorySubController::class, 'saveAdjustment']);
    Route::post('/adjustments/{id}/toggle-delete', [InventorySubController::class, 'toggleDeleteAdjustment']);
    Route::post('/adjustments/zero-all-stock', [InventorySubController::class, 'zeroAllStock'])->middleware('pos.auth:inventory.adjustments');

    Route::get('/orders/bootstrap', [InventorySubController::class, 'ordersBootstrapApi']);
    Route::post('/orders/save', [InventorySubController::class, 'saveWarehouseOrder']);
    Route::post('/orders/{id}/toggle-delete', [InventorySubController::class, 'toggleDeleteWarehouseOrder']);
    Route::post('/orders/quick-item', [InventorySubController::class, 'quickCreateOrderItem']);

    Route::get('/permissions/bootstrap', [InventorySubController::class, 'permissionsBootstrapApi']);
    Route::post('/permissions/save', [InventorySubController::class, 'saveAdminPermission']);
    Route::post('/permissions/{id}/toggle-delete', [InventorySubController::class, 'toggleDeleteAdminPermission']);

    Route::get('/stocktaking/data', [InventorySubController::class, 'stocktakingDataApi']);
    Route::post('/stocktaking/{id}/quick-qty', [InventorySubController::class, 'quickUpdateStockFromAudit']);
});

// ================= 3. قسم العملاء ومناطق التوصيل (Customers) =================
Route::prefix('customers')->group(function () {
    Route::get('/', [IndexController::class, 'index'])->name('customers.index');
    Route::get('/show', [ShowController::class, 'show'])->name('customers.show');
    Route::get('/delivery-areas', [CustomerController::class, 'deliveryAreas'])->name('customers.delivery_areas');
    Route::get('/payment', [ShowController::class, 'paymentView'])->name('customers.payment');
    Route::get('/payments-report', [ShowController::class, 'paymentsReportView'])->name('customers.payments_report');
});

Route::prefix('api/customers')->name('api.customers.')->middleware('pos.auth:customers.manage,write')->group(function () {
    Route::get('/delivery-zones', [CustomerController::class, 'getDeliveryZones'])->name('zones.index');
    Route::post('/delivery-zones', [CustomerController::class, 'storeDeliveryZone'])->name('zones.store');
    Route::put('/delivery-zones/{id}', [CustomerController::class, 'updateDeliveryZone'])->name('zones.update');
    Route::delete('/delivery-zones/{id}', [CustomerController::class, 'destroyDeliveryZone'])->name('zones.destroy');

    Route::get('/init-data', [IndexController::class, 'getInitData']);
    Route::post('/save', [IndexController::class, 'saveCustomer']);
    Route::post('/{id}/toggle-delete', [IndexController::class, 'toggleDeleteCustomer']);
    Route::post('/zones/quick', [IndexController::class, 'quickCreateZone']);

    Route::get('/payments/bootstrap', [ShowController::class, 'paymentsBootstrap']);
    Route::post('/payments/save', [ShowController::class, 'savePaymentVoucher'])->middleware('pos.auth:customers.manage');
    Route::delete('/payments/{id}', [ShowController::class, 'deletePaymentVoucher'])->middleware('pos.auth:customers.manage');

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

Route::prefix('api/orders')->name('api.orders.')->group(function () {
    Route::get('/archive', [OrderArchiveController::class, 'getOrders'])->name('index');
    Route::get('/last', [OrderArchiveController::class, 'last'])->name('last');
    Route::patch('/{id}/cost', [OrderArchiveController::class, 'updateCost'])->middleware('pos.auth:orders.delete')->name('update_cost');
    Route::delete('/{id}', [OrderArchiveController::class, 'destroy'])->middleware('pos.auth:orders.delete')->name('destroy');
    Route::post('/{id}/restore', [OrderArchiveController::class, 'restore'])->middleware('pos.auth:orders.delete')->name('restore');
});

// ================= 5. قسم المرتجعات (Returns) =================
Route::prefix('returns')->group(function () {
    Route::get('/log', [SalesReturnController::class, 'logView'])->name('returns.log');
    Route::get('/suppliers', [PurchaseController::class, 'supplierReturnsIndex'])->name('returns.suppliers');
    Route::get('/suppliers/create', [PurchaseController::class, 'supplierReturnCreate'])->name('returns.suppliers.create');
});

Route::prefix('api/returns')->name('api.returns.')->group(function () {
    Route::get('/log', [SalesReturnController::class, 'getReturnsLog'])->name('log');
    Route::delete('/{returnId}', [SalesReturnController::class, 'deleteReturn'])->middleware('pos.auth:pos.returns')->name('delete');
    Route::get('/invoice-details', [SalesReturnController::class, 'getInvoiceForReturn'])->name('invoice_details');
    Route::post('/partial/save', [SalesReturnController::class, 'savePartialReturn'])->middleware('pos.auth:pos.returns')->name('partial_save');
});

// ================= 6. قسم المشتريات (Purchases) =================
Route::prefix('purchases')->name('purchases.')->group(function () {
    Route::get('/create', [PurchaseController::class, 'create'])->name('create');
    Route::get('/log', [PurchaseController::class, 'index'])->name('log');
    Route::get('/index', [PurchaseController::class, 'index'])->name('index');
});

Route::prefix('api/purchases')->name('api.purchases.')->middleware('pos.auth:purchases.manage,write')->group(function () {
    Route::get('/bootstrap', [PurchaseController::class, 'bootstrapData'])->name('bootstrap');
    Route::get('/log', [PurchaseController::class, 'getInvoicesLog'])->name('log');
    Route::post('/save', [PurchaseController::class, 'saveInvoice'])->middleware('pos.auth:purchases.manage')->name('save');
    Route::delete('/{id}', [PurchaseController::class, 'deleteInvoice'])->middleware('pos.auth:purchases.manage')->name('delete');

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

Route::prefix('api/suppliers')->name('api.suppliers.')->middleware('pos.auth:suppliers.manage|purchases.manage,write')->group(function () {
    Route::get('/list', [SupplierController::class, 'listApi'])->name('list');
    Route::post('/save', [SupplierController::class, 'save'])->name('save');
    Route::delete('/{id}', [SupplierController::class, 'destroy'])->name('delete');

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

Route::prefix('api/expenses')->name('api.expenses.')->middleware('pos.auth:expenses.manage,write')->group(function () {
    Route::get('/bootstrap', [ExpenseController::class, 'bootstrapApi'])->name('bootstrap');
    Route::post('/tree/save', [ExpenseController::class, 'saveTree'])->name('tree.save');
    Route::post('/save', [ExpenseController::class, 'saveExpense'])->name('save');
    Route::delete('/{id}', [ExpenseController::class, 'destroy'])->name('delete');
});

// ================= 9. الإعدادات (Settings) =================
Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
Route::post('/api/settings/save', [SettingsController::class, 'save'])->middleware('throttle:30,1')->name('api.settings.save');
Route::post('/api/settings/reset-image', [SettingsController::class, 'resetImage'])->middleware('throttle:20,1')->name('api.settings.reset-image');
Route::get('/api/settings/printers', [SettingsController::class, 'printers'])->middleware('throttle:60,1')->name('api.settings.printers');
Route::get('/api/system/update/info', [SystemUpdateController::class, 'info'])->middleware('pos.auth:settings.manage')->name('api.system.update.info');
Route::post('/api/system/update/upload', [SystemUpdateController::class, 'upload'])->middleware(['pos.auth:settings.manage', 'throttle:5,1'])->name('api.system.update.upload');
Route::post('/api/system/update/online', [SystemUpdateController::class, 'online'])->middleware(['pos.auth:settings.manage', 'throttle:5,1'])->name('api.system.update.online');

// ================= 10. قسم تقارير العمل وتقارير الموقع (Work Reports) =================
Route::prefix('reports')->name('reports.')->middleware('pos.auth:reports.shift|reports.comprehensive')->group(function () {
    Route::get('/shift', [WorkReportController::class, 'shiftSummaryView'])->name('shift');
    Route::get('/comprehensive', [WorkReportController::class, 'comprehensiveView'])->name('comprehensive');
    Route::get('/income', fn () => redirect('/reports/comprehensive?tab=profits'))->name('income');
    Route::get('/accounts-summary', fn () => redirect('/reports/comprehensive?tab=customers'))->name('accounts');
    Route::get('/website', fn () => redirect('/reports/comprehensive?tab=website'))->name('website');
});

Route::prefix('api/reports')->name('api.reports.')->middleware('pos.auth:reports.shift|reports.comprehensive')->group(function () {
    Route::get('/comprehensive-data', [WorkReportController::class, 'comprehensiveDataApi'])->name('comprehensive.data');
    Route::get('/shift-data', [WorkReportController::class, 'shiftSummaryDataApi'])->name('shift.data');
});

// ================= 11. قسم شئون العاملين وإدارة الموظفين (Employees) =================
Route::prefix('employees')->name('employees.')->group(function () {
    Route::get('/create', [EmployeeController::class, 'createView'])->name('create');
    Route::get('/log', [EmployeeController::class, 'logView'])->name('log');
});

Route::prefix('api/employees')->name('api.employees.')->middleware('pos.auth:employees.manage')->group(function () {
    Route::get('/bootstrap', [EmployeeController::class, 'bootstrapApi'])->name('bootstrap');
    Route::post('/save', [EmployeeController::class, 'saveEmployee'])->name('save');
    Route::post('/{id}/transactions', [EmployeeController::class, 'storeTransaction'])->name('transactions.store');
    Route::delete('/transactions/{txId}', [EmployeeController::class, 'deleteTransaction'])->name('transactions.delete');
    Route::post('/{id}/settle', [EmployeeController::class, 'settleAccount'])->name('settle');
    Route::post('/{id}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('toggle_status');
});

// ================= 12. قسم الأدوات والإدارة والأمان والخزائن (Tools & Admin) =================
Route::prefix('tools')->name('tools.')->group(function () {
    Route::get('/stocktaking', [InventorySubController::class, 'stocktakingView'])->name('stocktaking');
    Route::get('/barcode-print', [\App\Http\Controllers\ToolsController::class, 'barcodePrint'])->name('barcode');
    Route::get('/scale-barcode-print', [\App\Http\Controllers\ToolsController::class, 'scaleBarcodePrint'])->name('scale_barcode');
        Route::get('/import-items', [InventorySubController::class, 'importItemsView'])->name('import_items');
        Route::get('/import-customers', [InventorySubController::class, 'importCustomersView'])->name('import_customers');
Route::get('/import-suppliers', [InventorySubController::class, 'importSuppliersView'])->name('import_suppliers');


});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/security-alerts', [SecurityAlertController::class, 'page'])->name('security_alerts');
    Route::get('/users', [AdminAuthController::class, 'usersView'])->name('users');
    Route::get('/shifts-log', [AdminAuthController::class, 'shiftsLogView'])->name('shifts_log');
    Route::get('/login-log', [AdminAuthController::class, 'loginLogView'])->name('login_log');
    Route::get('/permissions', [AdminAuthController::class, 'usersView'])->name('permissions');
    Route::get('/safes', [AdminAuthController::class, 'safesView'])->name('safes');

    Route::get('/riders', [DeliveryRunController::class, 'ridersPage'])->middleware('pos.auth:delivery.riders')->name('riders');
});

// مسارات الـ API الخاصة بقسم الإدارة (المستخدمين، الورديات، سجل الدخول، الخزائن)
Route::prefix('api/security')->group(function () {
    Route::post('/event', [SecurityAlertController::class, 'event'])->middleware('throttle:120,1');
    Route::get('/pending', [SecurityAlertController::class, 'pending']);
    Route::post('/ack', [SecurityAlertController::class, 'ack']);
    Route::get('/list', [SecurityAlertController::class, 'list']);
    Route::post('/cart-audit', [\App\Http\Controllers\Admin\CartAuditController::class, 'store'])->middleware('throttle:120,1');
    Route::get('/cart-audits', [\App\Http\Controllers\Admin\CartAuditController::class, 'list']);
    Route::post('/cart-audits/seen', [\App\Http\Controllers\Admin\CartAuditController::class, 'seen']);
    Route::post('/cart-audits/delete', [\App\Http\Controllers\Admin\CartAuditController::class, 'destroy']);
});

Route::prefix('api/admin')->group(function () {
    Route::get('/users/bootstrap', [AdminAuthController::class, 'usersBootstrapApi']);
    Route::post('/users/save', [AdminAuthController::class, 'saveUserApi']);
    Route::post('/users/{id}/password', [AdminAuthController::class, 'changeUserPasswordApi']);
    Route::post('/users/{id}/toggle-delete', [AdminAuthController::class, 'toggleDeleteUserApi']);

    Route::get('/shifts/log', [AdminAuthController::class, 'shiftsLogApi']);
    Route::get('/login-log/list', [AdminAuthController::class, 'loginLogApi']);

    Route::get('/safes/bootstrap', [AdminAuthController::class, 'safesBootstrapApi']);
    Route::post('/safes/save', [AdminAuthController::class, 'saveSafeApi'])->middleware('pos.auth:admin.safes');
    Route::post('/safes/transaction', [AdminAuthController::class, 'safeTransactionApi'])->middleware('pos.auth:admin.safes');
});




Route::prefix('api/tools')->middleware('pos.auth:customers.manage|suppliers.manage|inventory.items')->group(function () {
    Route::post('/import-customers-process', [InventorySubController::class, 'importCustomersProcessApi']);
    Route::post('/import-suppliers-process', [InventorySubController::class, 'importSuppliersProcessApi']);
});





}); // نهاية مجموعة pos.auth

// ================= الترخيص والتفعيل (مفتوحة دائماً) =================
Route::prefix('license')->group(function () {
    Route::get('/activate', [\App\Http\Controllers\LicenseController::class, 'activate']);
    Route::get('/expired', [\App\Http\Controllers\LicenseController::class, 'expired']);
    Route::get('/device', [\App\Http\Controllers\LicenseController::class, 'device']);
    Route::post('/reapply', [\App\Http\Controllers\LicenseController::class, 'reapply'])->middleware('throttle:10,1');
    Route::get('/status', [\App\Http\Controllers\LicenseController::class, 'status']);
    Route::post('/request', [\App\Http\Controllers\LicenseController::class, 'request'])->middleware('throttle:10,1');
    Route::post('/check', [\App\Http\Controllers\LicenseController::class, 'check']);
    Route::post('/reset', [\App\Http\Controllers\LicenseController::class, 'reset']);
    Route::post('/heartbeat', [\App\Http\Controllers\LicenseController::class, 'heartbeat']);
});

// ================= سندات الموبايل المعلقة (اعتماد الكاشير) =================
Route::middleware('pos.auth')->group(function () {
    Route::get('/admin/remote-vouchers', [\App\Http\Controllers\RemoteVoucherController::class, 'index']);
    Route::post('/admin/remote-vouchers/sync', [\App\Http\Controllers\RemoteVoucherController::class, 'sync'])->middleware('pos.auth:settings.manage');
    Route::post('/admin/remote-vouchers/{id}', [\App\Http\Controllers\RemoteVoucherController::class, 'decide'])->middleware('pos.auth:settings.manage');
});

// ================= موبايل Cloud Pro وسيرفر المالك (HQ_ENABLED=true فقط) =================
Route::middleware('hq')->group(function () {
    Route::get('/cloud-mobile', [\App\Http\Controllers\CloudMobileController::class, 'index']);
    Route::post('/cloud-mobile/login', [\App\Http\Controllers\CloudMobileController::class, 'login'])->middleware('throttle:10,1');
    Route::post('/cloud-mobile/logout', [\App\Http\Controllers\CloudMobileController::class, 'logout']);
    Route::get('/cloud-mobile/data', [\App\Http\Controllers\CloudMobileController::class, 'data']);
    Route::post('/cloud-mobile/voucher', [\App\Http\Controllers\CloudMobileController::class, 'voucher']);
});

// لوحة المالك السرية (المسار من HQ_PORTAL_PATH)
Route::prefix(config('license.portal'))->middleware('hq')->group(function () {
    $c = \App\Http\Controllers\Admin\HqPortalController::class;
    Route::get('/', [$c, 'login']);
    Route::post('/login', [$c, 'loginSubmit'])->middleware('throttle:10,1');
    Route::middleware('hq:auth')->group(function () use ($c) {
        Route::post('/logout', [$c, 'logout']);
        Route::get('/requests', [$c, 'requests']);
        Route::post('/requests/{id}/approve', [$c, 'approve']);
        Route::post('/requests/{id}/reject', [$c, 'reject']);
        Route::get('/plans', [$c, 'plans']);
        Route::post('/plans', [$c, 'savePlans']);
        Route::get('/stores', [$c, 'stores']);
        Route::post('/stores/{id}', [$c, 'storeAction']);
        Route::get('/account', [$c, 'accountPage']);
        Route::post('/account', [$c, 'account']);
    });
});
