<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Repositories\Interfaces\UserRepositoryInterfaces;
use Illuminate\Http\Request;

class PermintaanController extends Controller
{
    public function __construct(protected UserRepositoryInterfaces $userRepository) {}

    public function indexPermintaan(Request $request)
    {
        return $this->userRepository->indexPermintaan($request);
    }

    public function storePermintaan(Request $request)
    {
        return $this->userRepository->storePermintaan($request);
    }

    // AJAX urutan permintaan MERK - UKURAN - MOTIF
    public function getUkurans($merkId)
    {
        return $this->userRepository->getUkurans($merkId);
    }

    public function getMotifs($ukuranId)
    {
        return $this->userRepository->getMotifs($ukuranId);
    }

    public function editOrder(Order $order)
    {
        return $this->userRepository->editOrder($order);
    }

    public function editHtml($id)
    {
        return $this->userRepository->editHtml($id);
    }


    public function updateOrder(Request $request, Order $order)
    {
        return $this->userRepository->updateOrder($request, $order);
    }

    public function showOrder($orderId)
    {
        return $this->userRepository->showOrder($orderId);
    }

    public function deleteOrder(Order $order)
    {
        return $this->userRepository->deleteOrder($order);
    }

    // PDF
    public function generatePDF($id)
    {
        return $this->userRepository->generatePDF($id);
    }

    // Send WA
    public function sendWA(Order $order)
    {
        return $this->userRepository->sendWA($order);
    }

    // Send email
    public function sendEmail(Order $order)
    {
        return $this->userRepository->sendEmail($order);
    }
}
