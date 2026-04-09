<?php

namespace App\Repositories\Interfaces;

use App\Models\Order;
use Illuminate\Http\Request;

interface UserRepositoryInterfaces
{
    public function indexPermintaan(Request $request);

    public function storePermintaan(Request $request);

    public function getUkurans($merkId);

    public function getMotifs($ukuranId);

    public function editOrder(Order $order);

    public function editHtml($id);

    public function updateOrder(Request $request, Order $order);

    public function showOrder($orderId);

    public function deleteOrder(Order $order);

    public function generatePDF($id);

    public function sendWA(Order $order);

    public function sendEmail(Order $order);
}
