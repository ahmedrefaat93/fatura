<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PersmissionController;
use App\Http\Controllers\Api\RoleController;


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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

/**
 * login, reqister are apis for guest and logout for logged user, middleware assigned in constructor
 */
Route::group(['middleware' => 'api','prefix' => 'auth'], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);

});

/**
 * theses apis for super admin only
 */
Route::group(['middleware' => ['api','auth:api','is_super_admin']], function () {
    Route::get('/permissions', [PersmissionController::class, 'index']);
    Route::post('/permission/create', [PersmissionController::class, 'store']);
    Route::post('/permission/assign', [PersmissionController::class, 'assign']);
    Route::post('/permission/can', [PersmissionController::class, 'check']);

    Route::get('/roles',[RoleController::class,'index']);
    Route::post('/role/create',[RoleController::class,'store']);
    Route::post('/role/assign', [RoleController::class, 'assign']);
    Route::post('/role/can', [RoleController::class, 'check']);

});

/**
 * this apis to check role/permission for logged user
 */
Route::group(['middleware' => ['api','auth:api'],'prefix' => 'authed'], function () {

    Route::post('/permission/can', [PersmissionController::class, 'checkForAuthorize']);
    Route::post('/role/can', [RoleController::class, 'checkForAuthorize']);
});
