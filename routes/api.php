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
    Route::post('/signup', 'App\Http\Controllers\AuthController@register');
    Route::post('/signin', 'App\Http\Controllers\AuthController@login');
});

// J'ai créé un groupe sur ce middleware au cas ou on aurait besoin d'en ajouter d'autres plus tard (Partie 2 par exemple)
Route::middleware('auth:sanctum', 'throttle:60,1')->group(function () {
    Route::post('/signout', 'App\Http\Controllers\AuthController@logout');
    Route::post('/critics', 'App\Http\Controllers\CriticController@store');
    Route::get('/users/{id}', 'App\Http\Controllers\UserController@getById');
});
