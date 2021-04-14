<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AccessGroupController;
use App\Http\Controllers\ComponentController;
use App\Http\Controllers\ComponentAccessController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [AuthController::class, 'unauthorized']);

Route::get('/ping', function () {
    return ['pong' => true];
});

Route::get('/401', [AuthController::class, 'unauthorized'])->name('login');

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout']);
Route::post('/auth/refresh', [AuthController::class, 'refresh']);


Route::post('/user', [UserController::class, 'createUser']);
Route::get('/users', [UserController::class, 'readAllUsers']);
Route::get('/user/{id}', [UserController::class, 'readUser']);
Route::put('/user/{id}', [UserController::class, 'updateUser']);
Route::delete('/user/{id}', [UserController::class, 'deleteUser']);


Route::post('/access-group', [AccessGroupController::class, 'createAccessGroup']);
Route::get('/access-groups', [AccessGroupController::class, 'readAllAccessGroups']);
Route::get('/access-group/{id}', [AccessGroupController::class, 'readAccessGroup']);
Route::put('/access-group/{id}', [AccessGroupController::class, 'updateAccessGroup']);
Route::delete('/access-group/{id}', [AccessGroupController::class, 'deleteAccessGroup']);


Route::post('/component', [ComponentController::class, 'createComponent']);
Route::get('/components', [ComponentController::class, 'readAllComponents']);
Route::get('/component/{id}', [ComponentController::class, 'readComponent']);
Route::put('/component/{id}', [ComponentController::class, 'updateComponent']);
Route::delete('/component/{id}', [ComponentController::class, 'deleteComponent']);


Route::post('/component-access', [ComponentAccessController::class, 'createComponentAccess']);
Route::get('/component-access', [ComponentAccessController::class, 'readAllComponentAccess']);
Route::get('/component-access/{id}', [ComponentAccessController::class, 'readComponentAccess']);
Route::put('/component-access/{id}', [ComponentAccessController::class, 'updateComponentAccess']);
Route::delete('/component-access/{id}', [ComponentAccessController::class, 'deleteComponentAccess']);
