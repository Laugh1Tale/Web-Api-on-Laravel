<?php

use App\Http\Controllers\PaymentRequestController;
use App\Http\Controllers\PaymentScheduleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MortgageProgramController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/authentication/register',  [AuthController::class, 'register']);
Route::post('/authentication/login', [AuthController::class, 'login'])->name('login');

Route::middleware(['auth:api', 'role'])->group(function() {
    Route::middleware(['scope:moderator'])->
        post('/mortgage-programs', [MortgageProgramController::class, 'createMortgageProgram']);
    Route::middleware(['scope:admin,moderator,basic'])->
        get('/mortgage-programs', [MortgageProgramController::class, 'getAllMortgagePrograms']);
    Route::middleware(['scope:admin,admin,moderator,basic'])->
        get('/mortgage-programs/{id}', [MortgageProgramController::class, 'getMortgageProgramById']);
    Route::middleware(['scope:moderator'])->
        post('/mortgage-programs/{id}', [MortgageProgramController::class, 'updateMortgageProgramById']);
    Route::middleware(['scope:admin,moderator'])->
        delete('/mortgage-programs/{id}', [MortgageProgramController::class, 'deleteMortgageProgramById']);


    Route::middleware(['scope:admin,moderator,basic'])->
        post('/payment-requests', [PaymentRequestController::class, 'makePaymentRequest']);


    Route::middleware(['scope:admin,moderator,basic'])->
    post('/payment-schedule/{id}', [PaymentScheduleController::class, 'makePaymentSchedule']);


    Route::middleware(['scope:admin'])->
        get('/users/{userId}', [UserController::class, 'show']);
    Route::middleware(['scope:admin'])->
        post('/users/{userId}', [UserController::class, 'changeRole']);
});
