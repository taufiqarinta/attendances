<?php

namespace App\Repositories;

use App\Exports\ForecastExport;
use App\Exports\TotalForecastExport;
use App\Http\Requests\CustomerFormRequest;
use App\Http\Requests\CustomerFormUpdateRequest;
use App\Http\Requests\MerkFormRequest;
use App\Http\Requests\MerkFormUpdateRequest;
use App\Http\Requests\MotifFormRequest;
use App\Http\Requests\UkuranFormRequest;
use App\Models\Merk;
use App\Models\Motif;
use App\Models\Order;
use App\Models\Permintaan;
use App\Models\Ukuran;
use App\Models\User;
use App\Repositories\Interfaces\AdminRepositoryInterfaces;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class AdminRepositories implements AdminRepositoryInterfaces
{
    public function index(Request $request)
    {
        dd("Masuk bosq");

        $user = Auth::user();
        $search = $request->input('search', '');


        session()->flash('status', 'Selamat datang, ' . $user->name);
        return view('dashboard', compact('user', 'search'));
    }

    // Customer
    public function indexCustomer(Request $request)
    {
        $query = User::select('id', 'name', 'email', 'phone')->where('role_as', 0);

        if ($request->filled('search')) {
            $searchTerms = explode(' ', $request->search);
            foreach ($searchTerms as $term) {
                $query->where('name', 'like', '%' . $term . '%');
            }
        }

        $customers = $query->orderBy('name', 'ASC')->paginate(10)->appends(['search' => $request->search]);

        if ($request->ajax()) {
            return view('admin.customer.partials.table', compact('customers'))->render();
        }

        return view('admin.customer.index', compact('customers'));
    }

    public function storeCustomer(CustomerFormRequest $request)
    {
        $validatedData = $request->validated();

        $phone = $validatedData['phone'];
        if (!empty($phone) && str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $customer = new User;
        $customer->id_customer = $validatedData['id_customer'];
        $customer->name = $validatedData['name'];
        $customer->email = $validatedData['email'];
        $customer->phone = $phone;
        $customer->password = Hash::make($validatedData['password']); // hash password di sini

        $customer->save();

        return redirect()->route('admin.customer')->with('success', 'Customer berhasil ditambahkan');
    }

    public function updateCustomer(CustomerFormUpdateRequest $request, $id)
    {
        $validatedData = $request->validated();

        $customer = User::findOrFail($id);
        $customer->id_customer = $validatedData['id_customer'];
        $customer->name = $validatedData['name'];
        $customer->email = $validatedData['email'];

        if ($validatedData['phone'] && str_starts_with($validatedData['phone'], '0')) {
            $customer->phone = '62' . substr($validatedData['phone'], 1);
        } else {
            $customer->phone = $validatedData['phone'];
        }

        if (!empty($validatedData['password'])) {
            $customer->password = Hash::make($validatedData['password']);
        }

        $customer->save();

        return redirect()->route('admin.customer')->with('success', 'Customer berhasil diperbarui');
    }

    public function deleteCustomer($id)
    {
        $customer = User::findOrFail($id);
        $customer->delete();

        return redirect()->route('admin.customer')->with('success', 'Customer berhasil dihapus');
    }

    // Merk
    public function indexMerk()
    {
        $merks = Merk::select('id', 'name')->orderBy('id', 'ASC')->paginate(10);

        return view('admin.merk.index', compact('merks'));
    }

    public function storeMerk(MerkFormRequest $request)
    {
        $validatedData = $request->validated();

        $merk = new Merk;
        $merk->name = $validatedData['name'];

        $merk->save();

        return redirect()->route('admin.merk')->with('success', 'Merk berhasil ditambahkan');
    }

    public function updateMerk(MerkFormUpdateRequest $request, $id)
    {
        $merk = Merk::findOrFail($id);

        $merk->update([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.merk')->with('success', 'Merk berhasil diperbarui');
    }

    public function deleteMerk($id)
    {
        $merk = Merk::findOrFail($id);
        $merk->delete();

        return redirect()->route('admin.merk')->with('success', 'Merk berhasil dihapus');
    }

    // Ukuran
    public function indexUkuran()
    {
        $ukurans = Ukuran::select('id', 'name', 'merk_id')->orderBy('id', 'ASC')->paginate(10);

        $merks = Merk::select('id', 'name')->get();

        return view('admin.ukuran.index', compact('ukurans', 'merks'));
    }

    public function storeUkuran(UkuranFormRequest $request)
    {
        $validatedData = $request->validated();

        $ukuran = new Ukuran;
        $ukuran->name = $validatedData['name'];
        $ukuran->merk_id = $validatedData['merk_id'];

        $ukuran->save();

        return redirect()->route('admin.ukuran')->with('success', 'Ukuran berhasil ditambahkan');
    }

    public function updateUkuran(UkuranFormRequest $request, $id)
    {
        $ukuran = Ukuran::findOrFail($id);

        $ukuran->update([
            'name' => $request->name,
            'merk_id' => $request->merk_id,
        ]);

        return redirect()->route('admin.ukuran')->with('success', 'Ukuran berhasil diperbarui');
    }

    public function deleteUkuran($id)
    {
        $ukuran = Ukuran::findOrFail($id);
        $ukuran->delete();

        return redirect()->route('admin.ukuran')->with('success', 'Ukuran berhasil dihapus');
    }

    // Motif
    public function indexMotif()
    {
        $motifs = Motif::select('id', 'name', 'ukuran_id')->orderBy('id', 'ASC')->paginate(10);

        $ukurans = Ukuran::with('merk')->get();

        return view('admin.motif.index', compact('motifs', 'ukurans'));
    }

    public function storeMotif(MotifFormRequest $request)
    {
        $validatedData = $request->validated();

        $motif = new Motif;
        $motif->name = $validatedData['name'];
        $motif->ukuran_id = $validatedData['ukuran_id'];

        $motif->save();

        return redirect()->route('admin.motif')->with('success', 'Motif berhasil ditambahkan');
    }

    public function updateMotif(MotifFormRequest $request, $id)
    {
        $motif = Motif::findOrFail($id);

        $motif->update([
            'name' => $request->name,
            'ukuran_id' => $request->ukuran_id,
        ]);

        return redirect()->route('admin.motif')->with('success', 'Motif berhasil diperbarui');
    }

    public function deleteMotif($id)
    {
        $motif = Motif::findOrFail($id);
        $motif->delete();

        return redirect()->route('admin.motif')->with('success', 'Motif berhasil dihapus');
    }

    public function filterMotif(Request $request)
    {
        $merkId = $request->merk_id;

        // Ambil semua motif yang ukuran-nya memiliki merk tertentu
        $motifs = Motif::with(['ukuran.merk'])
            ->whereHas('ukuran.merk', function ($query) use ($merkId) {
                $query->where('id', $merkId);
            })
            ->get();

        return response()->json($motifs);
    }

    // Transaksi
    public function indexTransaksi(Request $request)
    {
        // Ambil query filter tanggal dari request
        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');

        // Query semua orders yang memiliki permintaan (tanpa filter user_id)
        $ordersQuery = Order::where('hapus', 0)
            ->whereHas('permintaans')
            ->with(['permintaans', 'user','cabang']);


        // Filter jika ada tanggal_awal & tanggal_akhir
        if ($tanggalAwal && $tanggalAkhir) {
            $ordersQuery->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
        } elseif ($tanggalAwal) {
            $ordersQuery->where('tanggal', '>=', $tanggalAwal);
        } elseif ($tanggalAkhir) {
            $ordersQuery->where('tanggal', '<=', $tanggalAkhir);
        }

        // Urutkan terbaru & paginate 10 per halaman
        $orders = $ordersQuery->orderBy('created_at', 'desc')->paginate(10);

        foreach ($orders as $order) {
            $order->forecast_period = $this->getForecastBulan($order->tanggal);
        }

        $merks = Merk::select('id', 'name')->orderBy('name')->get();

        return view('admin.transaksi.index', compact('orders', 'merks', 'tanggalAwal', 'tanggalAkhir'));
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

    public function editTransaksi(Order $order)
    {
        $permintaans = $order->permintaans()->get()->map(function ($p) {
            return [
                'id' => $p->id,
                'tanggal' => $p->tanggal,
                'merk_id' => $p->merk_id,
                'ukuran_id' => $p->ukuran_id,
                'motif' => $p->motif,
                'estimasi' => $p->estimasi,
            ];
        });
        return response()->json(['permintaans' => $permintaans]);
    }

    public function updateTransaksi(Request $request, Order $order)
    {
        $request->validate([
            'permintaans.*.id' => 'nullable|exists:permintaans,id',
            // 'permintaans.*.tanggal' => 'required|date',
            'permintaans.*.merk_id' => 'required',
            'permintaans.*.ukuran_id' => 'required',
            'permintaans.*.motif' => 'required',
            'permintaans.*.estimasi' => 'required|numeric',
        ]);

        $existingIds = $order->permintaans->pluck('id')->toArray();
        $submittedIds = collect($request->permintaans)->pluck('id')->filter()->toArray();

        // Delete yang dihapus user
        $toDelete = array_diff($existingIds, $submittedIds);
        Permintaan::destroy($toDelete);

        foreach ($request->permintaans as $item) {
            if (isset($item['id'])) {
                // Update
                $permintaan = Permintaan::find($item['id']);
                $permintaan->update([
                    // 'tanggal' => $item['tanggal'],
                    'merk_id' => $item['merk_id'],
                    'ukuran_id' => $item['ukuran_id'],
                    'motif' => $item['motif'],
                    // 'estimasi' => "{$item['estimasi']} unit",
                    'estimasi' => $item['estimasi'],
                ]);
            } else {
                // Tambah baru jika ada baris tambahan
                Permintaan::create([
                    'name' => auth()->user()->name,
                    'tanggal' => Carbon::now()->format('Y-m-d'),
                    'merk_id' => $item['merk_id'],
                    'ukuran_id' => $item['ukuran_id'],
                    'motif' => $item['motif'],
                    // 'estimasi' => "{$item['estimasi']} unit",
                    'estimasi' => $item['estimasi'],
                    'user_id' => $order->user_id,
                    'order_id' => $order->id,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Order berhasil diperbarui.');
    }

    public function deleteTransaksi(Order $order)
    {
        // $order->permintaans()->delete();
        // $order->delete();
        $order->update(['hapus' => 1]);

        return redirect()->back()->with('success', 'Order berhasil dihapus');
    }

    public function markPaid(Order $order)
    {
        $order->status = 1;
        $order->approval_id = auth()->user()->id;
        $order->save();

        $user = $order->user;

        if ($user && $user->role_as == 0 && $user->phone) {
            $rawPhone = preg_replace('/[^0-9]/', '', $user->phone);

            if (substr($rawPhone, 0, 2) === '62') {
                $phone = $rawPhone;
            } elseif (substr($rawPhone, 0, 1) === '0') {
                $phone = '62' . substr($rawPhone, 1);
            } else {
                $phone = '62' . ltrim($rawPhone, '+');
            }

            $details = [];
            foreach ($order->permintaans as $permintaan) {
                $merk = $permintaan->merk ? $permintaan->merk->name : '-';
                $ukuran = $permintaan->ukuran ? $permintaan->ukuran->name : '-';
                $motif = $permintaan->motif ?? '-';
                $estimasi = $permintaan->estimasi ?? '-';

                $details[] = "• {$merk}, {$ukuran}, {$motif}, Qty: {$estimasi}";
            }

            $detailText = implode("\n", $details);
            $formattedTanggal = \Carbon\Carbon::parse($order->tanggal)
                ->translatedFormat('d F Y');

            $message = "Halo {$user->name} orderanmu dengan kode - {$order->kode} di tanggal order {$formattedTanggal} telah masuk!\n\nDetail pesanan:\n{$detailText}\n\nTerima Kasih telah memesan :)";

            $text = urlencode($message);

            $waUrl = "https://api.whatsapp.com/send/?phone={$phone}&text={$text}";

            return response()->json(['success' => true, 'wa_url' => $waUrl]);
        }

        return response()->json(['success' => false, 'message' => 'Nomor WA user tidak ditemukan atau bukan role_as 0.']);
    }

    public function markPaidEmail(Order $order)
    {
        $order->status = 1;
        $order->save();

        $user = $order->user;

        if ($user && $user->role_as == 0 && $user->email) {

            // Format detail pesanan
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

            $message = "Halo {$user->name},\n\nOrderanmu dengan kode *{$order->kode}* di tanggal {$formattedTanggal} telah masuk!\n\nDetail pesanan:\n{$detailText}\n\nTerima kasih telah memesan :)";

            try {
                Mail::raw($message, function ($mail) use ($user) {
                    $mail->to($user->email)
                        ->subject('Pesanan Kamu Telah Masuk')
                        ->from(config('mail.from.address'), config('mail.from.name'));
                });

                return response()->json(['success' => true]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengirim email: ' . $e->getMessage()
                ]);
            }
        }

        return response()->json(['success' => false, 'message' => 'Email user tidak ditemukan atau bukan role_as 0.']);
    }

    public function showTransaksi($orderId)
    {
        $order = Order::with(['permintaans,merk', 'permintaans.ukuran'])->findOrFail($orderId);

        return response()->json($order);
    }

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
            ['start' => ['10-26'], 'end' => ['11-25'], 'hasil' => 'Desember'],
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

    // Log Aktivitas
    public function indexLog(Request $request)
    {
        $query = Order::with(['user', 'approval']);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
            $end = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();

            $query->where(function ($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end])
                ->orWhereBetween('updated_at', [$start, $end]);
            });
        }

        $logs = $query->orderByDesc('updated_at')->paginate(10)->withQueryString();

        return view('admin.log.index', compact('logs'));
    }


    public function generateForecast(Request $request)
    {
        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');

        if (!$tanggalAwal || !$tanggalAkhir) {
            return redirect()->back()->with('error', 'Tanggal filter wajib diisi.');
        }

        return Excel::download(new ForecastExport($tanggalAwal, $tanggalAkhir), 'forecast_period_' . now()->format('Ymd_His') . '.xlsx');
    }

    public function generateTotalForecast(Request $request)
    {
        $bulanDipilih = $request->get('bulan'); // [8, 9, 10] dll\
        $target = $request->get('target');

        $mapping = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $tahunSekarang = now()->year;
        $forecastList = [];

        foreach ($bulanDipilih as $bulan) {
            $namaBulan = $mapping[(int)$bulan];
            $forecastList[] = "$namaBulan $tahunSekarang";
        }

        return Excel::download(new \App\Exports\TotalForecastExport($forecastList , $target), 'total_forecast.xlsx');
    }
}
