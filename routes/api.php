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
        Route::get('customers/total', 'customerCountService');
        Route::get('customers/infos/{codeCliente}', 'customerInfosService');
        Route::get('cuentas/infos/{codAgencia}/{numeroCompte}', 'cuentaInfos');
        Route::get('todos', 'index');
        Route::post('todo', 'store');
        Route::get('todo/{id}', 'show');
        Route::put('todo/{id}', 'update');
        Route::delete('todo/{id}', 'destroy');
    });
//});
