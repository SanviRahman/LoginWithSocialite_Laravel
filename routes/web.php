<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Front\FrontController;
use App\Http\Controllers\Google\GoogleController;


Route::get('/',[FrontController::class,'home'])->name('home');
Route::get('/about',[FrontController::class,'about'])->name('about');

//User
Route::middleware('user')->group(function(){
    Route::get('/user/dashboard',[UserController::class,'dashboard'])->name('dashboard');
    Route::get('/user/profile',[UserController::class,'profile'])->name('profile');
    Route::post('/user/profile',[UserController::class,'profile_submit'])->name('profile_submit');
    Route::get('/user/logout',[UserController::class,'logout'])->name('logout');
});

Route::get('/user/registration',[UserController::class,'registration'])->name('registration');
Route::post('/user/registration',[UserController::class,'registration_submit'])->name('registration_submit');
Route::get('/user/registration_verify/{token}/{email}',[UserController::class,'registration_verify'])->name('registration_verify');
Route::get('/user/login',[UserController::class,'login'])->name('user_login');
Route::post('/user/login',[UserController::class,'login_submit'])->name('login_submit');
Route::get('/user/forget_password',[UserController::class,'forget_password'])->name('forget_password');
Route::post('/user/forget_password',[UserController::class,'forget_password_submit'])->name('forget_password_submit');
Route::get('/user/reset_password/{token}/{email}',[UserController::class,'reset_password'])->name('reset_password');
Route::post('/user/reset_password/{token}/{email}',[UserController::class,'reset_password_submit'])->name('reset_password_submit');


//User login with google
Route::get('/user/authorized/google',[GoogleController::class,'redirectToGoogle']);
Route::get('/user/authorized/google/callback',[GoogleController::class,'handleGoogleCallback']);

//Admin
Route::middleware('admin')->prefix('admin')->group(function(){
    Route::get('/admin/dashboard',[AdminController::class,'admin_dashboard'])->name('admin_dashboard');
    Route::get('/admin/profile',[AdminController::class,'admin_profile'])->name('admin_profile');
    Route::post('/admin/profile',[AdminController::class,'admin_profile_submit'])->name('admin_profile_submit');
    Route::get('/admin/logout',[AdminController::class,'admin_logout'])->name('admin_logout');
});

Route::get('/admin/login',[AdminController::class,'admin_login'])->name('admin_login');
Route::post('/admin/login',[AdminController::class,'admin_login_submit'])->name('admin_login_submit');
Route::get('/admin/forget_password',[AdminController::class,'admin_forget_password'])->name('admin_forget_password');
Route::post('/admin/forget_password',[AdminController::class,'admin_forget_password_submit'])->name('admin_forget_password_submit');
Route::get('/admin/reset_password/{token}/{email}',[AdminController::class,'admin_reset_password'])->name('admin_reset_password');
Route::post('/admin/reset_password/{token}/{email}',[AdminController::class,'admin_reset_password_submit'])->name('admin_reset_password_submit');
Route::fallback(function(){
    return view('errors.404');
});