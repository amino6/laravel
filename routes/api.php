<?php

use App\Http\Controllers\Api\DesignController;
use App\Http\Controllers\Auth\RegisterController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/',function() {
    return response()->json(['msg' => 'YAY!!!'],200);
});

// only authenticated users
Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::get('me',function() {
        return auth()->user();
    });
    Route::get('/users',function() {
        return response()->json(['users' => User::all()],200);
    });
    Route::apiResource('designs',DesignController::class);
});

// only guest users
Route::group(['middleware' => ['guest:sanctum']], function() {
    Route::get('/users-login',function() {
        return response()->json(['page' => 'login page'],200);
    });
});
