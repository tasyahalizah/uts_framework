<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;


class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        Log::info('AdminMiddleware invoked');

        if ($request->auth && $request->auth->role === 'admin' 
        ) {
            Log::info('Role admin authenticated');
            return $next($request);
        }
        
        Log::warning('Access denied for role: ' . ($request->auth ? $request->auth->role : 'None'));

        return response()->json([
            'msg' => 'Akses ditolak, hanya admin yang dapat mengakses'
        ], 403);
        
    }
    

}