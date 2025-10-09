<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

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

// SPA login/logout on web middleware
Route::post('/login',  [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout']);

// Optional: avoid 404 on GET /login in browsers
Route::get('/login', fn () => response()->json(['message' => 'Use POST /login'], 404));

Route::get('/_debug/csrf', function (\Illuminate\Http\Request $r) {
    return response()->json([
        'csrf_token()' => csrf_token(),                    // what Laravel expects
        'XSRF-TOKEN cookie (raw)' => $r->cookie('XSRF-TOKEN'), // URL-encoded cookie value
        'decoded XSRF cookie' => optional($r->cookie('XSRF-TOKEN'), fn($v) => urldecode($v)),
        'has_session' => $r->hasSession(),
        'session_id'  => $r->session()->getId(),
    ]);
});
