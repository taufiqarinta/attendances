<?php

namespace App\Repositories;

use App\Models\Merk;
use App\Models\Motif;
use App\Models\Order;
use App\Models\Permintaan;
use App\Models\Prioritas;
use App\Models\CabangCustomer;
use App\Models\Ukuran;
use App\Repositories\Interfaces\UserRepositoryInterfaces;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UserRepositories implements UserRepositoryInterfaces
{
    public function indexPermintaan(Request $request)
    {
        $userId = auth()->id();
        $idCust = auth()->user()->id_customer;


        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');

        $ordersQuery = Order::with(['cabang', 'user','permintaans' => function ($q) use ($userId) {
            $q->where('user_id', $userId);
        }])->where('hapus', 0)->whereHas('permintaans', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });


        if ($tanggalAwal && $tanggalAkhir) {
            $ordersQuery->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
        } elseif ($tanggalAwal) {
            $ordersQuery->where('tanggal', '>=', $tanggalAwal);
        } elseif ($tanggalAkhir) {
            $ordersQuery->where('tanggal', '<=', $tanggalAkhir);
        }

        $orders = $ordersQuery->orderBy('created_at', 'desc')->paginate(10);

        foreach ($orders as $order) {
            $order->forecast_period = $this->getForecastBulan($order->tanggal);
        }

        // ✅ Merk urut abjad
        $merks = Merk::select('merks.id', 'merks.name')
        ->join('users_merks', 'users_merks.id_merks', '=', 'merks.id')
        ->where('users_merks.id_customer', $idCust)
        ->orderBy('merks.name')
        ->get();

        $ukurans = Ukuran::select('id', 'name')->get();

        $daftarPrioritas = Prioritas::get();
        $cabangs = CabangCustomer::where('id_customer', $userId)->get();

        return view('permintaan.index', compact('orders', 'merks', 'ukurans', 'tanggalAwal', 'tanggalAkhir','daftarPrioritas','cabangs'));
    }

    public function storePermintaan(Request $request)
    {
        $validated = $request->validate([
            'id_cabang' => 'required|exists:cabang_customer,id_cabang',
            'permintaans.*.merk_id' => 'required|exists:merks,id',
            'permintaans.*.ukuran_id' => 'required|exists:ukurans,id',
            'permintaans.*.motif' => 'required',
            'permintaans.*.estimasi' => 'required|numeric',
            'permintaans.*.prioritas' => 'required|numeric',
        ], [
            'id_cabang.required' => 'Cabang wajib dipilih',
            'id_cabang.exists' => 'Cabang tidak valid',
            'permintaans.*.estimasi.required' => 'Estimasi order harus diisi',
            'permintaans.*.estimasi.numeric' => 'Estimasi order harus berupa angka',
        ]);

        // Forecast saat ini berdasarkan tanggal order
        $tanggalOrder = now();
        $forecast = $this->getForecastBulan($tanggalOrder);

        // ✅ CEK apakah user sudah pernah input forecast ini
        // $sudahAda = Order::where('user_id', auth()->id())
        //     ->where('forecast', $forecast)
        //     ->exists();

        $sudahAda = Order::where('id_cabang', $request->id_cabang)
            ->where('forecast', $forecast)
            ->exists();

        if ($sudahAda) {
            return redirect()->back()->with('error', "Forecast $forecast sudah pernah Anda isi. Silakan isi forecast berikutnya.");
        }

        // Generate kode order baru
        $lastNumber = Order::where('kode', 'LIKE', 'ORD - %')
            ->selectRaw("MAX(CAST(TRIM(SUBSTRING(kode, 7)) AS UNSIGNED)) as max_number")
            ->value('max_number');

        $nextNumber = str_pad(($lastNumber ? $lastNumber + 1 : 1), 3, '0', STR_PAD_LEFT);
        $kodeOrder = 'ORD - ' . $nextNumber;

        // Simpan order baru
        $order = Order::create([
            'kode' => $kodeOrder,
            'tanggal' => $tanggalOrder,
            'status' => 0,
            'user_id' => auth()->id(),
            'id_cabang' => $request->id_cabang,
            'forecast' => $forecast,
        ]);

        foreach ($validated['permintaans'] as $item) {
            Permintaan::create([
                'name' => auth()->user()->name,
                'tanggal' => $tanggalOrder->format('Y-m-d'),
                'merk_id' => $item['merk_id'],
                'ukuran_id' => $item['ukuran_id'],
                'motif' => $item['motif'],
                'estimasi' => $item['estimasi'],
                'prioritas' => $item['prioritas'],
                'user_id' => auth()->id(),
                'order_id' => $order->id,
            ]);
        }

        return redirect()->back()->with('success', 'Permintaan Order berhasil ditambahkan');
    }

    // AJAX urutan permintaan MERK - UKURAN - MOTIF
    public function getUkurans($merkId)
    {
        $ukurans = Ukuran::where('merk_id', $merkId)->get();
        return response()->json($ukurans);
    }

    public function getMotifs($ukuranId)
    {
        $motifs = Motif::where('ukuran_id', $ukuranId)->orderBy('name')->get();
        return response()->json($motifs);
    }

    public function editOrder(Order $order)
    {
        $permintaans = $order->permintaans()->get()->map(function ($p) {
            return [
                'id' => $p->id,
                'tanggal' => $p->tanggal,
                'merk_id' => $p->merk_id,
                'ukuran_id' => $p->ukuran_id,
                'motif' => $p->motif,
                'estimasi' => $p->estimasi,
                'prioritas' => $p->prioritas,
            ];
        });
        return response()->json(['permintaans' => $permintaans]);
    }

    public function editHtml($id)
    {
        $order = Order::with('permintaans')->findOrFail($id);

        // ✅ Merk urut abjad
        $merks = Merk::orderBy('name')->get();

        foreach ($order->permintaans as $item) {
            $item->ukurans = Ukuran::where('merk_id', $item->merk_id)->orderBy('name')->get();

            // ✅ Motif urut abjad
            $item->motifs = Motif::where('ukuran_id', $item->ukuran_id)->orderBy('name')->get();
        }

        return view('permintaan._form_edit', compact('order', 'merks'))->render();
    }


    public function updateOrder(Request $request, Order $order)
    {
        $request->validate([
            'permintaans.*.id' => 'nullable|exists:permintaans,id',
            // 'permintaans.*.tanggal' => 'required|date', // HAPUS
            'permintaans.*.merk_id' => 'required',
            'permintaans.*.ukuran_id' => 'required',
            'permintaans.*.motif' => 'required',
            'permintaans.*.estimasi' => 'required|numeric',
        ]);

        $existingIds = $order->permintaans->pluck('id')->toArray();
        $submittedIds = collect($request->permintaans)->pluck('id')->filter()->toArray();

        // Hapus permintaan yg dihapus user
        $toDelete = array_diff($existingIds, $submittedIds);
        Permintaan::destroy($toDelete);

        foreach ($request->permintaans as $item) {
            if (isset($item['id'])) {
                $permintaan = Permintaan::find($item['id']);
                $permintaan->update([
                    'tanggal' => $order->created_at->format('Y-m-d'), // ➜ FORCE AUTO TGL
                    'merk_id' => $item['merk_id'],
                    'ukuran_id' => $item['ukuran_id'],
                    'motif' => $item['motif'],
                    'estimasi' => $item['estimasi'],
                    'prioritas' => $item['prioritas'],
                ]);
            } else {
                Permintaan::create([
                    'name' => auth()->user()->name,
                    'tanggal' => $order->created_at->format('Y-m-d'), // ➜ FORCE AUTO TGL
                    'merk_id' => $item['merk_id'],
                    'ukuran_id' => $item['ukuran_id'],
                    'motif' => $item['motif'],
                    'estimasi' => $item['estimasi'],
                    'prioritas' => $item['prioritas'],
                    'user_id' => auth()->id(),
                    'order_id' => $order->id,
                ]);
            }
        }

        $order->touch();

        return redirect()->back()->with('success', 'Order berhasil diperbarui.');
    }

    public function showOrder($orderId)
    {
        $order = Order::with(['permintaans.merk', 'permintaans.ukuran','permintaans.prioritas'])->findOrFail($orderId);

        return response()->json($order);
    }

    public function deleteOrder(Order $order)
    {
        // $order->permintaans()->delete();
        // $order->delete();
        $order->update(['hapus' => 1]);

        return redirect()->back()->with('success', 'Order berhasil dihapus');
    }

    // PDF
    public function generatePDF($id)
    {
        $order = Order::with(['permintaans.merk', 'permintaans.ukuran', 'user'])->findOrFail($id);

        // Hitung total estimasi
        $totalQty = $order->permintaans->sum('estimasi');

        // Hitung forecast bulan
        $forecastBulan = $this->getForecastBulan($order->tanggal);

        $pdf = Pdf::loadView('permintaan.pdf_template', [
            'order' => $order,
            'totalQty' => $totalQty,
            'forecastBulan' => $forecastBulan
        ]);

        return $pdf->download('order_' . $order->kode . '.pdf');
    }

    private function getForecastBulan($tanggal)
    {
        $tanggal = \Carbon\Carbon::parse($tanggal);
        $year = now()->format('Y');

        // Definisikan mapping: [start, end, hasil]
        $mapping = [
            ['start' => ['06-26'], 'end' => ['07-25'], 'hasil' => 'Agustus'],
            ['start' => ['07-26'], 'end' => ['08-25'], 'hasil' => 'September'],
            ['start' => ['08-26'], 'end' => ['09-25'], 'hasil' => 'Oktober'],
            ['start' => ['09-26'], 'end' => ['10-25'], 'hasil' => 'November'],
            ['start' => ['10-26'], 'end' => ['11-25'], 'hasil' => 'Dessember'],
            ['start' => ['11-26'], 'end' => ['12-25'], 'hasil' => 'Januari'],
            ['start' => ['12-26'], 'end' => ['01-25'], 'hasil' => 'Februari'],
            ['start' => ['01-26'], 'end' => ['02-25'], 'hasil' => 'Maret'],
            ['start' => ['02-26'], 'end' => ['03-25'], 'hasil' => 'April'],
            ['start' => ['03-26'], 'end' => ['04-25'], 'hasil' => 'Mei'],
            ['start' => ['04-26'], 'end' => ['05-25'], 'hasil' => 'Juni'],
            ['start' => ['05-26'], 'end' => ['06-25'], 'hasil' => 'Juli'],
        ];

        foreach ($mapping as $item) {
            $start = \Carbon\Carbon::create($tanggal->year, substr($item['start'][0], 0, 2), substr($item['start'][0], 3, 2));
            $endMonth = substr($item['end'][0], 0, 2);
            $endDay = substr($item['end'][0], 3, 2);
            // Tangani rentang lintas tahun
            if ($endMonth == '01' && $start->month == 12) {
                $end = \Carbon\Carbon::create($tanggal->year + 1, $endMonth, $endDay);
            } else {
                $end = \Carbon\Carbon::create($tanggal->year, $endMonth, $endDay);
            }

            if ($tanggal->between($start, $end)) {
                return "{$item['hasil']} {$year}";
            }
        }

        // Default kalau tidak cocok
        return "N/A {$year}";
    }

    // Send WA
    public function sendWA(Order $order)
    {
        $admin = \App\Models\User::where('role_as', 1)
            ->whereNotNull('phone')
            ->first();

        if (!$admin) {
            return response()->json(['url' => null, 'error' => 'Nomor admin tidak ditemukan.']);
        }

        // Format nomor WA
        $rawPhone = preg_replace('/[^0-9]/', '', $admin->phone);
        if (substr($rawPhone, 0, 2) === '62') {
            $phone = $rawPhone;
        } elseif (substr($rawPhone, 0, 1) === '0') {
            $phone = '62' . substr($rawPhone, 1);
        } else {
            $phone = '62' . ltrim($rawPhone, '+');
        }

        // Buat pesan
        $details = [];
        foreach ($order->permintaans as $permintaan) {
            $merk = $permintaan->merk->name ?? '-';
            $ukuran = $permintaan->ukuran->name ?? '-';
            $motif = $permintaan->motif ?? '-';
            $estimasi = $permintaan->estimasi ?? '-';

            $details[] = "• {$merk}, {$ukuran}, {$motif}, Qty: {$estimasi}";
        }

        $detailText = implode("\n", $details);
        $formattedTanggal = \Carbon\Carbon::parse($order->tanggal)
            ->translatedFormat('d F Y');
        $message = "Halo Admin,\nAda permintaan order baru dengan Kode: {$order->kode} tanggal order: {$formattedTanggal}\n\nDetail:\n{$detailText}\n\nTerima kasih.";
        $encodedMessage = urlencode($message);

        $waUrl = "https://api.whatsapp.com/send?phone={$phone}&text={$encodedMessage}";

        return response()->json(['url' => $waUrl]);
    }

    public function sendEmail(Order $order)
    {
        $sender = Auth::user(); // User yang login
        if (!$sender || $sender->role_as != 0 || !$sender->email) {
            return response()->json(['success' => false, 'error' => 'User tidak valid atau tidak memiliki email.']);
        }

        // Ambil admin
        $admin = \App\Models\User::where('role_as', 1)
            ->where('email', 'teddy.tancorp@gmail.com')
            ->first();

        if (!$admin) {
            return response()->json(['success' => false, 'error' => 'Email admin tidak ditemukan.']);
        }

        // Siapkan detail permintaan
        $details = [];
        foreach ($order->permintaans as $permintaan) {
            $merk = $permintaan->merk->name ?? '-';
            $ukuran = $permintaan->ukuran->name ?? '-';
            $motif = $permintaan->motif ?? '-';
            $estimasi = $permintaan->estimasi ?? '-';

            $details[] = "• {$merk}, {$ukuran}, {$motif}, Qty: {$estimasi}";
        }

        $detailText = implode("\n", $details);
        $formattedTanggal = \Carbon\Carbon::parse($order->tanggal)->translatedFormat('d F Y');

        $message = "Halo Admin,\nAda permintaan order baru dari *{$sender->name}* (Email: {$sender->email})\nKode Order: {$order->kode}\nTanggal Order: {$formattedTanggal}\n\nDetail:\n{$detailText}\n\nTerima kasih.";

        try {
            Mail::raw($message, function ($mail) use ($admin, $sender) {
                $mail->to($admin->email)
                    ->from(config('mail.from.address'), config('mail.from.name')) // pengirim tetap resmi
                    ->replyTo($sender->email, $sender->name) // admin bisa klik 'Reply' ke user
                    ->subject('Permintaan Order Baru dari Pelanggan');
            });

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Gagal mengirim email.']);
        }
    }
}
