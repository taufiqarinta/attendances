<?php

namespace App\Repositories\Interfaces;

use App\Http\Requests\CustomerFormRequest;
use App\Http\Requests\CustomerFormUpdateRequest;
use App\Http\Requests\MerkFormRequest;
use App\Http\Requests\MerkFormUpdateRequest;
use App\Http\Requests\MotifFormRequest;
use App\Http\Requests\UkuranFormRequest;
use App\Models\Order;
use Illuminate\Http\Request;

interface AdminRepositoryInterfaces
{
    // Halaman Awal
    public function index(Request $request);

    // Customer Menu
    public function indexCustomer(Request $request);

    public function storeCustomer(CustomerFormRequest $request);

    public function updateCustomer(CustomerFormUpdateRequest $request, $id);

    public function deleteCustomer($id);

    // Merk Menu
    public function indexMerk();

    public function storeMerk(MerkFormRequest $request);

    public function updateMerk(MerkFormUpdateRequest $request, $id);

    public function deleteMerk($id);

    // Ukuran Menu
    public function indexUkuran();

    public function storeUkuran(UkuranFormRequest $request);

    public function updateUkuran(UkuranFormRequest $request, $id);

    public function deleteUkuran($id);

    // Motif Menu
    public function indexMotif();

    public function storeMotif(MotifFormRequest $request);

    public function updateMotif(MotifFormRequest $request, $id);

    public function deleteMotif($id);

    public function filterMotif(Request $request);

    // Transaksi Menu
    public function indexTransaksi(Request $request);

    public function getUkurans($merkId);

    public function getMotifs($ukuranId);

    public function editTransaksi(Order $order);

    public function updateTransaksi(Request $request, Order $order);

    public function deleteTransaksi(Order $order);

    // Mark Confirm
    public function markPaid(Order $order);

    // Mark Confirm By Email
    public function markPaidEmail(Order $order);

    // Show Transaksi
    public function showTransaksi($orderId);

    // Generate PDF
    public function generatePDF($id);

    // Index Log
    public function indexLog(Request $request);

    public function generateForecast(Request $request);

    public function generateTotalForecast(Request $request);
}
