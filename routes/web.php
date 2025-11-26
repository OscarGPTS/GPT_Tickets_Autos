<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\DriverLicenseController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehicleDocumentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes (Google OAuth2)
|--------------------------------------------------------------------------
*/

Route::get('/', [LoginController::class, 'index'])->name('login');
Route::get('/login/google', [LoginController::class, 'redirectToProvider'])->name('login.redirect');
Route::get('/auth/google/callback', [LoginController::class, 'handleProviderCallback'])->name('login.callback');
Route::get('/logout', [LoginController::class, 'logout'])->name('login.logout');

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    /*
    |--------------------------------------------------------------------------
    | Tickets Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/', [TicketController::class, 'index'])->name('index');
        Route::get('/create', [TicketController::class, 'create'])->name('create');
        Route::post('/', [TicketController::class, 'store'])->name('store');
        Route::get('/{ticket}', [TicketController::class, 'show'])->name('show');
        Route::get('/{ticket}/edit', [TicketController::class, 'edit'])->name('edit');
        Route::put('/{ticket}', [TicketController::class, 'update'])->name('update');
        
        // Acciones de aprobación/rechazo (solo encargados)
        Route::post('/{ticket}/approve', [TicketController::class, 'approve'])->name('approve');
        Route::post('/{ticket}/reject', [TicketController::class, 'reject'])->name('reject');
        
        // Asignar despachador (solo encargados)
        Route::post('/{ticket}/assign-dispatcher', [TicketController::class, 'assignDispatcher'])->name('assign.dispatcher');
        
        // Calificación del servicio (solo usuarios)
        Route::get('/{ticket}/rate', function ($ticket) {
            return view('tickets.rate', compact('ticket'));
        })->name('rate');
        Route::post('/{ticket}/rate', [TicketController::class, 'rate'])->name('rate.store');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Checklist Routes (Checkout/Checkin)
    |--------------------------------------------------------------------------
    */
    Route::prefix('checklists')->name('checklists.')->group(function () {
        Route::get('/{ticket}/checkout', [ChecklistController::class, 'checkoutForm'])->name('checkout');
        Route::post('/{ticket}/checkout', [ChecklistController::class, 'processCheckout'])->name('checkout.store');
        Route::get('/{ticket}/checkout/view', [ChecklistController::class, 'viewCheckout'])->name('checkout.view');
        Route::get('/{ticket}/checkin', [ChecklistController::class, 'checkinForm'])->name('checkin');
        Route::post('/{ticket}/checkin', [ChecklistController::class, 'processCheckin'])->name('checkin.store');
        Route::get('/{ticket}/checkin/view', [ChecklistController::class, 'viewCheckin'])->name('checkin.view');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Vehicles Routes (CRUD - solo encargados)
    |--------------------------------------------------------------------------
    */
    Route::resource('vehicles', VehicleController::class);
    
    /*
    |--------------------------------------------------------------------------
    | Vehicle Documents Routes (CRUD - solo encargados)
    |--------------------------------------------------------------------------
    */
    Route::prefix('vehicles/{vehicle}/documents')->name('vehicle-documents.')->group(function () {
        Route::get('/', [VehicleDocumentController::class, 'index'])->name('index');
        Route::get('/create', [VehicleDocumentController::class, 'create'])->name('create');
        Route::post('/', [VehicleDocumentController::class, 'store'])->name('store');
        Route::get('/{document}', [VehicleDocumentController::class, 'show'])->name('show');
        Route::get('/{document}/edit', [VehicleDocumentController::class, 'edit'])->name('edit');
        Route::put('/{document}', [VehicleDocumentController::class, 'update'])->name('update');
        Route::delete('/{document}', [VehicleDocumentController::class, 'destroy'])->name('destroy');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Driver Licenses Routes (CRUD - solo encargados)
    |--------------------------------------------------------------------------
    */
    Route::resource('driver-licenses', DriverLicenseController::class);
    
    /*
    |--------------------------------------------------------------------------
    | Users Routes (CRUD - solo admin/encargados)
    |--------------------------------------------------------------------------
    */
    Route::resource('users', UserController::class);
    
    /*
    |--------------------------------------------------------------------------
    | Admin Panel Routes (solo encargados)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:encargado'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/export', [AdminDashboardController::class, 'export'])->name('dashboard.export');
        
        Route::get('/users', function () {
            return view('admin.users.index');
        })->name('users.index');
        
        Route::get('/reports', function () {
            return view('admin.reports.index');
        })->name('reports.index');
    });
});

