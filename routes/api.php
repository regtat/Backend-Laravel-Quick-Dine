<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DetailPesananController;
use App\Http\Controllers\KantinController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PesananController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::group(['middleware'=> ['auth:sanctum']], function(){
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/allUsers', [AuthController::class, 'users']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::delete('/users/{id}',[AuthController::class, 'destroy']);
    Route::put('/users/{id}', [AuthController::class, 'update']);

    Route::get('/users', [KantinController::class, 'getUsers']);

    Route::get('/kantin', [KantinController::class, 'index']);
    Route::get('/kantin/{id}', [KantinController::class, 'show']);
    Route::post('/kantin', [KantinController::class, 'store']);
    Route::put('/kantin/{id}', [KantinController::class, 'update']);
    Route::delete('/kantin/{id}', [KantinController::class, 'destroy']);

    Route::get('/kantin/{id_kantin}/menu', [MenuController::class, 'index']);
    Route::get('/kantin/{id_kantin}/menu/{id}', [MenuController::class, 'show']);
    Route::post('/kantin/{id_kantin}/menu', [MenuController::class, 'store']);
    Route::put('/kantin/{id_kantin}/menu/{id}', [MenuController::class, 'update']);
    Route::delete('/kantin/{id_kantin}/menu/{id}', [MenuController::class, 'destroy']);

    //daftar pesanan berdasarkan role
    Route::get('/pesanan', [PesananController::class, 'index']);
    Route::get('/kantin/{id_kantin}/pesanan', [PesananController::class, 'index']);
    Route::get('/pesanan/{id}', [PesananController::class, 'show']);
    Route::post('/kantin/{id_kantin}/pesanan', [PesananController::class, 'store']);
    Route::put('/pesanan/{id}/status', [PesananController::class, 'updateStatus']);
    Route::delete('/pesanan/{id}', [PesananController::class, 'destroy']);

    Route::get('/pesanan/{id_pesanan}/detail', [DetailPesananController::class, 'index']);
    Route::get('/pesanan/{id_pesanan}/detail/{id}', [DetailPesananController::class, 'show']);
    Route::post('/pesanan/{id_pesanan}/detail', [DetailPesananController::class, 'store']);
    Route::delete('/pesanan/{id_pesanan}/detail/{id}', [DetailPesananController::class, 'destroy']);
});

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
