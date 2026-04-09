<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     * 
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        // Jika sudah login, redirect ke dashboard
        if (session()->has('nik')) {
            return redirect($this->getRedirectPath(session('level')));
        }
        
        return view('auth.login');
    }

    /**
     * Handle API callback setelah login sukses dari frontend
     */
    public function apiCallback(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            
            // Validasi data dari API
            if (!isset($data['nik']) || !isset($data['nama'])) {
                throw new \Exception('Data tidak lengkap dari API');
            }
            
            // Simpan data user ke session
            session([
                'is_logged_in' => true,
                'nik' => $data['nik'],
                'username' => $data['nama'],
                'level' => $data['level'] ?? 'user',
                'plant' => $data['plant'] ?? '',
                'kode_jabatan' => $data['kode_jabatan'] ?? '',
                'comp' => $data['comp'] ?? '',
                'tglmasuk' => $data['tglmasuk'] ?? '',
                'divisi' => $data['divisi'] ?? '',
                'dept' => $data['dept'] ?? '',
                'jabatan' => $data['jabatan'] ?? '',
                'email' => $data['email'] ?? '',
                'posisi' => $data['posisi'] ?? [],
                'role' => $data['role'] ?? '',
                // Simpan seluruh data untuk keperluan lain
                'api_user_data' => $data
            ]);
            
            // Regenerasi session untuk keamanan
            $request->session()->regenerate();
            
            // Tentukan redirect berdasarkan level
            $redirect = $this->getRedirectPath($data['level'] ?? '');
            
            return response()->json([
                'success' => true,
                'redirect' => $redirect,
                'message' => 'Login berhasil'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat session: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tentukan redirect path berdasarkan level
     */
    private function getRedirectPath($level)
    {
        // Sesuaikan dengan level di API Anda
        if (in_array($level, ['1', 'ADMIN', 'Super User', 'Super Admin'])) {
            return '/admin/welcome';
        } elseif (in_array($level, ['2', 'HEAD', 'Manager', 'Kepala'])) {
            return '/admin/welcome';
        } else {
            return '/dashboard';
        }
    }

    /**
     * Handle logout
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Hapus semua session
        $request->session()->forget([
            'is_logged_in',
            'nik',
            'username',
            'level',
            'plant',
            'kode_jabatan',
            'comp',
            'tglmasuk',
            'divisi',
            'dept',
            'jabatan',
            'email',
            'posisi',
            'role',
            'api_user_data'
        ]);
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}