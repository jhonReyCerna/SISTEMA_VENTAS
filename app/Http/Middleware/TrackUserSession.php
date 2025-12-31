<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\UserSession;

class TrackUserSession
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $this->updateUserSession($request);
        }

        return $next($request);
    }

    /**
     * Update or create user session record.
     */
    protected function updateUserSession(Request $request)
    {
        $sessionId = Session::getId();
        $user = Auth::user();
        
        // Parse device information
        $deviceInfo = UserSession::parseUserAgent($request->userAgent());
        
        // Update or create session record
        UserSession::updateOrCreate(
            ['id' => $sessionId],
            [
                'user_id' => $user->id_usuario,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'payload' => '', // Laravel handles this internally
                'device_name' => $deviceInfo['device_name'],
                'device_type' => $deviceInfo['device_type'],
                'browser' => $deviceInfo['browser'],
                'platform' => $deviceInfo['platform'],
                'is_current' => true,
                'last_activity' => time(),
                'created_at' => now(),
            ]
        );

        // Mark other sessions as not current
        UserSession::where('user_id', $user->id_usuario)
                   ->where('id', '!=', $sessionId)
                   ->update(['is_current' => false]);

        // Clean old sessions (older than session lifetime)
        $sessionLifetime = config('session.lifetime', 120) * 60;
        UserSession::where('last_activity', '<', time() - $sessionLifetime)->delete();
    }
}
