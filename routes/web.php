<?php

use Illuminate\Support\Facades\Route;

// استدعاء الكنترولرات الفعلية
use App\Http\Controllers\POS\PosSalesController;
use App\Http\Controllers\Inventory\InventoryItemController;
use App\Http\Controllers\Customers\CustomerController;
use App\Http\Controllers\Customers\IndexController;
use App\Http\Controllers\Customers\ShowController;
use App\Http\Controllers\Orders\OrderArchiveController;
use App\Http\Controllers\Returns\SalesReturnController;

/*
|--------------------------------------------------------------------------
| Web Routes - NextStore SaaS
|--------------------------------------------------------------------------
*/

// ================= الواجهة الرئيسية للبرنامج =================
// تفتح شاشة pos.archive كما طلبت بدون أي تداخل
Route::get('/', function () {
    return view('pos.archive');
})->name('home');


// ================= 1. قسم الكاشير والمبيعات (POS) =================
Route::prefix('pos')->group(function () {
    Route::get('/archive', function () {
        return view('pos.archive');
    })->name('pos.archive');

    Route::get('/sales', [PosSalesController::class, 'index'])->name('pos.sales');
    Route::get('/returns/partial', function () {
        return view('returns.partial');
    })->name('pos.returns.partial');
});

// مسارات الـ API الخاصة بشاشة البيع (POS Sales)
Route::prefix('api/pos')->group(function () {
    Route::get('/bootstrap', [PosSalesController::class, 'bootstrapData']);
    Route::post('/zones/save', [PosSalesController::class, 'saveZone']);
    Route::post('/customers/save', [PosSalesController::class, 'saveCustomer']);
    Route::post('/invoices/store', [PosSalesController::class, 'storeInvoice']);
});


// ================= 2. قسم المخازن (Inventory) =================
Route::prefix('inventory')->group(function () {
    Route::get('/items', [InventoryItemController::class, 'index'])->name('inventory.items');
    Route::get('/api/pos-items', [InventoryItemController::class, 'posItemsApi']);

    Route::get('/categories', fn() => view('inventory.categories'));
    Route::get('/units', fn() => view('inventory.units'));
    Route::get('/movement', fn() => view('inventory.movement'));

    Route::get('/manufacturing', fn() => view('inventory.manufacturing'));
    Route::get('/manufacturing-log', fn() => view('inventory.manufacturing-log'));

    Route::get('/adjustments', fn() => view('inventory.adjustments'));
    Route::get('/adjustments-log', fn() => view('inventory.adjustments-log'));
    Route::get('/adjustments-movement', fn() => view('inventory.adjustments-movement'));

    Route::get('/orders', fn() => view('inventory.orders'));
    Route::get('/orders-log', fn() => view('inventory.orders-log'));
    Route::get('/orders-movement', fn() => view('inventory.orders-log'));

    Route::get('/permissions', fn() => view('inventory.permissions'));
    Route::get('/permissions-log', fn() => view('inventory.permissions-log'));
    Route::get('/permissions-movement', fn() => view('inventory.permissions-log'));
});

// مسارات الـ API الخاصة بحفظ وتعديل أصناف المخزن والتصنيفات
Route::prefix('api/inventory')->group(function () {
    Route::post('/items/save', [InventoryItemController::class, 'saveItem']);
    Route::post('/categories/quick', [InventoryItemController::class, 'quickCategory']);
    Route::post('/items/batch', [InventoryItemController::class, 'batchAction']);
    Route::get('/pos-items', [InventoryItemController::class, 'posItemsApi']);
});


// ================= 3. قسم العملاء ومناطق التوصيل (Customers) =================
// تم دمجها بدون تكرار بحيث يخدم كل كنترولر الشاشة الخاصة به:
Route::prefix('customers')->group(function () {
    // 1. شاشة قائمة العملاء (IndexController)
    Route::get('/', [IndexController::class, 'index'])->name('customers.index');

    // 2. شاشة حساب العميل وكشف الحساب (ShowController)
    Route::get('/show', [ShowController::class, 'show'])->name('customers.show');

    // 3. شاشة مناطق التوصيل وأسعارها (CustomerController)
    Route::get('/delivery-areas', [CustomerController::class, 'deliveryAreas'])->name('customers.delivery_areas');

    // باقي شاشات العملاء الفرعية
    Route::get('/payment', fn() => view('customers.payment'));
    Route::get('/payments-report', fn() => view('customers.payments-report'));
});

// مسارات الـ API الموحدة للعملاء ومناطق التوصيل وحساب العميل
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

    // ج) API حساب العميل وكشف الحساب (ShowController)
    Route::get('/search-list', [ShowController::class, 'getCustomersList']);
    Route::get('/{id}/account-details', [ShowController::class, 'getAccountDetails']);
    Route::post('/{id}/quick-update', [ShowController::class, 'updateCustomerQuick']);
    Route::post('/{id}/transactions', [ShowController::class, 'storeTransaction']);
    Route::post('/{id}/notes', [ShowController::class, 'storeNote']);
});


// ================= 4. قسم أرشيف الطلبات (Orders Archive) =================
// يفتح فقط على /orders/archive بدون التأثير على الصفحة الرئيسية /
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
    Route::get('/log', fn() => view('returns.index'));
    Route::get('/suppliers', fn() => view('returns.suppliers'));
    Route::get('/suppliers/create', fn() => view('returns.supplier_create'));
});


// ================= 6. قسم المشتريات (Purchases) =================
Route::prefix('purchases')->group(function () {
    Route::get('/log', fn() => view('purchases.index'));
    Route::get('/create', fn() => view('purchases.create'));
});


// ================= 7. قسم الموردين (Suppliers) =================
Route::prefix('suppliers')->group(function () {
    Route::get('/', fn() => view('suppliers.index'));
    Route::get('/show', fn() => view('suppliers.show'));
    Route::get('/payments', fn() => view('suppliers.payments'));
});


// ================= 8. قسم المصروفات (Expenses) =================
Route::prefix('expenses')->group(function () {
    Route::get('/', fn() => view('expenses.index'));
    Route::get('/reports', fn() => view('expenses.reports'));
});


// ================= 9. الإعدادات (Settings) =================
Route::get('/settings', fn() => view('settings.index'));




// داخل قسم pos:
Route::get('/pos/returns/partial', [SalesReturnController::class, 'partialView'])->name('pos.returns.partial');

// مسارات الـ API للمرتجعات (/api/returns/...)
Route::prefix('api/returns')->name('api.returns.')->group(function () {
    Route::get('/invoice-details', [SalesReturnController::class, 'getInvoiceForReturn']);
    Route::post('/partial/save', [SalesReturnController::class, 'savePartialReturn']);
});



// ================= 5. قسم المرتجعات (Returns) =================
Route::prefix('returns')->group(function () {
    Route::get('/log', [SalesReturnController::class, 'logView'])->name('returns.log');
    Route::get('/suppliers', fn() => view('returns.suppliers'));
    Route::get('/suppliers/create', fn() => view('returns.supplier_create'));
});

// مسارات الـ API للمرتجعات (/api/returns/...)
Route::prefix('api/returns')->name('api.returns.')->group(function () {
    Route::get('/log', [SalesReturnController::class, 'getReturnsLog'])->name('log');
    Route::delete('/{returnId}', [SalesReturnController::class, 'deleteReturn'])->name('delete');
    Route::get('/invoice-details', [SalesReturnController::class, 'getInvoiceForReturn'])->name('invoice_details');
    Route::post('/partial/save', [SalesReturnController::class, 'savePartialReturn'])->name('partial_save');
});
