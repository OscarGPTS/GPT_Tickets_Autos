<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\DispatcherController;
use App\Http\Controllers\TestEmailController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Ruta para enviar correo de prueba (solo para validación en producción)
Route::get('/test-email', [TestEmailController::class, 'sendTestEmail']);

Route::prefix('dispatcher')->group(function () {
    // Ruta de prueba (test)
    Route::get('/test', [DispatcherController::class, 'test']);
    
    // Obtener todos los tickets (API de prueba - sin autenticación)
    Route::get('/all-tickets', [DispatcherController::class, 'getAllTickets']);
    
    // Login/Verificación de usuario y obtención de tickets
    Route::post('/login', [DispatcherController::class, 'login']);
    
    // Crear o actualizar checklist de salida (checkout)
    Route::post('/checklist/checkout', [DispatcherController::class, 'checkoutChecklist']);
    
    // Crear o actualizar checklist de entrada (checkin)
    Route::post('/checklist/checkin', [DispatcherController::class, 'checkinChecklist']);
    
    // Obtener detalle de un ticket específico
    Route::get('/ticket/{id}', [DispatcherController::class, 'getTicket']);
});

Route::prefix('user')->group(function () {
    // Obtener tickets del usuario autenticado (paginados)
    Route::post('/my-tickets', [DispatcherController::class, 'getUserTickets']);
});
