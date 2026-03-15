<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController; // <-- Nota: abbiamo aggiunto V1 qui!
use App\Http\Controllers\Api\V1\AlboController;
use App\Http\Controllers\Api\V1\EditoreController;
use App\Http\Controllers\Api\V1\AutoreController;
use App\Http\Controllers\Api\V1\StoriaController;
use App\Http\Controllers\Api\V1\CollanaController;
use App\Http\Controllers\Api\V1\RuoloController;
use App\Http\Controllers\Api\V1\AlboLettureController;
use App\Http\Controllers\Api\V1\StoriaLettureController;
use App\Http\Controllers\Api\V1\DashboardController;

// Raggruppiamo tutte le rotte sotto il prefisso "v1"
Route::prefix('v1')->group(function () {

    // L'indirizzo diventerà: http://fumetti-api.locale.it/api/v1/login
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    // Rotte protette dal Token
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        // Qui in futuro metteremo: Route::get('/albi', [AlboController::class, 'index']);
        Route::get('/albi', [AlboController::class, 'index']);
        Route::post('/albi', [AlboController::class, 'store']);
        Route::put('/albi/{id}', [AlboController::class, 'update']);
        Route::get('/albi/{id}', [AlboController::class, 'show']);
        Route::delete('/albi/{id}', [AlboController::class, 'destroy']);

        // ... rotte degli albi che avevamo già fatto ...

        Route::get('/editori/lista', [EditoreController::class, 'lista']);
        Route::get('/collane/lista', [CollanaController::class, 'lista']);
        Route::get('/autori/lista', [AutoreController::class, 'lista']);
        Route::get('/storie/lista', [StoriaController::class, 'lista']);
        Route::get('/ruoli/lista', [RuoloController::class, 'lista']);

        Route::get('/storie/{id}', [StoriaController::class, 'show']);

        // I 5 CRUD completi per le tabelle satellite!
        Route::apiResource('editori', EditoreController::class);
        Route::apiResource('autori', AutoreController::class);
        Route::apiResource('storie', StoriaController::class);
        Route::apiResource('collane', CollanaController::class);
        Route::apiResource('ruoli', RuoloController::class);

        // Letture Albi
        Route::get('/albi/{alboId}/letture', [AlboLettureController::class, 'index']);
        Route::post('/albi/{alboId}/letture', [AlboLettureController::class, 'store']);
        Route::delete('/albi/{alboId}/letture/{data_lettura}', [AlboLettureController::class, 'destroy']);

        // Letture Storie
        Route::get('/storie/{storiaId}/letture', [StoriaLettureController::class, 'index']);
        Route::post('/storie/{storiaId}/letture', [StoriaLettureController::class, 'store']);
        Route::delete('/storie/{storiaId}/letture/{data_lettura}', [StoriaLettureController::class, 'destroy']);

        Route::get('/dashboard', [DashboardController::class, 'index']);
    });
});
