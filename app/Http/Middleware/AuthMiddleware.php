<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {

        $jwt = $request->bearerToken();
        \Log::info('AuthMiddleware invoked');

        if (is_null($jwt) || $jwt == '') {
            \Log::warning('No JWT token found');
            return response()->json([
                'msg' => 'Akses ditolak, token tidak memenuhi'
            ], 401);
        } else {
            try {
                // Tambahkan logging untuk debug
                \Log::info('Token diterima: ' . $jwt);

                $jwtDecoded = JWT::decode($jwt, new Key(env('JWT_SECRET_KEY'), 'HS256'));

                // Ambil pengguna dari payload token
                $user = User::find($jwtDecoded->sub);

                if (!$user) {
                    return response()->json([
                        'msg' => 'Akses ditolak, pengguna tidak ditemukan'
                    ], 401);
                }

                $request->auth = $user;
                \Log::info('User authenticated: ' . $user->role);

                return $next($request);
            } catch (\Exception $e) {
                // Log the error for debugging
                \Log::error('Error decoding token: ' . $e->getMessage());

                return response()->json([
                    'msg' => 'Token tidak valid'
                ], 401);
            }
        }
    }
}