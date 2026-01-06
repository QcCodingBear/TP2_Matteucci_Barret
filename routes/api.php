<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckIfAdmin;

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

// J'applique ici le throttling à toutes les routes (60 tentatives par minute)
Route::middleware('throttle:60,1', 'auth:sanctum')->group(function () {

    Route::post('/signout', 'App\Http\Controllers\AuthController@logout');

    Route::post('/critics', 'App\Http\Controllers\CriticController@store');

    Route::get('/users/{id}', 'App\Http\Controllers\UserController@getById');

    Route::patch('/users/{id}/password', 'App\Http\Controllers\UserController@updatePassword');

        Route::middleware(CheckIfAdmin::class)->group(function () {

        Route::post('/films', 'App\Http\Controllers\FilmController@store');

        Route::put('/films/{id}', 'App\Http\Controllers\FilmController@update');

        Route::delete('/films/{id}', 'App\Http\Controllers\FilmController@destroy');
    });
});
