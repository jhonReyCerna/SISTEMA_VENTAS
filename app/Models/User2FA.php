<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class User2FA extends Model
{
    protected $table = 'user_2fa';

    protected $fillable = [
        'user_id',
        'secret_key',
        'is_enabled',
        'backup_codes',
        'enabled_at',
        'last_used_at',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'enabled_at' => 'datetime',
        'last_used_at' => 'datetime',
        'backup_codes' => 'array',
    ];

    /**
     * Get the user that owns the 2FA.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_usuario');
    }

    /**
     * Get the encrypted secret key.
     */
    public function getSecretKeyAttribute($value)
    {
        return $value ? $value : null;
    }

    /**
     * Set the secret key (sin encriptar por ahora para evitar problemas de longitud).
     */
    public function setSecretKeyAttribute($value)
    {
        $this->attributes['secret_key'] = $value;
    }

    /**
     * Generate backup codes.
     */
    public static function generateBackupCodes()
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(bin2hex(random_bytes(4)));
        }
        return $codes;
    }

    /**
     * Use a backup code.
     */
    public function useBackupCode($code)
    {
        $codes = $this->backup_codes ?? [];
        $key = array_search(strtoupper($code), $codes);
        
        if ($key !== false) {
            unset($codes[$key]);
            $this->backup_codes = array_values($codes);
            $this->last_used_at = now();
            $this->save();
            return true;
        }
        
        return false;
    }
}
