<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckApiSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah session memiliki nik (tanda sudah login)
        if (!$request->session()->has('nik')) {
            return redirect('/')->with('error', 'Silahkan login terlebih dahulu');
        }

        return $next($request);
    }
}