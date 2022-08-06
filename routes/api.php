<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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




//user
Route::middleware(['auth:sanctum','return-json','is_admin'])->group(function (){
    Route::get('users', [UserController::class, 'All']);
    Route::post('users/register-admin', [UserController::class, 'RegisterAdmin']);
    Route::get('users/pending-changes', [UserController::class, 'PendingChanges']);
    Route::post('users/initiate-change/{id}', [UserController::class, 'InitiateUserUpdate']);
    Route::post('users/complete-change/{id}', [UserController::class, 'CompleteUserUpdate']);
});

//auth
Route::middleware(['return-json'])->group(function (){
    Route::post('register', [AuthController::class, 'Register'])->middleware('return-json');
    Route::post('login', [AuthController::class, 'Login'])->middleware('return-json');
});


Route::middleware(['auth:sanctum','return-json'])->group(function (){
    Route::post('logout', [AuthController::class, 'Logout']);
});
