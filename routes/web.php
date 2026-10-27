<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// الراوت بتاع شاشة الأرشيف (الواجهة الرئيسية اللي بتفتح أول ما تفتح البرنامج)
Route::get('/', function () {
    return view('pos.archive');
});

// الراوت بتاع شاشة الكاشير (شاشة البيع F2)
Route::get('/pos/sales', function () {
    return view('pos.sales');
});

Route::get('/purchases/create', function () {
    return view('purchases.create');
});

Route::get('/suppliers', function () { return view('suppliers.index'); });

Route::get('/suppliers/show', function () {
    return view('suppliers.show');
});

Route::get('/suppliers/payments', function () {
    return view('suppliers.payments');
});

Route::get('/pos/returns/partial', function () { return view('returns.partial'); });

Route::get('/purchases/log', function () {
    return view('purchases.index');
});

Route::get('/returns/log', function () {
    return view('returns.index');
});


Route::get('/returns/suppliers', function () {
    return view('returns.suppliers');
});

Route::get('/returns/suppliers/create', function () {
    return view('returns.supplier_create');
});

Route::get('/customers', function () {
    return view('customers.index');
});

Route::get('/customers/show', function () {
    return view('customers.show');
});

Route::get('/customers/delivery-areas', function () {
    return view('customers.delivery_areas');
});


Route::get('/orders/archive', function () {
    return view('orders.archive');
});

Route::get('/suppliers/payments', function () {
    return view('suppliers.payments');
});

Route::get('/customers/payment', function () {
    return view('customers.payment');
});

Route::get('/customers/payments-report', function () {
    return view('customers.payments-report');
});

Route::get('/expenses', function () {
    return view('expenses.index');
});

Route::get('/expenses/reports', function () {
    return view('expenses.reports');
});




Route::prefix('inventory')->group(function () {
    Route::get('/items', fn() => view('inventory.items'));
    Route::get('/categories', fn() => view('inventory.categories'));
    Route::get('/units', fn() => view('inventory.units'));
    Route::get('/movement', fn() => view('inventory.movement'));

    // تسوية الأرصدة (القائمة الفرعية في الصورة)
    // Route::get('/adjustments', fn() => view('inventory.adjustments'));
    // Route::get('/adjustments-log', fn() => view('inventory.adjustments-log'));
    // Route::get('/adjustments-movement', fn() => view('inventory.adjustments-movement'));

    // أوامر مخزنية وأذون إدارية
    // Route::get('/orders', fn() => view('inventory.orders'));
    // Route::get('/permissions', fn() => view('inventory.permissions'));
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



Route::get('/settings', function () {
    return view('settings.index');
});
