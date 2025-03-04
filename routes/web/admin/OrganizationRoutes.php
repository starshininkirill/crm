<?php
use App\Http\Controllers\Admin\DocumentTemplateController;
use App\Http\Controllers\Admin\OrganizationServiceDocumentTemplateController;
use App\Http\Controllers\Admin\OrganizationController;
use Illuminate\Support\Facades\Route;


Route::prefix('organizations')->group(function () {
    Route::get('', [OrganizationController::class, 'index'])->name('admin.organization.index');
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

    Route::prefix('osdt')->group(function () {
        Route::post('/', [OrganizationServiceDocumentTemplateController::class, 'store'])->name('admin.osdt.store');
        Route::delete('/{osdt}', [OrganizationServiceDocumentTemplateController::class, 'destroy'])->name('admin.osdt.destroy');
    });
});