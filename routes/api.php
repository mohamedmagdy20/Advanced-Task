<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
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

Route::group(['controller'=>AuthController::class],function(){
    Route::post('login','login');
    Route::post('register','register');
});

// protectec Endpoints
Route::group(['middleware'=>'auth:sanctum'],function(){
    
    // logout
    Route::delete('logout',[AuthController::class,'logout']);

    Route::group(['controller'=>TaskController::class,'prefix'=>'tasks'],function(){
        Route::get('/','index')->name('tasks.index');
        Route::post('create','store')->name('tasks.store');
        Route::patch('update/{id}','update')->name('tasks.update');
        Route::patch('{id}/status','updateStatus')->name('tasks.update-status');
        Route::delete('/{id}','destroy')->name('tasks.destroy');
        Route::delete('force_delete/{id}','forceDelete')->name('tasks.force_delete');
        Route::get('trached','trashedData')->name('tasks.trached');
        Route::get('restore/{id}','restore')->name('tasks.restore');
    });
});
