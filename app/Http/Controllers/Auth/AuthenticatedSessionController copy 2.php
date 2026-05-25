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
     */
    public function create(): View|RedirectResponse
    {
        if (session()->has('nik')) {
            return redirect($this->getRedirectPath(session('role')));
        }
        
        return view('auth.login');
    }

    /**
     * Handle API callback setelah login sukses
     */
    public function apiCallback(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            
            if (!isset($data['nik']) || !isset($data['nama'])) {
                throw new \Exception('Data tidak lengkap dari API');
            }
            
            // Tentukan role dan posisi jika tidak dikirim dari API
            $role = $data['role'] ?? $this->determineRole($data);
            $posisi = $data['posisi'] ?? $this->determinePosisi($role, $data);
            
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
                'posisi' => $posisi,
                'role' => $role,
                'api_user_data' => $data
            ]);
            
            $request->session()->regenerate();
            
            $redirect = $this->getRedirectPath($role);
            
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
     * Tentukan role berdasarkan data
     */
    private function determineRole(array $data): string
    {
        $kodeJabatan = $data['kode_jabatan'] ?? '';
        $level = $data['level'] ?? '';
        
        if ($kodeJabatan == '0001' || $level == '1' || $level == 'ADMIN') {
            return 'admin';
        } elseif ($level == '2' || $level == 'HEAD' || $level == 'MANAGER') {
            return 'atasan';
        }
        
        return 'staff';
    }
    
    /**
     * Tentukan posisi berdasarkan role
     */
    private function determinePosisi(string $role, array $data): string
    {
        if ($role == 'admin') {
            return 'admin';
        } elseif ($role == 'atasan') {
            return 'atasan';
        }
        
        return 'staff';
    }
    
    /**
     * Tentukan redirect path berdasarkan role
     */
    private function getRedirectPath($role): string
    {
        if ($role == 'admin') {
            return '/admin/welcome';
        } elseif ($role == 'atasan') {
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