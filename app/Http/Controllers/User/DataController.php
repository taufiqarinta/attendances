<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DataController extends Controller
{
    public function data(Request $request)
    {
        $user = Auth::user();
        $userLocationId = $user->location_id;

        $query = File::with(['location', 'user'])->where('location_id', $userLocationId);

        if ($request->has('tanggal') && $request->tanggal != '') {
            $query->whereDate('created_at', $request->tanggal);
        }

        $files = $query->orderBy('created_at', 'desc')->paginate(10);

        $files->appends($request->only('tanggal'));

        $locations = Location::find($userLocationId);

        return view('data.index', compact('locations', 'files'));
    }
}
