<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\TravelZoneController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WhatsAppController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // MSIB App Routes
    Route::resource('requests', RequestController::class);
    Route::resource('approvals', ApprovalController::class)->only(['index', 'show', 'update']);

    // Admin & Extra
    Route::middleware(['permission:settings.manage'])->group(function () {
        Route::get('/settings/policies', [SettingsController::class, 'policies'])->name('settings.policies');
        Route::get('/settings/flows', [SettingsController::class, 'flows'])->name('settings.flows');
        Route::post('/settings/flows', [SettingsController::class, 'storeFlow'])->name('settings.flows.store');
        Route::get('/settings/flows/{flow}/edit', [SettingsController::class, 'editFlow'])->name('settings.flows.edit');
        Route::put('/settings/flows/{flow}', [SettingsController::class, 'updateFlow'])->name('settings.flows.update');
        Route::delete('/settings/flows/{flow}', [SettingsController::class, 'destroyFlow'])->name('settings.flows.destroy');
        Route::get('/settings/permissions', [SettingsController::class, 'permissions'])->name('settings.permissions');
        Route::post('/settings/permissions', [SettingsController::class, 'updatePermissions'])->name('settings.permissions.update');
        Route::resource('settings/roles', RoleController::class)->names('settings.roles');
        Route::resource('settings/users', UserManagementController::class)->names('settings.users');
        Route::resource('settings/divisions', DivisionController::class)->names('settings.divisions');
        Route::resource('settings/policies', PolicyController::class)->names('settings.policies');
        Route::resource('settings/travel-zones', TravelZoneController::class)->names('settings.travel-zones');

        // WhatsApp Gateway
        Route::get('/settings/whatsapp', [WhatsAppController::class, 'index'])->name('settings.whatsapp');
        Route::get('/settings/whatsapp/status', [WhatsAppController::class, 'status'])->name('settings.whatsapp.status');
        Route::post('/settings/whatsapp/start', [WhatsAppController::class, 'start'])->name('settings.whatsapp.start');
        Route::post('/settings/whatsapp/terminate', [WhatsAppController::class, 'terminate'])->name('settings.whatsapp.terminate');
        Route::post('/settings/whatsapp/test-send', [WhatsAppController::class, 'testSend'])->name('settings.whatsapp.test-send');
    });

    Route::middleware(['permission:reports.view'])->group(function () {
        Route::get('/reports/export', [ReportController::class, 'exportCsv'])->name('reports.export');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
    });
});

require __DIR__ . '/auth.php';
