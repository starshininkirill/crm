<?php

use App\Http\Controllers\Admin\MainController;

use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware('role:admin')->group(function () {
    Route::get('/', [MainController::class, 'admin'])->name('admin');

    include __DIR__ . '/admin/ContractRoutes.php';
    include __DIR__ . '/admin/PaymentRoutes.php';
    include __DIR__ . '/admin/OrganizationRoutes.php';
    include __DIR__ . '/admin/ServiceRoutes.php';
    include __DIR__ . '/admin/UserRoutes.php';
    include __DIR__ . '/admin/TimeCheckRoutes.php';
    include __DIR__ . '/admin/DepartmentRoutes.php';
    include __DIR__ . '/admin/SaleDepartmentRoutes.php';
    include __DIR__ . '/admin/SettingRoutes.php';
});
