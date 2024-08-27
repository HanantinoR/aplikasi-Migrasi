<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TahapController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/dashboard');
})->middleware('auth');


Route::group(['middleware'=> 'auth'],function(){
    //DASHBOARD
    Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard');
    //Tahap 1
    Route::get('/tahap1',[TahapController::class,'index1'])->name('tahap.1');
    route::get('/tahap1/{id}',[TahapController::class,'detail1'])->name('tahap1.detail');
    //Tahap 2
    Route::get('/tahap2',[TahapController::class,'index2'])->name('tahap.2');
    route::get('/tahap2/{id}',[TahapController::class,'detail2'])->name('tahap2.detail');
    //Tahap 3
    Route::get('/tahap3',[TahapController::class,'index3'])->name('tahap.3');
    route::get('/tahap3/{id}',[TahapController::class,'detail3'])->name('tahap3.detail');
    //Tahap 4
    Route::get('/tahap4',[TahapController::class,'index4'])->name('tahap.4');
    route::get('/tahap4/{id}',[TahapController::class,'detail4'])->name('tahap4.detail');
});

Route::get('/tables', function () {
    return view('tables');
})->name('tables')->middleware('auth');

Route::get('/signin', function () {
    return view('account-pages.signin');
})->name('signin');

Route::get('/signup', function () {
    return view('account-pages.signup');
})->name('signup')->middleware('guest');

Route::get('/sign-up', [RegisterController::class, 'create'])
    ->middleware('guest')
    ->name('sign-up');

Route::post('/sign-up', [RegisterController::class, 'store'])
    ->middleware('guest');

Route::get('/sign-in', [LoginController::class, 'create'])
    ->middleware('guest')
    ->name('sign-in');

Route::post('/sign-in', [LoginController::class, 'store'])
    ->middleware('guest');

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])
    ->middleware('guest')
    ->name('password.request');

Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])
    ->middleware('guest')
    ->name('password.reset');

Route::post('/reset-password', [ResetPasswordController::class, 'store'])
    ->middleware('guest');
