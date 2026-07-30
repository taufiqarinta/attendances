<?php

use App\Exports\KomplainExport;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DaftarAgenController;
use App\Http\Controllers\DaftarHargaController;
use App\Http\Controllers\DaftarTokoController;
use App\Http\Controllers\FormOrderController;
use App\Http\Controllers\GeofencePlantController;
use App\Http\Controllers\ItemMasterController;
use App\Http\Controllers\ItemMasterTambahanController;
use App\Http\Controllers\IzinController;
use App\Http\Controllers\KehadiranController;
use App\Http\Controllers\KomplainController;
use App\Http\Controllers\MasterLokasiEventController;
use App\Http\Controllers\MasterTargetController;
use App\Http\Controllers\OrderGatheringController;
use App\Http\Controllers\Orientation\MasterOrientationActivityController;
use App\Http\Controllers\Orientation\OrientationProgramController;
use App\Http\Controllers\PeringkatController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportSPGController;
use App\Http\Controllers\ReportStockSPGController;
use App\Http\Controllers\SelfReportController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\SuratPesananBarangController;
use App\Http\Controllers\User\PermintaanController;
use App\Http\Controllers\User\WelcomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WilayahController;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login/api-callback', [AuthenticatedSessionController::class, 'apiCallback'])->name('login.api-callback');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
Route::get('/logout-get', [AuthenticatedSessionController::class, 'destroy'])->name('logout.get');

Route::get('/welcome', [WelcomeController::class, 'index'])->name('welcome');

// Route yang membutuhkan session login
Route::middleware(['web', 'check.api.session'])->group(function () {
    Route::resource('geofence-plant', GeofencePlantController::class)
        ->parameters(['geofence-plant' => 'geofence_plant']);

    Route::get('/change-password', function () {
        return view('auth.change-password');
    })->name('profile.edit');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/admin/welcome', function () {
        return view('admin.welcome');
    })->name('admin.welcome');


    Route::get('/absensi', function () {
        return view('absensi.index');
    })->name('absensi.index');

    Route::get('/allabsensi', function () {
        return view('absensi.allabsensi');
    })->name('allabsensi.index');

    Route::get('/absensi/create', function () {
        return view('absensi.create');
    })->name('absensi.create');
    Route::post('/absensi/save-photo', [App\Http\Controllers\AttendanceController::class, 'savePhotoFromBackend'])->name('absensi.save-photo');
    Route::get('/print-page', [App\Http\Controllers\PrintController::class, 'showPrintPage'])->name('print.page');
    Route::get('/storage/attendances/{year}/{month}/{filename}', [App\Http\Controllers\AttendanceController::class, 'getPhoto'])->where(['year' => '[0-9]+', 'month' => '[0-9]+'])->name('absensi.photo');

    Route::get('/approval', function () {
        return view('izin.approval');
    })->name('approval.page');

    Route::get('/izin', [IzinController::class, 'index'])->name('izin.index');
    Route::post('/izin/upload-file', [IzinController::class, 'uploadFile'])->name('izin.upload');
    Route::get('/izin/file/{filename}', [IzinController::class, 'getFile'])->name('izin.file');

    // Routes untuk Report
    Route::get('/selfreport', [SelfReportController::class, 'index'])->name('selfreport.index');

    // Biodata
    Route::get('/biodata',          fn() => view('biodata.index'))->name('biodata');
    Route::get('/approval-biodata', fn() => view('biodata.approval_biodata'))->name('approval.biodata');

    // Report
    Route::get('/report', function () {
        return view('report.index');
    })->name('report.index');

    // Summary atau Plant
    Route::get('/summary', function () {
        return view('summary.index');
    })->name('summary.index');

    // History Approval
    Route::get('/history', function () {
        return view('history.index');
    })->name('history.index');
    Route::prefix('orientation')->group(function () {

        /*
    |--------------------------------------------------------------------------
    | Master Activity
    |--------------------------------------------------------------------------
    */
        Route::controller(MasterOrientationActivityController::class)
            ->prefix('master-activity')
            ->group(function () {
                Route::get('/', 'index')
                    ->name('orientation.master-activity.index');

                Route::post('/', 'store')
                    ->name('orientation.master-activity.store');

                Route::get('/{id}/edit', 'edit')
                    ->name('orientation.master-activity.edit');

                Route::put('/{id}', 'update')
                    ->name('orientation.master-activity.update');

                Route::delete('/{id}', 'destroy')
                    ->name('orientation.master-activity.destroy');
            });

        /*
    |--------------------------------------------------------------------------
    | Orientation Program
    |--------------------------------------------------------------------------
    */
        Route::controller(OrientationProgramController::class)->group(function () {

            Route::get('/', 'index')
                ->name('orientation.index');

            Route::get('/create', 'create')
                ->name('orientation.create');

            Route::post('/', 'store')
                ->name('orientation.store');

            Route::get('/{orientation}/edit', 'edit')
                ->name('orientation.edit');

            Route::put('/{orientation}', 'update')
                ->name('orientation.update');

            Route::delete('/{orientation}', 'destroy')
                ->name('orientation.destroy');

            Route::get('/detail/{orientation}', 'show')
                ->name('orientation.detail');

            /*
        |--------------------------------------------------------------------------
        | Activity
        |--------------------------------------------------------------------------
        */
            Route::post('/score', 'updateScore')
                ->name('orientation.activity.score');

            Route::post('/status', 'updateStatus')
                ->name('orientation.activity.status');

            Route::post('/activity/get-score', 'getScore')
                ->name('orientation.activity.get-score');

            /*
        |--------------------------------------------------------------------------
        | Action
        |--------------------------------------------------------------------------
        */
            Route::post('/{orientation}/cancel', 'cancel')
                ->name('orientation.cancel');

            Route::post('/{orientation}/complete', 'complete')
                ->name('orientation.complete');

            /*
        |--------------------------------------------------------------------------
        | Export
        |--------------------------------------------------------------------------
        */
            Route::get('/{orientation}/export-schedule', 'exportSchedule')
                ->name('orientation.export.schedule');

            Route::get('/{orientation}/export-participants', 'exportParticipants')
                ->name('orientation.export.participants');
        });
    });
});

Route::fallback(function () {
    return response()->view('errors.custom-404', [], 404);
});

require __DIR__ . '/auth.php';