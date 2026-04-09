<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SelfReportController extends Controller
{
    public function index()
    {
        // Hanya untuk render view, data akan diambil via API
        return view('selfreport.index');
    }
}