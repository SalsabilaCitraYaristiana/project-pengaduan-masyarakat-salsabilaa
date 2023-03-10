<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CekRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    // ...$roles -> CekRole:admin,petugas -> $roles = ['admin', 'petugas'];
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // ...$roles akan mengubah string yang dipisah dengan koma menjadi item array, namanya spread operator
        // $request->user()->role akan ambil data user yg login bagian role
        if (in_array($request->user()->role, $roles)) {
            return $next($request);
        }else {
            return redirect()->back();
        }
    }  
}
