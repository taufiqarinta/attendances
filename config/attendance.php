<?php

return [
    'base_url' => env('API_BASE_URL', 'https://web.kobin.co.id/api/hris'),
    'mode' => env('API_MODE', 'live'),
    
    // Helper untuk mendapatkan full URL
    'get_url' => function($endpoint) {
        $baseUrl = config('attendance.base_url');
        $mode = config('attendance.mode');
        
        // Jika mode test, tambahkan /test di path
        if ($mode === 'test') {
            // Parse URL untuk menyisipkan /test
            $parsed = parse_url($baseUrl);
            $path = isset($parsed['path']) ? $parsed['path'] : '';
            
            // Hapus trailing slash di path
            $path = rtrim($path, '/');
            
            // Sisipkan /test sebelum endpoint
            $newPath = $path . '/test/' . ltrim($endpoint, '/');
            
            // Rebuild URL
            $url = $parsed['scheme'] . '://' . $parsed['host'];
            if (isset($parsed['port'])) {
                $url .= ':' . $parsed['port'];
            }
            $url .= '/' . ltrim($newPath, '/');
            
            return $url;
        }
        
        // Mode live
        return rtrim($baseUrl, '/') . '/' . ltrim($endpoint, '/');
    },
];