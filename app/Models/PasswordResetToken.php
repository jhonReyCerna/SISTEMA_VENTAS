<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Request;

class PasswordResetToken extends Model
{
    protected $table = 'password_reset_tokens';
    public $timestamps = false;

    protected $fillable = [
        'email',
        'token',
        'ip_address',
        'user_agent',
        'used',
        'used_at',
        'expires_at',
        'created_at',
    ];

    protected $casts = [
        'used' => 'boolean',
        'used_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * Create a new password reset token.
     */
    public static function createToken($email, $expirationMinutes = 60)
    {
        // Clean old tokens for this email
        self::where('email', $email)->delete();
        
        $token = Str::random(64);
        
        $record = self::create([
            'email' => $email,
            'token' => Hash::make($token),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'expires_at' => now()->addMinutes($expirationMinutes),
            'created_at' => now(),
        ]);
        
        // Return the plain token for email sending
        $record->plain_token = $token;
        return $record;
    }

    /**
     * Verify if token is valid.
     */
    public static function isValidToken($email, $token)
    {
        $record = self::where('email', $email)
                     ->where('used', false)
                     ->where('expires_at', '>', now())
                     ->first();
        
        if (!$record) {
            return false;
        }
        
        return Hash::check($token, $record->token);
    }

    /**
     * Use the token (mark as used).
     */
    public static function useToken($email, $token)
    {
        $record = self::where('email', $email)
                     ->where('used', false)
                     ->where('expires_at', '>', now())
                     ->first();
        
        if (!$record || !Hash::check($token, $record->token)) {
            return false;
        }
        
        $record->used = true;
        $record->used_at = now();
        $record->save();
        
        return true;
    }

    /**
     * Clean expired tokens.
     */
    public static function cleanExpired()
    {
        return self::where('expires_at', '<', now())->delete();
    }

    /**
     * Get token attempts from IP.
     */
    public static function getAttemptsFromIP($ip, $hours = 1)
    {
        return self::where('ip_address', $ip)
                  ->where('created_at', '>=', now()->subHours($hours))
                  ->count();
    }

    /**
     * Check if IP is rate limited.
     */
    public static function isRateLimited($ip, $maxAttempts = 5)
    {
        return self::getAttemptsFromIP($ip, 1) >= $maxAttempts;
    }
}
