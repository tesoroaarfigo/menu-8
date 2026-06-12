<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\DeliveryZoneController;
use App\Http\Controllers\PaymentMethodController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductController::class, 'menu'])->name('menu');
Route::get('/api/delivery-zones', [DeliveryZoneController::class, 'getActive'])->name('api.zones.active');
Route::get('/api/payment-methods', [PaymentMethodController::class, 'getActive'])->name('api.payment-methods.active');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    
    // Rutas de Zonas de Entrega
    Route::resource('zones', DeliveryZoneController::class);
    
    // Rutas de Métodos de Pago
    Route::resource('payment-methods', PaymentMethodController::class);
});
