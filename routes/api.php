<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TodoController;
use App\Http\Middleware\CheckTokenVersion;

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
});


//Route::middleware([CheckTokenVersion::class])->group(function () {
    Route::controller(TodoController::class)->group(function () {
        Route::get('customers/total', 'customerCount');
        Route::get('customers/infos/{codeCliente}', 'customerInfos');
        Route::post('customers/create', 'customerCreate');
        Route::get('cuentas/infos/{codAgencia}/{numeroCompte}', 'cuentaInfos');
        Route::post('cuentas/debit', 'debitCompte');
        Route::post('cuentas/credit', 'creditCompte');
        Route::post('cuentas/transfer', 'makeTransfer');
        Route::post('cuentas/funds/reserve', 'reservationDeFonds');
        Route::post('cuentas/funds/unreserve', 'unReservationDeFonds');
    });
//});
