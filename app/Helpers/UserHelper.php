<?php

namespace App\Helpers;

class UserHelper
{
    /**
     * Cek apakah user sudah login
     */
    public static function isLoggedIn()
    {
        return session()->has('is_logged_in') && session('is_logged_in') === true;
    }

    /**
     * Ambil data user dari session
     */
    public static function user()
    {
        if (!self::isLoggedIn()) {
            return null;
        }

        return (object) [
            'nik' => session('nik'),
            'nama' => session('username'),
            'level' => session('level'),
            'plant' => session('plant'),
            'kode_jabatan' => session('kode_jabatan'),
            'divisi' => session('divisi'),
            'dept' => session('dept'),
            'jabatan' => session('jabatan'),
            'email' => session('email'),
            'posisi' => session('posisi', []),
            'role' => session('role', ''),
            'all_data' => session('api_user_data', [])
        ];
    }

    /**
     * Ambil specific field dari user
     */
    public static function get($key, $default = null)
    {
        return session($key, $default);
    }

    /**
     * Cek apakah user memiliki level tertentu
     */
    public static function hasLevel($level)
    {
        $userLevel = session('level');
        
        if (is_array($level)) {
            return in_array($userLevel, $level);
        }
        
        return $userLevel == $level;
    }
}