<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\API\PasswordResetController;


Route::prefix('auth')->group(function () {
    Route::post('admin/register', [AuthController::class, 'registerAdmin']);
    Route::post('admin/login', [AuthController::class, 'loginAdmin']);
    Route::post('manager/login', [AuthController::class, 'loginManager']);

    
    Route::post('password/email', [PasswordResetController::class, 'sendResetLinkEmail']);
    Route::post('password/verify', [PasswordResetController::class, 'verifyToken']);
    Route::post('password/reset', [PasswordResetController::class, 'reset']);
});



Route::middleware('auth:sanctum')->group(function () {

    Route::post('logout', [AuthController::class, 'logout']);

    
    Route::prefix('admin')->middleware('admin')->group(function () {

        
        Route::post('manager/register', [AuthController::class, 'registerManager']);
        Route::apiResource('managers', ManagerController::class)->except(['destroy']);
        Route::get('dashboard', [AdminController::class, 'dashboard']);

        Route::get('members', [AdminController::class, 'getAllMembers']);
        Route::get('tasks', [AdminController::class, 'getAllTasks']);

        Route::get('donations', [DonationController::class, 'index']);
        Route::post('donations', [DonationController::class, 'store']);
        Route::get('managers-list', [DonationController::class, 'getManagers']);
        Route::post('transfer', [DonationController::class, 'transfer']);
    });



    Route::prefix('manager')->middleware('manager')->group(function () {

        Route::apiResource('tasks', TaskController::class);

        Route::apiResource('members', MemberController::class)->except(['index']);
        Route::get('my-members', [MemberController::class, 'getManagerMembers']);

        Route::post('tasks/{task}/media', [MediaController::class, 'store']);
        Route::delete('media/{media}', [MediaController::class, 'destroy']);

        Route::get('transfers', [DonationController::class, 'managerTransfers']);
        Route::get('stats', [DonationController::class, 'managerStats']);
    });


    Route::prefix('shared')->middleware('adminOrManager')->group(function () {
        Route::get('members', [MemberController::class, 'index']);
    });
});
