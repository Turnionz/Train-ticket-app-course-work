<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CrewController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\StationController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TrainController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\WagonController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => to_route('trips.index'));

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('register', [AuthController::class, 'create'])->name('register');

    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('register', [AuthController::class, 'store'])->name('auth.register');
});

Route::get('/trips', [TripController::class, 'index'])->name('trips.index');
Route::post('/trips/create', [TripController::class, 'tripCreate'])->name('trips.tripCreate');
Route::get('/trips/{trip}', [TripController::class, 'show'])->name('trips.show')->whereNumber('trip');

Route::middleware('auth')->group(function () {

    Route::delete('logout', [AuthController::class, 'destroy'])->name('logout');
    Route::delete('auth', [AuthController::class, 'destroy'])->name('auth.destroy');

    Route::resource('trains', TrainController::class);
    Route::resource('trips', TripController::class)->except(['index', 'show']);
    Route::resource('employees', EmployeeController::class);
    Route::resource('crews', CrewController::class);
    Route::resource('wagons', WagonController::class);
    Route::resource('stations', StationController::class);
    Route::resource('routes', RouteController::class);
    Route::resource('tickets', TicketController::class);

    Route::post('/trips/{trip}/details', [TripController::class, 'details'])->name('trips.details');
    Route::post('/trips/buy', [TicketController::class, 'buy'])->name('trips.buy');
    Route::post('/trips/payment', [TicketController::class, 'store'])->name('trips.payment');
    Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])->name('tickets.destroy');

    Route::delete('deregNeighbour/{station}', [StationController::class, 'deregisterNeighbour'])->name('deregNeighbour');
    Route::put('registerNeighbour/{station}', [StationController::class, 'registerNeighbour'])->name('registerNeighbour');
});
