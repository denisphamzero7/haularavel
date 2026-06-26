<?php
use App\Modules\Jobs\JobController;
use Illuminate\Support\Facades\Route;

Route::get('/stats', [JobController::class, 'stats'])->middleware('permission:jobs.stats,web');
Route::get('/export', [JobController::class, 'export'])->middleware('permission:jobs.export,web');
Route::post('/import', [JobController::class, 'import'])->middleware('permission:jobs.import,web');
Route::post('/bulk-delete', [JobController::class, 'bulkDestroy'])->middleware('permission:jobs.bulk-delete,web');
Route::post('/bulk-update-status', [JobController::class, 'bulkUpdateStatus'])->middleware('permission:jobs.bulk-update-status,web');
Route::get('/', [JobController::class, 'index'])->middleware('permission:jobs.index,web');
Route::get('/{job}', [JobController::class, 'show'])->middleware('permission:jobs.show,web');
Route::post('/', [JobController::class, 'store'])->middleware('permission:jobs.store,web');
Route::put('/{job}', [JobController::class, 'update'])->middleware('permission:jobs.update,web');
Route::delete('/{job}', [JobController::class, 'destroy'])->middleware('permission:jobs.delete,web');
Route::patch('/{job}/status', [JobController::class, 'changeStatus'])->middleware('permission:jobs.edit,web');
