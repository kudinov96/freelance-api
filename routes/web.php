<?php

use App\Http\Controllers\FacebookController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function (\App\Services\FacebookApiService $facebookApiService) {
    dd($facebookApiService->lead("165170452849953"));

    return view('welcome');
});

Route::get("facebook/webhook", [FacebookController::class, "setWebhook"]);
Route::post("facebook/webhook", [FacebookController::class, "webhook"]);
