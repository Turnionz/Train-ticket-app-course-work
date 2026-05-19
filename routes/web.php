<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CrewController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\TrainController;
use App\Http\Controllers\TripController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => to_route('trips.index'));

Route::resource('trains', TrainController::class);
Route::resource('trips', TripController::class);

Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::get('register', [AuthController::class, 'create'])->name('register');

Route::post('login', [AuthController::class, 'login'])->name('auth.login');
Route::post('register', [AuthController::class, 'store'])->name('auth.register');

Route::delete('logout', fn() => to_route('auth.destroy'))->name('logout');
Route::delete('auth', [AuthController::class, 'destroy'])->name('auth.destroy');

Route::resource('employees', EmployeeController::class);

Route::resource('crews', CrewController::class);
