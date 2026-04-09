<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\FormOrder;
use App\Models\MasterLokasiEvent;
use Illuminate\Support\Facades\DB;

class Peringkat extends Component
{
    public $selectedLokasi = '';
    public $lokasiEvents = [];

    protected $listeners = ['refreshPeringkat' => '$refresh'];

    public function mount()
    {
        $this->lokasiEvents = MasterLokasiEvent::orderBy('nama_lokasi')->get();
    }

    public function getPeringkatProperty()
    {
        $query = FormOrder::select(
            'nama_toko',
            'no_hp',
            'pic',
            'kota',
            DB::raw('SUM(total_point) as total_point_accumulated')
        )
        ->groupBy('nama_toko', 'no_hp', 'pic', 'kota');

        if ($this->selectedLokasi) {
            $query->where('lokasi_event', $this->selectedLokasi);
        }

        return $query->orderByDesc('total_point_accumulated')
            ->limit(10)
            ->get()
            ->map(function ($item, $index) {
                $item->peringkat = $index + 1;
                return $item;
            });
    }

    public function updatedSelectedLokasi()
    {
        // Method ini akan otomatis dipanggil ketika selectedLokasi berubah
        // Livewire akan me-refresh data secara otomatis
    }

    public function render()
    {
        return view('livewire.peringkat', [
            'peringkat' => $this->peringkat
        ]);
    }
}