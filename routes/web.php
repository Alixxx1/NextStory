<?php

use Illuminate\Support\Facades\Route;

// استدعاء الكنترولرات (يتم استدعاء الكنترولر اللي هننشئه لكل قسم)
use App\Http\Controllers\POS\POSController;
use App\Http\Controllers\Purchases\PurchaseController;
use App\Http\Controllers\Suppliers\SupplierController;
use App\Http\Controllers\Returns\ReturnController;
use App\Http\Controllers\Customers\CustomerController;
use App\Http\Controllers\Orders\OrderController;
use App\Http\Controllers\Expenses\ExpenseController;
use App\Http\Controllers\Settings\SettingController;
use App\Http\Controllers\Inventory\InventoryItemController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ================= 1. قسم الكاشير والمبيعات (POS) =================
Route::prefix('pos')->group(function () {
    Route::get('/archive', function () { return view('pos.archive'); }); // أو نربطها بكنترولر
    Route::get('/sales', function () { return view('pos.sales'); });
    Route::get('/returns/partial', function () { return view('returns.partial'); });
});

// الواجهة الرئيسية
Route::get('/', function () { return view('pos.archive'); });


// ================= 2. قسم المخازن (Inventory) =================
Route::prefix('inventory')->group(function () {
    // ربطنا شاشة الأصناف بالكنترولر الحقيقي اللي لسه عاملينه
    Route::get('/items', [InventoryItemController::class, 'index'])->name('inventory.items');

    // API للـ POS
    Route::get('/api/pos-items', [InventoryItemController::class, 'posItemsApi']);

    // باقي الشاشات (تُربط بكنترولراتها لاحقاً)
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
    Route::get('/orders-movement', fn() => view('inventory.orders-log')); // تأكد من اسم الـ view الصحيح هنا

    Route::get('/permissions', fn() => view('inventory.permissions'));
    Route::get('/permissions-log', fn() => view('inventory.permissions-log'));
    Route::get('/permissions-movement', fn() => view('inventory.permissions-log'));
});

// ================= 3. قسم المشتريات (Purchases) =================
Route::prefix('purchases')->group(function () {
    Route::get('/log', function () { return view('purchases.index'); });
    Route::get('/create', function () { return view('purchases.create'); });
});

// ================= 4. قسم الموردين (Suppliers) =================
Route::prefix('suppliers')->group(function () {
    Route::get('/', function () { return view('suppliers.index'); });
    Route::get('/show', function () { return view('suppliers.show'); });
    Route::get('/payments', function () { return view('suppliers.payments'); });
});

// ================= 5. قسم العملاء (Customers) =================
Route::prefix('customers')->group(function () {
    Route::get('/', function () { return view('customers.index'); });
    Route::get('/show', function () { return view('customers.show'); });
    Route::get('/delivery-areas', function () { return view('customers.delivery_areas'); });
    Route::get('/payment', function () { return view('customers.payment'); });
    Route::get('/payments-report', function () { return view('customers.payments-report'); });
});

// ================= 6. قسم المرتجعات (Returns) =================
Route::prefix('returns')->group(function () {
    Route::get('/log', function () { return view('returns.index'); });
    Route::get('/suppliers', function () { return view('returns.suppliers'); });
    Route::get('/suppliers/create', function () { return view('returns.supplier_create'); });
});

// ================= 7. قسم الطلبات والأونلاين (Orders) =================
Route::prefix('orders')->group(function () {
    Route::get('/archive', function () { return view('orders.archive'); });
});

// ================= 8. قسم المصروفات (Expenses) =================
Route::prefix('expenses')->group(function () {
    Route::get('/', function () { return view('expenses.index'); });
    Route::get('/reports', function () { return view('expenses.reports'); });
});

// ================= 9. الإعدادات (Settings) =================
Route::get('/settings', function () { return view('settings.index'); });

// مسارات الـ API الخاصة بحفظ وتعديل أصناف المخزن والتصنيفات
Route::post('/api/inventory/items/save', [InventoryItemController::class, 'saveItem']);
Route::post('/api/inventory/categories/quick', [InventoryItemController::class, 'quickCategory']);
Route::post('/api/inventory/items/batch', [InventoryItemController::class, 'batchAction']);
Route::get('/api/inventory/pos-items', [InventoryItemController::class, 'posItemsApi']);
