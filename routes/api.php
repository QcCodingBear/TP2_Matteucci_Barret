<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


//Routes du TP2 ici :
Route::get('/films', 'App\Http\Controllers\FilmController@index');

// J'applique ici le throttling à toutes les routes (5 tentatives par minute)
Route::middleware('throttle:5,1')->group(function () {

    Route::post('/signup', 'App\Http\Controllers\AuthController@signup');
    Route::post('/signin', 'App\Http\Controllers\AuthController@signin');

    // J'ai créé un sous-groupe de middleware au cas ou on aurait besoin d'en ajouter d'autres plus tard
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/signout', 'App\Http\Controllers\AuthController@signout');
    });
});

