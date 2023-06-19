<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Task\TaskController;
use Illuminate\Console\View\Components\Task;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::prefix('/v1/auth')
    ->name('auth.')
    ->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/register', [AuthController::class, 'register'])->name('register');
    });


Route::prefix('/v1/tasks')
    ->name('tasks.')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/', [TaskController::class, 'getTasks'])
            ->name('index');
        Route::post('/create', [TaskController::class, 'createTask'])
            ->name('create');
        Route::patch('/{taskId}/update', [TaskController::class, 'updateTask'])
            ->name('update')
            ->whereUuid('taskId');
        Route::patch('/{taskId}/complete', [TaskController::class, 'markTaskAsCompleted'])
            ->name('complete')
            ->whereUuid('taskId');
        Route::delete('/{taskId}/delete', [TaskController::class, 'deleteTask'])
            ->name('delete')
            ->whereUuid('taskId');
    });

