<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class UserSession extends Model
{
    protected $table = 'user_sessions';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'user_id',
        'ip_address',
        'user_agent',
        'payload',
        'device_name',
        'device_type',
        'browser',
        'platform',
        'is_current',
        'last_activity',
        'created_at',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'last_activity' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Get the user that owns the session.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_usuario');
    }

    /**
     * Parse user agent and populate device info.
     */
    public static function parseUserAgent($userAgent)
    {
        // Basic parsing without external dependency initially
        $deviceType = 'desktop';
        $browser = 'Unknown';
        $platform = 'Unknown';
        
        if (stripos($userAgent, 'mobile') !== false || stripos($userAgent, 'android') !== false) {
            $deviceType = 'mobile';
        } elseif (stripos($userAgent, 'tablet') !== false || stripos($userAgent, 'ipad') !== false) {
            $deviceType = 'tablet';
        }
        
        // Basic browser detection
        if (stripos($userAgent, 'chrome') !== false) {
            $browser = 'Chrome';
        } elseif (stripos($userAgent, 'firefox') !== false) {
            $browser = 'Firefox';
        } elseif (stripos($userAgent, 'safari') !== false) {
            $browser = 'Safari';
        } elseif (stripos($userAgent, 'edge') !== false) {
            $browser = 'Edge';
        }
        
        // Basic platform detection
        if (stripos($userAgent, 'windows') !== false) {
            $platform = 'Windows';
        } elseif (stripos($userAgent, 'mac') !== false) {
            $platform = 'macOS';
        } elseif (stripos($userAgent, 'linux') !== false) {
            $platform = 'Linux';
        } elseif (stripos($userAgent, 'android') !== false) {
            $platform = 'Android';
        } elseif (stripos($userAgent, 'ios') !== false) {
            $platform = 'iOS';
        }
        
        return [
            'device_type' => $deviceType,
            'browser' => $browser,
            'platform' => $platform,
            'device_name' => $platform . ' ' . $browser,
        ];
    }

    /**
     * Get active sessions for user.
     */
    public static function getActiveSessions($userId)
    {
        return self::where('user_id', $userId)
                  ->where('last_activity', '>', time() - config('session.lifetime', 120) * 60)
                  ->orderBy('last_activity', 'desc')
                  ->get();
    }

    /**
     * Terminate session.
     */
    public function terminate()
    {
        return $this->delete();
    }

    /**
     * Check if session is expired.
     */
    public function isExpired()
    {
        return $this->last_activity < (time() - config('session.lifetime', 120) * 60);
    }

    /**
     * Get formatted last activity.
     */
    public function getLastActivityFormattedAttribute()
    {
        return date('d/m/Y H:i:s', $this->last_activity);
    }
}
