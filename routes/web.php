<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ControlController;
use App\Http\Controllers\MQTTController;
use App\Http\Controllers\DeviceHistoryController;
use App\Http\Controllers\SensorDataController;
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

Route::get('/', function () {return redirect('sign-in');})->middleware('guest');
Route::get('/home', function () {return redirect('dashboard');})->middleware('auth');
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');
Route::get('sign-up', [RegisterController::class, 'create'])->middleware('guest')->name('register');
Route::post('sign-up', [RegisterController::class, 'store'])->middleware('guest');
Route::get('sign-in', [SessionsController::class, 'create'])->middleware('guest')->name('login');
Route::post('sign-in', [SessionsController::class, 'store'])->middleware('guest');
Route::post('verify', [SessionsController::class, 'show'])->middleware('guest');

Route::group(['middleware' => 'auth'], function () {
	// user route
	Route::post('sign-out', [SessionsController::class, 'destroy'])->middleware('auth')->name('logout');
	Route::get('profile', [ProfileController::class, 'create'])->middleware('auth')->name('profile');
	Route::post('user-profile', [ProfileController::class, 'update'])->middleware('auth');
	Route::get('user-management', [ProfileController::class, 'index'])->name('user-management');
	Route::get('user-profile', function () {
		return view('pages.user.user-profile');
	})->name('user-profile');
	Route::post('/store', [UserController::class, 'store'])->name('store');
	Route::post('/update', [UserController::class, 'update'])->name('update');
	Route::delete('/delete', [UserController::class, 'destroy'])->name('delete');
	Route::get('change-password', function () {
		return view('pages.user.change-password');
	})->name('change-password');
	Route::post('/change-password', [UserController::class, 'changePassword']);
	
	Route::get('static-sign-in', function () {
		return view('pages.static-sign-in');
	})->name('static-sign-in');
	Route::get('static-sign-up', function () {
		return view('pages.static-sign-up');
	})->name('static-sign-up');

});

Route::post('/control/toggle-device', [ControlController::class, 'toggleDevice']);
Route::post('/control-device/{id}', [DeviceController::class, 'toggle']);

// Route::get('/device-history', [DeviceHistoryController::class, 'index'])->name('device.history');
Route::get('/device-history', [DeviceHistoryController::class, 'index'])->name('device-history.index');

Route::get('/sensor-data', [SensorDataController::class, 'index'])->name('sensor.data.index');
Route::get('/sensor-data/latest', [SensorDataController::class, 'getLatestData'])->name('sensor.data.latest');
Route::get('/sensor-data/filter', [SensorDataController::class, 'filterData'])->name('sensor.data.filter');