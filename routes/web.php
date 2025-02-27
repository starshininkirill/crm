<?php

use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\Departments\SaleDepartmentController;
use App\Http\Controllers\Admin\DocumentTemplateController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\admin\SettingsController;
use App\Http\Controllers\Admin\User\EmploymentTypeController;
use App\Http\Controllers\Admin\User\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Lk\ContractGeneratorController as LkContractGeneratorController;
use App\Http\Controllers\Lk\MainController as LkMainController;
use App\Http\Controllers\Lk\ActGeneratorController as LkActGeneratorController;
use App\Http\Controllers\Lk\SbpGeneratorController as LkSbpGeneratorController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\Resources\OptionController;
use App\Http\Controllers\Resources\OrganizationServiceDocumentTemplateController as ResourcesOrganizationServiceDocumentTemplateController;
use App\Http\Controllers\Resources\PaymentController as ResourcesPaymentController;

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

        Route::prefix('/acts')->group(function () {
            Route::get('/create', [LkActGeneratorController::class, 'create'])->name('lk.act.create');
            Route::post('/store', [LkActGeneratorController::class, 'store'])->name('lk.act.store');
        });

        Route::prefix('/sbp')->group(function () {
            Route::get('/create', [LkSbpGeneratorController::class, 'create'])->name('lk.sbp.create');
            Route::post('/store', [LkSbpGeneratorController::class, 'store'])->name('lk.sbp.store');
        });
    });
});


Route::prefix('admin')->middleware('role:admin')->group(function () {
    Route::get('/', [MainController::class, 'admin'])->name('admin');

    Route::prefix('contracts')->group(function () {
        Route::get('', [ContractController::class, 'index'])->name('admin.contract.index');

        // Нужно будет перенести в ресурс
        Route::post('/{contract}/attach-performers', [ContractController::class, 'attachPerformers'])->name('admin.contract.attach-performer');
        Route::get('/{contract}', [ContractController::class, 'show'])->name('admin.contract.show');
    });

    Route::prefix('payments')->group(function () {
        Route::get('/search-contract', [PaymentController::class, 'searchContract'])->name('admin.payment.search-contract');

        Route::get('/unsorted', [PaymentController::class, 'unsorted'])->name('admin.payment.unsorted');
        Route::get('/unsorted-sbp', [PaymentController::class, 'unsortedSbp'])->name('admin.payment.unsortedSbp');

        Route::get('/{payment}/show', [PaymentController::class, 'show'])->name('admin.payment.show');
        Route::get('/{payment}/edit', [PaymentController::class, 'edit'])->name('admin.payment.edit');
        Route::patch('/{payment}', [PaymentController::class, 'update'])->name('admin.payment.update');

        Route::get('', [PaymentController::class, 'index'])->name('admin.payment.index');
    });

    Route::prefix('organizations')->group(function () {
        Route::get('', [OrganizationController::class, 'index'])->name('admin.organization.index');
        Route::get('/create', [OrganizationController::class, 'create'])->name('admin.organization.create');
        Route::get('/{organization}/edit', [OrganizationController::class, 'edit'])->name('admin.organization.edit');
        Route::get('/{organization}/show', [OrganizationController::class, 'edit'])->name('admin.organization.show');

        Route::post('/', [OrganizationController::class, 'store'])->name('admin.organization.store');
        Route::patch('/{organization}', [OrganizationController::class, 'update'])->name('admin.organization.update');
        Route::delete('/{organization}', [OrganizationController::class, 'destroy'])->name('admin.organization.destroy');

        Route::prefix('document-templates')->group(function () {
            Route::get('/', [DocumentTemplateController::class, 'index'])->name('admin.organization.document-template.index');
            Route::get('/attach', [DocumentTemplateController::class, 'attach'])->name('admin.organization.document-template.attach');
            Route::get('/{documentTemplate}/edit', [DocumentTemplateController::class, 'edit'])->name('admin.organization.document-template.edit');

            Route::post('/', [DocumentTemplateController::class, 'store'])->name('admin.organization.document-template.store');
            Route::patch('/{documentTemplate}', [DocumentTemplateController::class, 'update'])->name('admin.organization.document-template.update');
            Route::delete('/{documentTemplate}', [DocumentTemplateController::class, 'destroy'])->name('admin.organization.document-template.destroy');
        });
    });

    Route::prefix('servicies')->group(function () {
        Route::prefix('categories')->group(function () {
            Route::get('/', [ServiceCategoryController::class, 'index'])->name('admin.service.category.index');
            Route::get('/{serviceCategory}/edit', [ServiceCategoryController::class, 'edit'])->name('admin.service.category.edit');

            Route::post('/', [ServiceCategoryController::class, 'store'])->name('admin.service-category.store');
            Route::patch('/{serviceCategory}', [ServiceCategoryController::class, 'update'])->name('admin.service-category.update');
            Route::delete('/{serviceCategory}', [ServiceCategoryController::class, 'destroy'])->name('admin.service-category.destroy');
        });

        Route::get('/create', [ServiceController::class, 'create'])->name('admin.service.create');
        Route::get('/edit/{service}', [ServiceController::class, 'edit'])->name('admin.service.edit');
        Route::get('/{serviceCategory?}', [ServiceController::class, 'index'])->name('admin.service.index');

        Route::post('/', [ServiceController::class, 'store'])->name('admin.service.store');
        Route::patch('/{service}', [ServiceController::class, 'update'])->name('admin.service.update');
        Route::delete('/{service}', [ServiceController::class, 'destroy'])->name('admin.service.destroy');
    });

    Route::prefix('users')->group(function () {

        Route::prefix('employment-types')->group(function () {
            Route::get('/', [EmploymentTypeController::class, 'index'])->name('admin.employment-type.index');
            Route::post('/', [EmploymentTypeController::class, 'store'])->name('admin.employment-type.store');
            Route::delete('/{employmentType}', [EmploymentTypeController::class, 'destroy'])->name('admin.employment-type.destroy');
        });

        Route::get('/', [UserController::class, 'index'])->name('admin.user.index');
        Route::get('/create', [UserController::class, 'create'])->name('admin.user.create');
        Route::get('/{user}', [UserController::class, 'show'])->name('admin.user.show');
        Route::post('/', [UserController::class, 'store'])->name('admin.user.store');
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

        Route::get('/calls', [SaleDepartmentController::class, 'callsReport'])->name('admin.sale-department.calls');
        Route::get('/user-report', [SaleDepartmentController::class, 'userReport'])->name('admin.sale-department.user-report');

        Route::get('/plans-settings', [SaleDepartmentController::class, 'plansSettings'])->name('admin.sale-department.plans-settings');
        Route::put('/{workPlan}', [SaleDepartmentController::class, 'updateWorkPlan'])->name('admin.sale-department.work-plan.update');
        Route::delete('/{workPlan}', [SaleDepartmentController::class, 'destroyWorkPlan'])->name('admin.sale-department.work-plan.destroy');
        Route::post('/', [SaleDepartmentController::class, 'storeWorkPlan'])->name('admin.sale-department.work-plan.store');

        Route::get('/t2-settings', [SaleDepartmentController::class, 't2Settings'])->name('admin.sale-department.t2-settings');
        Route::get('/t2-load-data', [SaleDepartmentController::class, 't2LoadData'])->name('admin.sale-department.t2-load-data');
    });

    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('admin.settings.index');
        Route::get('/calendar', [SettingsController::class, 'calendar'])->name('admin.settings.calendar');
        Route::post('/calendar/change-day', [SettingsController::class, 'toggleWorkingDayType'])->name('admin.settings.calendar.change-day');
        Route::get('/finance-week', [SettingsController::class, 'financeWeek'])->name('admin.settings.finance-week');
        Route::post('/finance-week', [SettingsController::class, 'setWeeks'])->name('admin.settings.finance-week.set-weeks');
    });
});

Route::middleware('auth')->group(function () {

    Route::resource('option', OptionController::class)->only([
        'store',
        'update',
        'destroy'
    ]);

    Route::post('mass-update', [OptionController::class, 'massUpdate'])->name('option.mass-update');

    Route::resource('payment', ResourcesPaymentController::class)->only([
        'index',
        'store',
    ]);

    Route::get('payment/shortlist/{payment}', [ResourcesPaymentController::class, 'shortlist'])->name('payment.shortlist');
    Route::post('payment/shortlist/attach', [ResourcesPaymentController::class, 'shortlistAttach'])->name('payment.shortlist.attach');

    Route::resource('osdt', ResourcesOrganizationServiceDocumentTemplateController::class)->only([
        'store',
        'destroy'
    ]);
})->middleware('role:admin');
