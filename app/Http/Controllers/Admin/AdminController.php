<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerFormRequest;
use App\Http\Requests\CustomerFormUpdateRequest;
use App\Http\Requests\MerkFormRequest;
use App\Http\Requests\MerkFormUpdateRequest;
use App\Http\Requests\MotifFormRequest;
use App\Http\Requests\UkuranFormRequest;
use App\Models\Order;
use App\Repositories\Interfaces\AdminRepositoryInterfaces;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct(protected AdminRepositoryInterfaces $adminRepository) {}

    public function index(Request $request)
    {
        return $this->adminRepository->index($request);
    }

    public function indexCustomer(Request $request)
    {
        return $this->adminRepository->indexCustomer($request);
    }

    public function storeCustomer(CustomerFormRequest $request)
    {
        return $this->adminRepository->storeCustomer($request);
    }

    public function updateCustomer(CustomerFormUpdateRequest $request, $id)
    {
        return $this->adminRepository->updateCustomer($request, $id);
    }

    public function deleteCustomer($id)
    {
        return $this->adminRepository->deleteCustomer($id);
    }

    // Merk
    public function indexMerk()
    {
        return $this->adminRepository->indexMerk();
    }

    public function storeMerk(MerkFormRequest $request)
    {
        return $this->adminRepository->storeMerk($request);
    }

    public function updateMerk(MerkFormUpdateRequest $request, $id)
    {
        return $this->adminRepository->updateMerk($request, $id);
    }

    public function deleteMerk($id)
    {
        return $this->adminRepository->deleteMerk($id);
    }

    // Ukuran
    public function indexUkuran()
    {
        return $this->adminRepository->indexUkuran();
    }

    public function storeUkuran(UkuranFormRequest $request)
    {
       return $this->adminRepository->storeUkuran($request);
    }

    public function updateUkuran(UkuranFormRequest $request, $id)
    {
        return $this->adminRepository->updateUkuran($request, $id);
    }

    public function deleteUkuran($id)
    {
        return $this->adminRepository->deleteUkuran($id);
    }

    // Motif
    public function indexMotif()
    {
        return $this->adminRepository->indexMotif();
    }

    public function storeMotif(MotifFormRequest $request)
    {
        return $this->adminRepository->storeMotif($request);
    }

    public function updateMotif(MotifFormRequest $request, $id)
    {
        return $this->adminRepository->updateMotif($request, $id);
    }

    public function deleteMotif($id)
    {
        return $this->adminRepository->deleteMotif($id);
    }

    public function filterMotif(Request $request)
    {
        return $this->adminRepository->filterMotif($request);
    }

    // Transaksi
    public function indexTransaksi(Request $request)
    {
        return $this->adminRepository->indexTransaksi($request);
    }

    // AJAX urutan permintaan MERK - UKURAN - MOTIF
    public function getUkurans($merkId)
    {
        return $this->adminRepository->getUkurans($merkId);
    }

    public function getMotifs($ukuranId)
    {
        return $this->adminRepository->getMotifs($ukuranId);
    }

    public function editTransaksi(Order $order)
    {
        return $this->adminRepository->editTransaksi($order);
    }

    public function updateTransaksi(Request $request, Order $order)
    {
        return $this->adminRepository->updateTransaksi($request, $order);
    }

    public function deleteTransaksi(Order $order)
    {
       return $this->adminRepository->deleteTransaksi($order);
    }

    public function markPaid(Order $order)
    {
        return $this->adminRepository->markPaid($order);
    }

    public function markPaidEmail(Order $order)
    {
        return $this->adminRepository->markPaidEmail($order);
    }

    public function showTransaksi($orderId)
    {
        return $this->adminRepository->showTransaksi($orderId);
    }

    public function generatePDF($id)
    {
        return $this->adminRepository->generatePDF($id);
    }

    // Log Aktivitas
    public function indexLog(Request $request)
    {
        return $this->adminRepository->indexLog($request);
    }

    public function generateForecast(Request $request)
    {
        return $this->adminRepository->generateForecast($request);
    }

    public function generateTotalForecast(Request $request)
    {
        return $this->adminRepository->generateTotalForecast($request);
    }
}
