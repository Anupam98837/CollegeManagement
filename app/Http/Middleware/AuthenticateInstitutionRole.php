<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\DB;

class AuthenticateInstitutionRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next , ...$roles)
    {

        $token = $request->bearerToken() ?? $request->header('Authorization'); 
        $hashedToken = hash('sha256', $token);
        foreach ($roles as $role) {
            
            $userData = DB::table('personal_access_tokens')
                ->where('token', $hashedToken)
                ->where('designation', $role)
                ->first();
        
            if ($userData) {
                return $next($request);
            }
        }
        return response()->json(['error' => 'Unauthorized Access'], 403);
        
    }
}
