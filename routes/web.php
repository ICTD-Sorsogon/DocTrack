<?php

use Illuminate\Support\Facades\Route;

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
Auth::routes();
Route::get('/', function () {
    return Redirect::to('login');
});

Route::get('/', function()
{
    return view('auth/login');
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('laravel-websockets','BeyondCode\LaravelWebSockets\Dashboard\Http\Controllers\ShowDashboard');

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');

// Route::get('/test', [App\Http\Controllers\NotificationController::class, 'test']);
