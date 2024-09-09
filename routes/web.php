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

route::get('/', function () {
    return redirect('/dashboard');
})->middleware('auth');


route::group(['middleware'=> 'auth'],function(){
    //DASHBOARD
    route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard');
    //Tahap 1
    route::get('/tahap1',[TahapController::class,'index1'])->name('tahap.1');
    route::get('/tahap1/{id_proposal}',[TahapController::class,'detail1'])->name('tahap1.detail');
    route::post('/tahap1/post/rekon',[TahapController::class,'post_rekon_tahap_1'])->name('rekon_tahap_1');

    //Tahap 2
    route::get('/tahap2',[TahapController::class,'index2'])->name('tahap.2');
    route::get('/tahap2/{id_proposal}',[TahapController::class,'detail2'])->name('tahap2.detail');
    route::get('/tahap2/dokumen_pekebun/{id_pekebun}/{id_proposal}',[TahapController::class,'dokumen_pekebun_2'])->name('tahap2.dokumen_pekebun');
    route::post('/tahap2/dokumen_pekebun/post',[TahapController::class,'post_dokumen_pekebun_2'])->name('tahap2.post_dokumen_pekebun');
    route::get('/tahap2/legalitas_pekebun/{id_legalitas_pekebun}',[TahapController::class,'legalitas_pekebun_2'])->name('tahap2.legalitas_pekebun');

    //Tahap 3
    route::get('/tahap3',[TahapController::class,'index3'])->name('tahap.3');
    route::get('/tahap3/{id}',[TahapController::class,'detail3'])->name('tahap3.detail');
    //Tahap 4
    route::get('/tahap4',[TahapController::class,'index4'])->name('tahap.4');
    route::get('/tahap4/{id_proposal}',[TahapController::class,'detail4'])->name('tahap4.detail');

});

route::get('/tables', function () {
    return view('tables');
})->name('tables')->middleware('auth');

route::get('/signin', function () {
    return view('account-pages.signin');
})->name('signin');

route::get('/signup', function () {
    return view('account-pages.signup');
})->name('signup')->middleware('guest');

route::get('/sign-up', [RegisterController::class, 'create'])
    ->middleware('guest')
    ->name('sign-up');

route::post('/sign-up', [RegisterController::class, 'store'])
    ->middleware('guest');

route::get('/sign-in', [LoginController::class, 'create'])
    ->middleware('guest')
    ->name('sign-in');

route::post('/sign-in', [LoginController::class, 'store'])
    ->middleware('guest');

route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

route::get('/forgot-password', [ForgotPasswordController::class, 'create'])
    ->middleware('guest')
    ->name('password.request');

route::post('/forgot-password', [ForgotPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])
    ->middleware('guest')
    ->name('password.reset');

route::post('/reset-password', [ResetPasswordController::class, 'store'])
    ->middleware('guest');
