<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\IzinController;
use App\Http\Controllers\Api\GeofencePlantApiController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::apiResource('geofence-plant', GeofencePlantApiController::class)
    ->names('api.geofence-plant')
    ->parameters(['geofence-plant' => 'geofence_plant']);
Route::post('/absensi/save-photo', [AttendanceController::class, 'savePhotoFromBackend']);
// Route::post('/izin/upload-file', [IzinController::class, 'uploadFile'])->name('izin.upload');
Route::post('/izin/upload-file', [IzinController::class, 'uploadFile'])->name('izin.upload.api');
