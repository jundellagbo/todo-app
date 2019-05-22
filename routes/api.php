<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Api for Authentication
Route::group([
    "prefix" => "auth"
], function() {
    Route::post("/login", "AuthController@login");
    Route::post("/register", "AuthController@register");
    // Registered Users
    Route::group([
        "middleware" => "auth:api"
    ], function() {
        Route::get("user", "AuthController@user");
        Route::get("logout", "AuthController@logout");
    });
});

Route::group([
    "prefix" => "todos",
    "middleware" => "auth:api"
], function() {
    Route::get("/", "TodoController@index");
    Route::post("/store", "TodoController@store");
    Route::get("/remove/{id}", "TodoController@remove");
    Route::post("/set-status", "TodoController@set_status");
});