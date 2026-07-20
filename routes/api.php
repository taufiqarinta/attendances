<?php

use App\Http\Controllers\Api\GeofencePlantApiController;
use App\Http\Controllers\Api\ParticipantOrientationController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\IzinController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::apiResource('geofence-plant', GeofencePlantApiController::class)
    ->names('api.geofence-plant')
    ->parameters(['geofence-plant' => 'geofence_plant']);
Route::post('/absensi/save-photo', [AttendanceController::class, 'savePhotoFromBackend']);
// Route::post('/izin/upload-file', [IzinController::class, 'uploadFile'])->name('izin.upload');
Route::post('/izin/upload-file', [IzinController::class, 'uploadFile'])->name('izin.upload.api');


/*
|--------------------------------------------------------------------------
| API Routes - Orientation Program for Participants
|--------------------------------------------------------------------------
*/

Route::prefix('orientation')->group(function () {
    // Get all orientation programs for a participant
    Route::get('/participant/programs', [ParticipantOrientationController::class, 'getParticipantOrientations'])
        ->name('api.orientation.participant.programs');
    
    // Get participant dashboard/summary
    Route::get('/participant/dashboard', [ParticipantOrientationController::class, 'getParticipantDashboard'])
        ->name('api.orientation.participant.dashboard');
    
    // Get detail of specific orientation program with all activities
    Route::get('/participant/program/{programId}', [ParticipantOrientationController::class, 'getParticipantOrientationDetail'])
        ->name('api.orientation.participant.program.detail');
    
    // Get activities of specific orientation program (simplified)
    Route::get('/participant/program/{programId}/activities', [ParticipantOrientationController::class, 'getParticipantActivities'])
        ->name('api.orientation.participant.program.activities');
});