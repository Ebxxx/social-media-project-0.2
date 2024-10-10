<?php

// namespace App\Http\Controllers\Auth;

// use App\Http\Controllers\Controller;
// use App\Http\Requests\Auth\LoginRequest;
// use Illuminate\Http\Request;
// use Illuminate\Http\Response;
// use Illuminate\Support\Facades\Auth;

// class AuthenticatedSessionController extends Controller
// {
//     /**
//      * Handle an incoming authentication request.
//      */
//     public function store(LoginRequest $request): Response
//     {
//         $request->authenticate();

//         $request->session()->regenerate();

//         return response()->noContent();
//     }

//     /**
//      * Destroy an authenticated session.
//      */
//     public function destroy(Request $request): Response
//     {
//         Auth::guard('web')->logout();

//         $request->session()->invalidate();

//         $request->session()->regenerateToken();

//         return response()->noContent();
//     }
// }


namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): Response
    {
        // Authenticate the user
        $request->authenticate();
        
        // Regenerate session
        $request->session()->regenerate();

        // Get the authenticated user
        $user = Auth::user();

        // Insert session data into 'sessions' table
        DB::table('sessions')->updateOrInsert(
            ['user_id' => $user->id],
            [
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'last_login_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return response()->noContent();
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        // Log out the user
        Auth::guard('web')->logout();

        // Invalidate the session
        $request->session()->invalidate();

        // Regenerate CSRF token
        $request->session()->regenerateToken();

        // Remove session data from 'sessions' table
        DB::table('sessions')->where('user_id', Auth::id())->delete();

        return response()->noContent();
    }
}

