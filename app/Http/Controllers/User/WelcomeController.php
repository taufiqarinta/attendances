<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SubProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WelcomeController extends Controller
{
    public function index(Request $request)
    {
        dd("Masuk bosq");
        $user = Auth::user();
        $search = $request->input('search', '');

        // $query = SubProject::query();

        // if ($search) {
        //     $query->where('nama_pt', 'like', "%{$search}%")
        //         ->orWhere('nama_sub_project', 'like', "%{$search}%");
        // }

        // $subProjects = $query->paginate(10);

        // Jika request dari AJAX, kirim hanya partial table (tanpa layout)
        if ($request->ajax()) {
            return view('partials.subproject-table', compact('subProjects'))->render();
        }

        session()->flash('status', 'Selamat datang, ' . $user->name);
        return view('dashboard', compact('user', 'search'));
    }
}
