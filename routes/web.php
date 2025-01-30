<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController; 
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Definicion de rutas ViewsAuth
Route::get("/", [AuthController::class,"showLoginForm"]);
Route::get("/login", [AuthController::class,"showLoginForm"])->name('login');
Route::get("/register", [AuthController::class,"showRegisterForm"]);
Route::get("/verifyCode", [AuthController::class,"showVerifyCodeForm"]);

//Definicion de rutas Auth
Route::post('loginUser', [AuthController::class,'login'])->name('loginUser');
Route::post('verifyCodeUser', [AuthController::class,'verificationCode'])->name('verifyCodeUser');
Route::post('resendCode', [AuthController::class, 'resendVerifyCode'])->name('resendCode');
Route::post('registerUser', [AuthController::class,'register'])->name('registerUser');
Route::get('logout', [AuthController::class,'logout'])->name('logout');
Route::get('activateAccount/{id_user}', [AuthController::class,'activateAccount'])->name('activateAccount')
->where('id_user', '[0-9]+');

//Definicion de rutas de la app con autenticacion
Route::middleware(['auth', 'isActive'])->group(function () {
    Route::get('/home', [AuthController::class,'showHome'])->name('home');
});
