<?php

use App\Http\Controllers\Web\Admin\MainController;

use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware('checkPermission:can view admin')->group(function () {
    Route::get('/', [MainController::class, 'admin'])->name('admin');

    if(!app()->environment('production')){
        include __DIR__ . '/admin/ContractRoutes.php';
        include __DIR__ . '/admin/PaymentRoutes.php';
        include __DIR__ . '/admin/ServiceRoutes.php';
        include __DIR__ . '/admin/UserRoutes.php';
        include __DIR__ . '/admin/DepartmentRoutes.php';
        include __DIR__ . '/admin/SaleDepartmentRoutes.php';
        include __DIR__ . '/admin/ProjectManagersDepartmentRouter.php';
        include __DIR__ . '/admin/SettingRoutes.php';
        include __DIR__ . '/admin/AdvertisingDepartmentRouter.php';
    }

    include __DIR__ . '/admin/OrganizationRoutes.php';
    include __DIR__ . '/admin/DocumentGeneratorRoutes.php';
});
