<?php

use App\Http\Controllers\Admin\ContractController as AdminContractController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\Departments\SaleDepartmentController;
use App\Http\Controllers\Admin\DocumentTemplateController as AdminDocumentTemplateController;
use App\Http\Controllers\Admin\OrganizationController as AdminOrganizationController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\WorkingDayController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Resources\ContractController;
use App\Http\Controllers\FinanceWeekController;
use App\Http\Controllers\Lk\ContractGeneratorController as LkContractGeneratorController;
use App\Http\Controllers\Lk\MainController as LkMainController;
use App\Http\Controllers\Lk\PaymentGeneratorController as LkPaymentGeneratorController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\Resources\DepartmentController as ResourcesDepartmentController;
use App\Http\Controllers\Resources\DocumentTemplateController as ResourcesDocumentTemplateController;
use App\Http\Controllers\Resources\OptionController;
use App\Http\Controllers\Resources\OrganizationController as ResourcesOrganizationController;
use App\Http\Controllers\Resources\OrganizationServiceDocumentTemplateController as ResourcesOrganizationServiceDocumentTemplateController;
use App\Http\Controllers\Resources\PaymentController as ResourcesPaymentController;
use App\Http\Controllers\Resources\PositionController as ResourcesPositionController;
use App\Http\Controllers\Resources\ServiceCategoryController as ResourcesServiceCategoryController;
use App\Http\Controllers\Resources\ServiceController as ResourcesServiceController;
use App\Http\Controllers\Resources\WorkPlanController;
use App\Http\Controllers\Resources\UserController as ResourcesUserController;

use Illuminate\Support\Facades\Route; 


Route::get('/', [MainController::class, 'home'])->name('home');
Route::get('/fast-login', [MainController::class, 'loginHome'])->name('fastLogin');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});

Route::middleware('auth')->group(function () {

    Route::get('/logout', [LogoutController::class, 'logout'])->name('logout');

    Route::prefix('/lk')->group(function () {
        Route::get('/', [LkMainController::class, 'index'])->name('lk');


        Route::prefix('/contracts')->group(function () {
            Route::get('/create', [LkContractGeneratorController::class, 'create'])->name('lk.contract.create');
            Route::post('/store', [LkContractGeneratorController::class, 'store'])->name('lk.contract.store');
        });
        
        Route::prefix('/payments')->group(function () {
            Route::get('/create', [LkPaymentGeneratorController::class, 'create'])->name('lk.payment.create');
            Route::post('/store', [LkPaymentGeneratorController::class, 'store'])->name('lk.payment.store');
        });
    });
});


Route::prefix('admin')->middleware('role:admin')->group(function () {
    Route::get('/', [MainController::class, 'admin'])->name('admin');

    Route::prefix('contracts')->group(function () {
        Route::get('', [AdminContractController::class, 'index'])->name('admin.contract.index');

        // Нужно будет перенести в ресурс
        // Route::post('/{contract}/attach-user', [AdminContractController::class, 'attachUser'])->name('admin.contract.attachUser');
        Route::get('/{contract}', [AdminContractController::class, 'show'])->name('admin.contract.show');
    });

    Route::prefix('payments')->group(function () {
        Route::get('', [PaymentController::class, 'index'])->name('admin.payment.index');
        Route::get('/unsettled', [PaymentController::class, 'unsorted'])->name('admin.payment.unsorted');
        Route::get('/{payment}', [PaymentController::class, 'show'])->name('admin.payment.show');
    });

    Route::prefix('organizations')->group(function () {
        Route::get('', [AdminOrganizationController::class, 'index'])->name('admin.organization.index');
        Route::get('/create', [AdminOrganizationController::class, 'create'])->name('admin.organization.create');
        Route::get('/{organization}/edit', [AdminOrganizationController::class, 'edit'])->name('admin.organization.edit');
        Route::prefix('document-templates')->group(function () {
            Route::get('/', [AdminDocumentTemplateController::class, 'index'])->name('admin.organization.document-template.index');
            Route::get('/attach', [AdminDocumentTemplateController::class, 'attach'])->name('admin.organization.document-template.attach');
            Route::get('/{documentTemplate}/edit', [AdminDocumentTemplateController::class, 'edit'])->name('admin.organization.document-template.edit');
        });
    });

    Route::prefix('servicies')->group(function () {
        Route::prefix('categories')->group(function () {
            Route::get('/', [ServiceCategoryController::class, 'index'])->name('admin.service.category.index');
            Route::get('/{serviceCategory}/edit', [ServiceCategoryController::class, 'edit'])->name('admin.service.category.edit');
        });

        Route::get('/create', [ServiceController::class, 'create'])->name('admin.service.create');
        Route::get('/edit/{service}', [ServiceController::class, 'edit'])->name('admin.service.edit');
        Route::get('/{serviceCategory?}', [ServiceController::class, 'index'])->name('admin.service.index');
    });

    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('admin.user.index');
        Route::get('/create', [UserController::class, 'create'])->name('admin.user.create');
        Route::get('/{user}', [UserController::class, 'show'])->name('admin.user.show');
    });

    Route::prefix('departments')->group(function () {
        Route::get('', [DepartmentController::class, 'index'])->name('admin.department.index');
        Route::get('/create', [DepartmentController::class, 'create'])->name('admin.department.create');
        Route::get('/{department}', [DepartmentController::class, 'show'])->name('admin.department.show');

        Route::prefix('positions')->group(function () {
            Route::get('/create', [PositionController::class, 'create'])->name('admin.department.position.create');
        });
    });

    Route::prefix('sale-department')->group(function () {
        Route::get('/', [SaleDepartmentController::class, 'index'])->name('admin.sale-department.index');
        Route::get('/user-report', [SaleDepartmentController::class, 'userReport'])->name('admin.sale-department.user-report');
        Route::get('/report-settings', [SaleDepartmentController::class, 'reportSettings'])->name('admin.sale-department.report-settings');
    });

    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('admin.settings.index');
        Route::get('/calendar', [SettingsController::class, 'calendar'])->name('admin.settings.calendar');
        Route::get('/finance-week', [SettingsController::class, 'financeWeek'])->name('admin.settings.finance-week');
    });
});

Route::middleware('auth')->group(function () {
    Route::resource('workPlan', WorkPlanController::class)->only([
        'store',
        'update',
        'destroy'
    ]);

    Route::resource('option', OptionController::class)->only([
        'store',
        'update',
        'destroy'
    ]);

    Route::resource('contract', ContractController::class)->only([
        'store',
        'index'
    ]);

    Route::resource('payment', ResourcesPaymentController::class)->only([
        'index',
        'store',
    ]);

    Route::get('payment/shortlist/{payment}', [ResourcesPaymentController::class, 'shortlist'])->name('payment.shortlist');
    Route::post('payment/shortlist/attach', [ResourcesPaymentController::class, 'shortlistAttach'])->name('payment.shortlist.attach');

    Route::resource('service-category', ResourcesServiceCategoryController::class)->only([
        'store',
        'update',
        'destroy'
    ]);

    Route::resource('service', ResourcesServiceController::class)->only([
        'store',
        'update',
        'destroy'
    ]);

    Route::resource('organization', ResourcesOrganizationController::class)->only([
        'store',
        'update',
        'destroy'
    ]);

    Route::resource('document-template', ResourcesDocumentTemplateController::class)->only([
        'store',
        'update',
        'destroy'
    ]);

    Route::get('document-template/download/{documentTemplate}', [ResourcesDocumentTemplateController::class, 'download'])->name('document-template.download');

    Route::resource('osdt', ResourcesOrganizationServiceDocumentTemplateController::class)->only([
        'store',
        'destroy'
    ]);

    Route::resource('user', ResourcesUserController::class)->only([
        'store',
        'destroy'
    ]);

    Route::resource('department', ResourcesDepartmentController::class)->only([
        'store',
    ]);
    Route::resource('department', ResourcesPositionController::class)->only([
        'store',
    ]);

    Route::post('/working-day', [WorkingDayController::class, 'toggleType']);

    Route::post('/finance-week', [FinanceWeekController::class, 'setWeeks'])->name('finance-week.set-weeks');
    Route::put('/finance-week', [FinanceWeekController::class, 'updateWeeks'])->name('finance-week.set-weeks');
})->middleware('role:admin');
