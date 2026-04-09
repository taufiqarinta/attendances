<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    /**
     * Display the self report page
     */
    public function selfReport()
    {
        $periodeNow = $this->getPeriodeNow();
        $bawahan = $this->getBawahan(session('nik'));
        $data = $this->getSelfReport(date('Y-m-d', strtotime($periodeNow[0]->Periode ?? 'now')), session('nik'));
        
        return view('selfreport.index', compact('data', 'periodeNow', 'bawahan'));
    }
}