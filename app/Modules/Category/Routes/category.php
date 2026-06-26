<?php

use App\Modules\Category\CategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/export', [CategoryController::class, 'export'])->middleware('permission:category.export,web');
Route::post('/import', [CategoryController::class, 'import'])->middleware('permission:category.import,web');
Route::post('/bulk-delete', [CategoryController::class, 'bulkDestroy'])->middleware('permission:category.bulkDestroy,web');
Route::get('/', [CategoryController::class, 'index'])->middleware('permission:category.index,web');
Route::get('/{category}', [CategoryController::class, 'show'])->middleware('permission:category.show,web');
Route::post('/', [CategoryController::class, 'store'])->middleware('permission:category.store,web');
Route::put('/{category}', [CategoryController::class, 'update'])->middleware('permission:category.update,web');
Route::delete('/{category}', [CategoryController::class, 'destroy'])->middleware('permission:category.destroy,web');
