<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class SimpleDummyAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization');
        
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
        
        $token = substr($authHeader, 7); // Remove "Bearer "
        
        // Simple dummy authentication for testing (DB-less)
        if (str_starts_with($token, 'dummy_token_')) {
            // Create a dummy user object without database
            $dummyUser = new User([
                'id' => 1,
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
            $dummyUser->exists = true;
            
            // Set authenticated user manually
            Auth::setUser($dummyUser);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token'
            ], 401);
        }
        
        return $next($request);
    }
}
