<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class LogActividad extends Model
{
    use HasFactory;

    protected $table = 'logs_actividad';
    protected $primaryKey = 'id_log';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'accion',
        'modulo',
        'descripcion',
        'datos_anteriores',
        'datos_nuevos',
        'ip',
        'user_agent',
        'fecha_hora'
    ];

    protected $casts = [
        'datos_anteriores' => 'array',
        'datos_nuevos' => 'array',
        'fecha_hora' => 'datetime'
    ];

    /**
     * Usuario que realizó la acción
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Registrar una nueva actividad
     */
    public static function registrar($accion, $modulo, $descripcion, $datos_anteriores = null, $datos_nuevos = null)
    {
        return self::create([
            'id_usuario' => Auth::id(),
            'accion' => $accion,
            'modulo' => $modulo,
            'descripcion' => $descripcion,
            'datos_anteriores' => $datos_anteriores ? json_encode($datos_anteriores) : null,
            'datos_nuevos' => $datos_nuevos ? json_encode($datos_nuevos) : null,
            'ip' => Request::ip(),
            'user_agent' => Request::userAgent()
        ]);
    }

    /**
     * Log de creación
     */
    public static function logCrear($modulo, $descripcion, $datos = null)
    {
        return self::registrar('crear', $modulo, $descripcion, null, $datos);
    }

    /**
     * Log de edición
     */
    public static function logEditar($modulo, $descripcion, $datos_anteriores, $datos_nuevos)
    {
        return self::registrar('editar', $modulo, $descripcion, $datos_anteriores, $datos_nuevos);
    }

    /**
     * Log de eliminación
     */
    public static function logEliminar($modulo, $descripcion, $datos = null)
    {
        return self::registrar('eliminar', $modulo, $descripcion, $datos, null);
    }

    /**
     * Log de login/logout
     */
    public static function logAutenticacion($accion, $descripcion)
    {
        return self::registrar($accion, 'autenticacion', $descripcion);
    }
}
