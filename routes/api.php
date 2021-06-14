<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UsersController;

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

Route::group([
    'prefix' => 'v1'
], function () {

    // Login route
    Route::post('login', [AuthController::class, 'login']);
    Route::post('reg', [AuthController::class, 'register']);


    Route::group([
        'middleware' => ['auth:sanctum']
    ], function () {

        Route::post('logout', [AuthController::class, 'logout']);


        Route::apiResources([
            'users' => UsersController::class,
        ]);
    });
});
