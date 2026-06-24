<?php

namespace App\Helpers;

class ApiHelper
{
    /**
     * Bangun full URL endpoint, otomatis sisipkan /test
     * kalau APP_MODE (config attendance.mode) = 'test'.
     */
    public static function getApiUrl(string $endpoint): string
    {
        $baseUrl = config('attendance.base_url');
        $mode    = config('attendance.mode');

        $parsed = parse_url($baseUrl);
        $path   = isset($parsed['path']) ? rtrim($parsed['path'], '/') : '';

        // Mode test: sisipkan /test kalau belum ada di path
        if ($mode === 'test' && !str_contains($path, '/test')) {
            $path .= '/test';
        }

        $url = $parsed['scheme'] . '://' . $parsed['host'];

        if (isset($parsed['port'])) {
            $url .= ':' . $parsed['port'];
        }

        $url .= $path . '/' . ltrim($endpoint, '/');

        return $url;
    }
}