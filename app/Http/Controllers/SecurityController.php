<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use PragmaRX\Google2FA\Google2FA;
use App\Models\User;
use App\Models\User2FA;
use App\Models\UserSession;
use App\Models\LoginHistory;
use App\Models\PasswordResetToken;

class SecurityController extends Controller
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
        // Middleware se maneja en las rutas, no en el constructor
    }

    // 🔐 2FA Management
    public function show2FASetup()
    {
        $user = Auth::user();
        $user2fa = User2FA::where('user_id', $user->id_usuario)->first();
        
        if (!$user2fa) {
            // Generate new secret
            $secret = $this->google2fa->generateSecretKey();
            $qrCodeUrl = $this->google2fa->getQRCodeUrl(
                config('app.name'),
                $user->email,
                $secret
            );
            
            // Create temporary 2FA record
            $user2fa = User2FA::create([
                'user_id' => $user->id_usuario,
                'secret_key' => $secret,
                'is_enabled' => false,
                'backup_codes' => User2FA::generateBackupCodes(),
            ]);
        } else {
            $qrCodeUrl = $this->google2fa->getQRCodeUrl(
                config('app.name'),
                $user->email,
                $user2fa->secret_key
            );
        }

        return view('security.2fa-setup', compact('user2fa', 'qrCodeUrl'));
    }

    public function enable2FA(Request $request)
    {
        $request->validate([
            'code' => 'required|string|min:6|max:6',
        ]);

        $user = Auth::user();
        $user2fa = User2FA::where('user_id', $user->id_usuario)->first();
        
        if (!$user2fa) {
            return back()->withErrors(['code' => 'Configuración 2FA no encontrada.']);
        }

        $valid = $this->google2fa->verifyKey($user2fa->secret_key, $request->code);
        
        if (!$valid) {
            return back()->withErrors(['code' => 'Código de verificación inválido.']);
        }

        $user2fa->update([
            'is_enabled' => true,
            'enabled_at' => now(),
            'last_used_at' => now(),
        ]);

        return redirect()->route('security.dashboard')->with('success', '¡Autenticación de dos factores habilitada exitosamente!');
    }

    public function disable2FA(Request $request)
    {
        $request->validate([
            'password' => 'required',
            'code' => 'required|string|min:6|max:8',
        ]);

        $user = Auth::user();
        
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Contraseña incorrecta.']);
        }

        $user2fa = User2FA::where('user_id', $user->id_usuario)->where('is_enabled', true)->first();
        
        if (!$user2fa) {
            return back()->withErrors(['code' => '2FA no está habilitado.']);
        }

        // Verify 2FA code or backup code
        $valid = $this->google2fa->verifyKey($user2fa->secret_key, $request->code) || 
                 $user2fa->useBackupCode($request->code);
        
        if (!$valid) {
            return back()->withErrors(['code' => 'Código de verificación inválido.']);
        }

        $user2fa->delete();

        return redirect()->route('security.dashboard')->with('success', 'Autenticación de dos factores deshabilitada.');
    }

    // 💻 Session Management
    public function showSessions()
    {
        $user = Auth::user();
        $currentSessionId = Session::getId();
        $activeSessions = UserSession::getActiveSessions($user->id_usuario);

        return view('security.sessions', compact('activeSessions', 'currentSessionId'));
    }

    public function terminateSession(Request $request)
    {
        $sessionId = $request->session_id;
        $currentSessionId = Session::getId();
        
        if ($sessionId === $currentSessionId) {
            return response()->json(['error' => 'No puedes cerrar tu sesión actual desde aquí.'], 400);
        }

        $session = UserSession::find($sessionId);
        if ($session && $session->user_id === Auth::id()) {
            $session->terminate();
            return response()->json(['success' => 'Sesión terminada exitosamente.']);
        }

        return response()->json(['error' => 'Sesión no encontrada.'], 404);
    }

    public function terminateAllSessions(Request $request)
    {
        $user = Auth::user();
        $currentSessionId = Session::getId();
        
        $request->validate([
            'password' => 'required',
        ]);

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Contraseña incorrecta.']);
        }

        // Terminate all sessions except current
        UserSession::where('user_id', $user->id_usuario)
                  ->where('id', '!=', $currentSessionId)
                  ->delete();

        return redirect()->route('security.sessions')->with('success', 'Todas las demás sesiones han sido cerradas.');
    }

    // 📊 Login History
    public function showLoginHistory()
    {
        $user = Auth::user();
        $loginHistory = LoginHistory::where('user_id', $user->id_usuario)
                                   ->orderBy('login_at', 'desc')
                                   ->paginate(20);

        $recentAttempts = LoginHistory::getRecentAttempts($user->id_usuario, 24);
        $stats = [
            'total_logins' => LoginHistory::where('user_id', $user->id_usuario)->where('login_status', 'success')->count(),
            'failed_attempts' => LoginHistory::where('user_id', $user->id_usuario)->where('login_status', 'failed')->count(),
            'unique_ips' => LoginHistory::where('user_id', $user->id_usuario)->distinct('ip_address')->count('ip_address'),
            'devices' => LoginHistory::where('user_id', $user->id_usuario)->distinct('device_name')->count('device_name'),
        ];

        return view('security.login-history', compact('loginHistory', 'recentAttempts', 'stats'));
    }

    // 🔑 Password Reset
    public function showPasswordResetForm(Request $request)
    {
        $token = $request->token;
        $email = $request->email;

        if (!$token || !$email) {
            return redirect()->route('login')->withErrors(['email' => 'Token de recuperación inválido.']);
        }

        return view('auth.reset-password', compact('token', 'email'));
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        if (!PasswordResetToken::isValidToken($request->email, $request->token)) {
            return back()->withErrors(['email' => 'Token de recuperación inválido o expirado.']);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Usuario no encontrado.']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Mark token as used
        PasswordResetToken::useToken($request->email, $request->token);

        // Log password reset
        LoginHistory::createLoginRecord($user, 'success', 'Password reset');

        // Terminate all sessions for security
        UserSession::where('user_id', $user->id_usuario)->delete();

        return redirect()->route('login')->with('success', 'Contraseña actualizada exitosamente. Todas las sesiones han sido cerradas por seguridad.');
    }

    // 🛡️ Security Dashboard
    public function dashboard()
    {
        $user = Auth::user();
        $user2fa = User2FA::where('user_id', $user->id_usuario)->first();
        $activeSessions = UserSession::getActiveSessions($user->id_usuario)->count();
        $recentLogins = LoginHistory::getRecentAttempts($user->id_usuario, 24);
        
        $securityScore = $this->calculateSecurityScore($user, $user2fa, $activeSessions, $recentLogins);

        return view('security.dashboard', compact('user', 'user2fa', 'activeSessions', 'recentLogins', 'securityScore'));
    }

    private function calculateSecurityScore($user, $user2fa, $activeSessions, $recentLogins)
    {
        $score = 0;
        $recommendations = [];

        // 2FA enabled (+30 points)
        if ($user2fa && $user2fa->is_enabled) {
            $score += 30;
        } else {
            $recommendations[] = 'Habilitar autenticación de dos factores';
        }

        // Strong password (estimate +20 points)
        if (strlen($user->password) >= 60) { // Hashed password length
            $score += 20;
        } else {
            $recommendations[] = 'Usar una contraseña más fuerte';
        }

        // Recent activity (+10 points)
        if ($recentLogins->where('login_status', 'success')->count() > 0) {
            $score += 10;
        }

        // Limited sessions (+10 points)
        if ($activeSessions <= 3) {
            $score += 10;
        } else {
            $recommendations[] = 'Limitar el número de sesiones activas';
        }

        // No failed attempts in last 24h (+10 points)
        if ($recentLogins->where('login_status', 'failed')->count() === 0) {
            $score += 10;
        } else {
            $recommendations[] = 'Revisar intentos de acceso fallidos';
        }

        // Email verified (+10 points - if you have email verification)
        $score += 10;

        // Profile complete (+10 points)
        if ($user->nombre && $user->email) {
            $score += 10;
        }

        return [
            'score' => min($score, 100),
            'level' => $score >= 80 ? 'Excelente' : ($score >= 60 ? 'Bueno' : ($score >= 40 ? 'Regular' : 'Bajo')),
            'color' => $score >= 80 ? 'success' : ($score >= 60 ? 'info' : ($score >= 40 ? 'warning' : 'danger')),
            'recommendations' => $recommendations,
        ];
    }
}
