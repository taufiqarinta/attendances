<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KomplainController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\MasterTargetController;
use App\Http\Controllers\User\PermintaanController;
use App\Http\Controllers\User\WelcomeController;
use App\Http\Controllers\OrderGatheringController;
use App\Http\Controllers\DaftarTokoController;
use App\Http\Controllers\DaftarAgenController;
use Illuminate\Support\Facades\Route;
use App\Exports\KomplainExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\FormOrderController;
use App\Http\Controllers\KehadiranController;
use App\Http\Controllers\MasterLokasiEventController;
use App\Http\Controllers\SuratPesananBarangController;
use App\Http\Controllers\PeringkatController;
use App\Http\Controllers\DaftarHargaController;
use App\Http\Controllers\ItemMasterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ItemMasterTambahanController;
use App\Http\Controllers\ReportSPGController;
use App\Http\Controllers\ReportStockSPGController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\IzinController;
use App\Http\Controllers\SelfReportController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;



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
});

Route::get('/suratpesananbarang/export-excel', [SuratPesananBarangController::class, 'exportExcel'])->name('suratpesananbarang.export-excel');
Route::get('/suratpesananbarang/{id}/export-pdf', [SuratPesananBarangController::class, 'exportPDF'])->name('suratpesananbarang.export-pdf');

Route::get('/welcome', [WelcomeController::class, 'index'])->middleware(['auth', 'verified'])->name('welcome');

Route::post('/reportstockspg/check-duplicate', [ReportStockSPGController::class, 'checkDuplicate'])
    ->name('reportstockspg.check-duplicate');

Route::post('/stockopname/check-duplicate', [StockOpnameController::class, 'checkDuplicate'])
    ->name('stockopname.check-duplicate');


// API untuk get SPG data
Route::get('daftar-toko/get-spg', [DaftarTokoController::class, 'getSpgByKode'])->name('get-spg');

Route::middleware('auth')->group(function () {
    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Daftar Toko Routes - hanya untuk admin
    Route::prefix('daftar-toko')->name('daftar-toko.')->group(function () {
        Route::get('/', [DaftarTokoController::class, 'index'])->name('index');
        Route::get('/create', [DaftarTokoController::class, 'create'])->name('create');
        Route::post('/', [DaftarTokoController::class, 'store'])->name('store');
        Route::get('/{daftar_toko}', [DaftarTokoController::class, 'show'])->name('show');
        Route::get('/{daftar_toko}/edit', [DaftarTokoController::class, 'edit'])->name('edit');
        Route::put('/{daftar_toko}', [DaftarTokoController::class, 'update'])->name('update');
        Route::post('/{daftar_toko}/activate', [DaftarTokoController::class, 'activate'])->name('activate');
        Route::post('/{daftar_toko}/deactivate', [DaftarTokoController::class, 'deactivate'])->name('deactivate');
        
    });
});

Route::middleware(['auth', 'verified'])->group(function () {

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
    });

    Route::resource('itemmaster', ItemMasterController::class);
    Route::resource('itemmaster-tambahan', ItemMasterTambahanController::class);
    Route::post('itemmaster-tambahan/{id}/activate', [ItemMasterTambahanController::class, 'activate'])->name('itemmaster-tambahan.activate');
    Route::post('itemmaster-tambahan/{id}/deactivate', [ItemMasterTambahanController::class, 'deactivate'])->name('itemmaster-tambahan.deactivate');

    // Report Penjualan SPG
    // Route untuk Report SPG
    Route::resource('reportspg', ReportSPGController::class);
    // API untuk get items (Select2)
    Route::get('/api/items', [ReportSPGController::class, 'getItems'])->name('api.items');
    Route::get('/reportspg/export/excel', [ReportSPGController::class, 'exportExcel'])->name('reportspg.export-excel');

    // Route untuk Report Stock SPG
    Route::resource('reportstockspg', ReportStockSPGController::class)->middleware('auth');
    Route::get('/api/stock-items', [ReportStockSPGController::class, 'getItems'])->name('api.stock-items');
    Route::post('/api/previous-stock', [ReportStockSPGController::class, 'getPreviousStock'])->name('api.previous-stock');
    // API untuk get latest stock
    Route::post('/api/latest-stock', [ReportStockSPGController::class, 'getLatestStock'])->name('api.latest-stock');
    // Export Excel
    Route::get('/report-stock-spg/export-excel', [ReportStockSPGController::class, 'exportExcel'])
    ->name('reportstockspg.export-excel');

    // Stock Opname Routes
    Route::get('/stockopname/export-excel', [StockOpnameController::class, 'exportExcel'])->name('stockopname.export-excel');
    Route::resource('stockopname', StockOpnameController::class);
    Route::post('/stockopname/{stockopname}/status', [StockOpnameController::class, 'updateStatus'])->name('stockopname.update-status');
    Route::get('/api/stockopname-items', [StockOpnameController::class, 'getItems'])->name('api.stockopname-items');


    Route::resource('suratpesananbarang', SuratPesananBarangController::class);
    Route::post('suratpesananbarang/{suratpesananbarang}/submit', [SuratPesananBarangController::class, 'submit'])->name('suratpesananbarang.submit');
    Route::post('suratpesananbarang/{suratpesananbarang}/approve', [SuratPesananBarangController::class, 'approve'])->name('suratpesananbarang.approve');
    Route::post('suratpesananbarang/{suratpesananbarang}/diketahui', [SuratPesananBarangController::class, 'diketahui'])->name('suratpesananbarang.diketahui');
    Route::post('suratpesananbarang/{suratpesananbarang}/reject', [SuratPesananBarangController::class, 'reject'])->name('suratpesananbarang.reject');

    // Cancel status / memundurkan status
    Route::post('/suratpesananbarang/{id}/cancel-approval-sls', [SuratPesananBarangController::class, 'cancelApprovalSLS'])->name('suratpesananbarang.cancel-approval-sls');
    Route::post('/suratpesananbarang/{id}/cancel-approval-fnc', [SuratPesananBarangController::class, 'cancelApprovalFNC'])->name('suratpesananbarang.cancel-approval-fnc');

    // Import daftar Harga
    Route::get('/daftarharga/import', [DaftarHargaController::class, 'importForm'])->name('daftarharga.import-form');
    Route::post('/daftarharga/import-preview', [DaftarHargaController::class, 'importPreview'])->name('daftarharga.import-preview');
    Route::post('/daftarharga/import-process', [DaftarHargaController::class, 'importProcess'])->name('daftarharga.import-process');

    Route::get('/get-products/{ukuran}', [SuratPesananBarangController::class, 'getProductsByUkuran']);
    Route::get('/get-brands/{ukuran}/{product}', [SuratPesananBarangController::class, 'getBrandsByProduct']);
    Route::get('/get-kw/{ukuran}/{product}/{brand}', [SuratPesananBarangController::class, 'getKwByBrand']);
    Route::get('/get-harga/{ukuran}/{product}/{brand}/{kw}/{jenisHarga}', [SuratPesananBarangController::class, 'getHargaBySelection']);

    Route::resource('daftarharga', DaftarHargaController::class);
    Route::get('daftarharga/export/excel', [DaftarHargaController::class, 'exportExcel'])->name('daftarharga.export-excel');

    Route::resource('daftar-agen', DaftarAgenController::class);

});

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::controller(AdminController::class)->group(function () {
        Route::get('welcome', 'index');
        
        // Customer
        Route::get('customer', 'indexCustomer')->name('admin.customer');
        Route::post('customer', 'storeCustomer')->name('admin.customer.store');
        Route::get('api/customers/{id}', function ($id) {
            return \App\Models\User::findOrFail($id);
        });
        Route::put('customer/{id}', 'updateCustomer')->name('admin.customer.update');
        Route::delete('customer/{id}', 'deleteCustomer')->name('admin.customer.delete');

        // Merk
        Route::get('merk', 'indexMerk')->name('admin.merk');
        Route::post('merk', 'storeMerk')->name('admin.merk.store');
        Route::put('merk/{id}', 'updateMerk')->name('admin.merk.update');
        Route::delete('merk/{id}', 'deleteMerk')->name('admin.merk.delete');

        // Ukuran
        Route::get('ukuran', 'indexUkuran')->name('admin.ukuran');
        Route::post('ukuran', 'storeUkuran')->name('admin.ukuran.store');
        Route::put('ukuran/{id}', 'updateUkuran')->name('admin.ukuran.update');
        Route::delete('ukuran/{id}', 'deleteUkuran')->name('admin.ukuran.delete');

        // Motif
        Route::get('motif', 'indexMotif')->name('admin.motif');
        Route::post('motif', 'storeMotif')->name('admin.motif.store');
        Route::put('motif/{id}', 'updateMotif')->name('admin.motif.update');
        Route::delete('motif/{id}', 'deleteMotif')->name('admin.motif.delete');

        Route::get('/filter-motif', 'filterMotif')->name('motif.filter');

        // Tranksaksi
        Route::get('transaksi', 'indexTransaksi')->name('admin.transaksi');
        Route::get('/transaksi/{order}/edit', 'editTransaksi')->name('transaksi.edit');
        Route::put('/transaksi/{order}/update', 'updateTransaksi')->name('transaksi.update');
        Route::delete('/transaksi/{order}/delete', 'deleteTransaksi')->name('transaksi.delete');

        // AJAX untuk pemilihan ukuran dan motif
        Route::get('/get-ukurans/{merkId}', 'getUkurans')->name('get-ukurans');
        Route::get('/get-motifs/{ukuranId}', 'getMotifs')->name('get-motifs');

        Route::put('/transaksi/{order}/mark-paid', 'markPaid')->name('order.markPaid');

        // Show
        Route::get('/transaksi/{order}/view', 'showTransaksi');

        // PDF
        Route::get('/transaksi/{order}/pdf', 'generatePDF')->name('transaksi.pdf');

        // Send WA
        Route::post('/send-wa/transaksi/{order}',  'markPaid')->name('transaksi.markPaid');

        // Send Email
        Route::post('/send-email/transaksi/{order}', 'markPaidEmail')->name('transaksi.markPaidEmail');

        // Log
        Route::get('log', 'indexLog')->name('admin.log');

        // Download Forecast Period
        Route::get('/transaksi/generate-forecast', 'generateForecast')->name('admin.generateForecast');

        // Total Forecast
        Route::get('/transaksi/generate-totalforecast', 'generateTotalForecast')->name('admin.generateTotalForecast');
    });
});


require __DIR__ . '/auth.php';
