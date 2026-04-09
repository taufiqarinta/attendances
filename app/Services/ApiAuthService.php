<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiAuthService
{
    protected $apiUrl;
    protected $apiPersonalUrl;
    
    public function __construct()
    {
        // URL API login - sesuaikan dengan domain/server API Anda
        $this->apiUrl = env('API_LOGIN_URL', 'http://192.168.1.51/api_login.php');
        $this->apiPersonalUrl = env('API_PERSONAL_DATA_URL', 'http://192.168.1.51/api_get_personal_data.php');
    }
    
    /**
     * Attempt login via API
     */
    public function attemptLogin($nik, $password)
    {
        try {
            $response = Http::timeout(10)->post($this->apiUrl, [
                'nik' => $nik,
                'password' => $password
            ]);
            
            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('API Login gagal: ' . $response->body());
                return [
                    'success' => false,
                    'message' => 'Gagal terhubung ke server autentikasi'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Exception saat login via API: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan koneksi ke server: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get personal data from API
     */
    public function getPersonalData($nik)
    {
        try {
            $response = Http::timeout(10)->post($this->apiPersonalUrl, [
                'nik' => $nik
            ]);
            
            if ($response->successful()) {
                $result = $response->json();
                
                if ($result['success'] ?? false) {
                    return $result['data'] ?? [];
                }
            }
            
            return [];
        } catch (\Exception $e) {
            Log::error('Exception saat get personal data via API: ' . $e->getMessage());
            return [];
        }
    }
}