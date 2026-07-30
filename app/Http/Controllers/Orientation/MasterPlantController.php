<?php

namespace App\Http\Controllers\Orientation;

use App\Http\Controllers\Controller;
use App\Models\Orientation\MasterPlant;

class MasterPlantController extends Controller
{
    public function index()
    {
        $plants = MasterPlant::orderBy('name_plant')->get();

        return response()->json($plants);
    }
}