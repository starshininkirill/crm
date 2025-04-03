<?php

use App\Http\Controllers\Admin\Organization\DocumentSelectionRuleController;
use App\Http\Controllers\Admin\Organization\DocumentTemplateController;
use App\Http\Controllers\Admin\Organization\OrganizationController;
use Illuminate\Support\Facades\Route;


Route::prefix('organizations')->group(function () {
    Route::prefix('document-templates')->group(function () {
        Route::get('/', [DocumentTemplateController::class, 'index'])->name('admin.document-template.index');
        Route::get('/{documentTemplate}/edit', [DocumentTemplateController::class, 'edit'])->name('admin.document-template.edit');
        Route::get('/{documentTemplate}', [DocumentTemplateController::class, 'edit'])->name('admin.document-template.show');

        Route::post('/', [DocumentTemplateController::class, 'store'])->name('admin.document-template.store');
        Route::patch('/{documentTemplate}', [DocumentTemplateController::class, 'update'])->name('admin.document-template.update');
        Route::delete('/{documentTemplate}', [DocumentTemplateController::class, 'destroy'])->name('admin.document-template.destroy');
    });

    Route::prefix('document-selection-rule')->group(function(){
        Route::get('/', [DocumentSelectionRuleController::class, 'index'])->name('admin.document-selection-rule.index');
        Route::post('/', [DocumentSelectionRuleController::class, 'store'])->name('admin.document-selection-rule.store');
        Route::get('/check', [DocumentSelectionRuleController::class, 'check'])->name('admin.document-selection-rule.check');
        Route::delete('/{documentSelectionRule}', [DocumentSelectionRuleController::class, 'destroy'])->name('admin.document-selection-rule.destroy');
    });

    Route::get('/{organization}/edit', [OrganizationController::class, 'edit'])->name('admin.organization.edit');
    Route::get('/{organization}', [OrganizationController::class, 'edit'])->name('admin.organization.show');
    Route::get('', [OrganizationController::class, 'index'])->name('admin.organization.index');

    Route::post('/', [OrganizationController::class, 'store'])->name('admin.organization.store');
    Route::patch('/{organization}', [OrganizationController::class, 'update'])->name('admin.organization.update');
    Route::delete('/{organization}', [OrganizationController::class, 'destroy'])->name('admin.organization.destroy');


});