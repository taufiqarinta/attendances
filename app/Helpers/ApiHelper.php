<?php

namespace App\Helpers;

class ApiHelper
{
    public static function getApiUrl($endpoint)
    {
        $baseUrl = config('attendance.base_url');
        $mode = config('attendance.mode');
        
        // Jika mode test
        if ($mode === 'test') {
            // Cek apakah base URL sudah mengandung /test
            if (strpos($baseUrl, '/test') !== false) {
                return rtrim($baseUrl, '/') . '/' . ltrim($endpoint, '/');
            }
            
            // Sisipkan /test
            $parsed = parse_url($baseUrl);
            $path = isset($parsed['path']) ? rtrim($parsed['path'], '/') : '';
            $newPath = $path . '/test';
            
            $url = $parsed['scheme'] . '://' . $parsed['host'];
            if (isset($parsed['port'])) {
                $url .= ':' . $parsed['port'];
            }
            $url .= $newPath . '/' . ltrim($endpoint, '/');
            
            return $url;
        }
        
        // Mode live
        return rtrim($baseUrl, '/') . '/' . ltrim($endpoint, '/');
    }
}