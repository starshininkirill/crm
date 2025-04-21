<?php 

use App\Http\Controllers\Admin\PaymentController;
use Illuminate\Support\Facades\Route;

Route::prefix('payments')->group(function () {
    Route::get('/search-contract', [PaymentController::class, 'searchContract'])->name('admin.payment.search-contract');

    Route::get('/unsorted', [PaymentController::class, 'unsorted'])->name('admin.payment.unsorted');
    Route::get('/unsorted-sbp', [PaymentController::class, 'unsortedSbp'])->name('admin.payment.unsortedSbp');

    Route::get('payment/shortlist/{payment}', [PaymentController::class, 'shortlist'])->name('admin.payment.shortlist');
    Route::post('payment/shortlist/attach', [PaymentController::class, 'shortlistAttach'])->name('admin.payment.shortlist.attach');

    Route::get('/{payment}/show', [PaymentController::class, 'show'])->name('admin.payment.show');
    Route::patch('/{payment}', [PaymentController::class, 'update'])->name('admin.payment.update');

    Route::get('', [PaymentController::class, 'index'])->name('admin.payment.index');
});