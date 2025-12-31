<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class LoginHistory extends Model
{
    protected $table = 'login_history';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'device_name',
        'device_type',
        'browser',
        'platform',
        'country',
        'city',
        'login_status',
        'failure_reason',
        'is_2fa_used',
        'login_at',
        'logout_at',
        'session_duration',
    ];

    protected $casts = [
        'is_2fa_used' => 'boolean',
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
    ];

    /**
     * Get the user that owns the login history.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_usuario');
    }

    /**
     * Create a login record.
     */
    public static function createLoginRecord($user, $status = 'success', $failureReason = null, $is2faUsed = false)
    {
        $ipAddress = Request::ip();
        $userAgent = Request::userAgent();
        
        $deviceInfo = UserSession::parseUserAgent($userAgent);
        
        // Try to get location (basic implementation)
        $location = self::getLocationFromIP($ipAddress);
        
        return self::create([
            'user_id' => $user ? $user->id_usuario : null,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'device_name' => $deviceInfo['device_name'],
            'device_type' => $deviceInfo['device_type'],
            'browser' => $deviceInfo['browser'],
            'platform' => $deviceInfo['platform'],
            'country' => $location['country'] ?? null,
            'city' => $location['city'] ?? null,
            'login_status' => $status,
            'failure_reason' => $failureReason,
            'is_2fa_used' => $is2faUsed,
            'login_at' => now(),
        ]);
    }

    /**
     * Update logout information.
     */
    public function recordLogout()
    {
        $this->logout_at = now();
        $this->session_duration = $this->login_at->diffInMinutes(now());
        $this->save();
    }

    /**
     * Get location from IP (basic implementation).
     */
    private static function getLocationFromIP($ip)
    {
        // Basic implementation - you can integrate with services like ipinfo.io, maxmind, etc.
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return ['country' => 'Local', 'city' => 'Local'];
        }
        
        // For now, return null - you can implement actual geolocation here
        return ['country' => null, 'city' => null];
    }

    /**
     * Get recent login attempts for user.
     */
    public static function getRecentAttempts($userId, $hours = 24)
    {
        return self::where('user_id', $userId)
                  ->where('login_at', '>=', now()->subHours($hours))
                  ->orderBy('login_at', 'desc')
                  ->get();
    }

    /**
     * Get failed login attempts from IP.
     */
    public static function getFailedAttemptsFromIP($ip, $minutes = 15)
    {
        return self::where('ip_address', $ip)
                  ->where('login_status', 'failed')
                  ->where('login_at', '>=', now()->subMinutes($minutes))
                  ->count();
    }

    /**
     * Check if IP is suspicious.
     */
    public static function isSuspiciousIP($ip, $userId = null)
    {
        $failedAttempts = self::getFailedAttemptsFromIP($ip, 15);
        
        // If more than 5 failed attempts in 15 minutes, consider suspicious
        if ($failedAttempts >= 5) {
            return true;
        }
        
        // If user has never logged in from this IP, it might be suspicious
        if ($userId) {
            $previousLogins = self::where('user_id', $userId)
                                 ->where('ip_address', $ip)
                                 ->where('login_status', 'success')
                                 ->count();
            
            return $previousLogins === 0;
        }
        
        return false;
    }
}
