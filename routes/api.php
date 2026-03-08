<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController; // <-- Nota: abbiamo aggiunto V1 qui!

// Raggruppiamo tutte le rotte sotto il prefisso "v1"
Route::prefix('v1')->group(function () {

    // L'indirizzo diventerà: http://fumetti-api.locale.it/api/v1/login
    Route::post('/login', [AuthController::class, 'login']);

    // Rotte protette dal Token
    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        // Qui in futuro metteremo: Route::get('/albi', [AlboController::class, 'index']);
    });
});
