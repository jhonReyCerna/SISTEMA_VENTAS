@extends('layouts.auth-two-column')

@section('content')
    <div class="rounded-xl p-8 relative" style="backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">
        <div class="absolute inset-0 rounded-xl" style="background: linear-gradient(135deg, rgba(255,255,255,0.04), rgba(255,255,255,0.02)); border: 1px solid rgba(255,255,255,0.06); box-shadow: 0 10px 30px rgba(0,0,0,0.25);"></div>
        <div class="relative bg-white/5 p-6 rounded-xl text-white">
        <div class="flex flex-col items-center mb-6">
            <x-application-logo class="w-16 h-16 text-gray-800" />
            <h2 class="mt-4 text-xl font-semibold text-gray-800">Iniciar sesión</h2>
            <p class="text-sm text-gray-500 mt-1">Acceso al panel administrativo</p>
        </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-4" id="loginForm">
            @csrf

            <!-- Email with icon -->
            <div class="relative">
                <label for="email" class="block text-sm font-medium text-white/85">Correo electrónico</label>
                <div class="mt-2 relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon-svg h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path d="M2.94 6.94A2 2 0 014 6h12a2 2 0 011.06.94L10 11 2.94 6.94z" /><path d="M18 8.5V14a2 2 0 01-2 2H4a2 2 0 01-2-2V8.5l8 4 8-4z" /></svg>
                    </span>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="glass-input block w-full pl-12 bg-white/6 text-white placeholder-white/60 border border-white/10 rounded-full py-3" placeholder="correo@ejemplo.com" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-300" />
            </div>

            <!-- Password with show/hide -->
            <div class="relative">
                <label for="password" class="block text-sm font-medium text-white/85">Contraseña</label>
                <div class="mt-2 relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center">
                        <!-- lock icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon-svg h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 8a3 3 0 016 0v1h1a2 2 0 012 2v5a2 2 0 01-2 2H4a2 2 0 01-2-2v-5a2 2 0 012-2h1V8zm3-2a1 1 0 00-1 1v1h2V7a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    </span>
                    <input id="password" name="password" type="password" required class="glass-input block w-full pl-12 pr-12 bg-white/6 text-slate-900 placeholder-white/60 border border-white/10 rounded-full py-3" placeholder="••••••••" />
                    <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-white/75">
                        <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M2.94 6.94A10.02 10.02 0 0110 4c4.97 0 9.14 3 10 6a10.02 10.02 0 01-7.06 4.06A9.96 9.96 0 0110 16a9.96 9.96 0 01-7.06-4.06 10.02 10.02 0 00.0-4.999z"/></svg>
                        <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3.707 3.293a1 1 0 010 1.414L2.414 6H3v8a2 2 0 002 2h8a2 2 0 002-2v-.586l1.293 1.293a1 1 0 001.414-1.414l-14-14a1 1 0 00-1.414 0z" clip-rule="evenodd"/></svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-300" />
            </div>

            <div class="flex items-center justify-between">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="remember" class="h-4 w-4 text-indigo-500 rounded border-white/20 bg-white/6">
                    <span class="ml-2 text-sm text-slate-900">Recordarme</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm text-slate-900 underline decoration-slate-300" href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
                @endif
            </div>

            <div>
                <button type="submit" class="w-full rounded-full bg-white text-slate-900 font-semibold py-3 shadow-lg hover:brightness-95 transition">Iniciar sesión</button>
            </div>

            <div class="flex items-center justify-center mt-3">
                <div class="or-divider">— o —</div>
            </div>

            <div class="mt-3">
                <a id="registerBtn" href="{{ route('register') }}" class="w-full rounded-full text-slate-900 font-semibold py-3 shadow-lg transition inline-block text-center" style="background-color: #d1fae5 !important; color: #064e3b !important;">Crear cuenta</a>
            </div>
        </form>
        </div>
    </div>

    <script>
        (function(){
            const toggle = document.getElementById('togglePassword');
            if (!toggle) return;
            const pwd = document.getElementById('password');
            const eyeOpen = document.getElementById('eyeOpen');
            const eyeClosed = document.getElementById('eyeClosed');
            toggle.addEventListener('click', function(){
                if (pwd.type === 'password'){
                    pwd.type = 'text';
                    eyeOpen.classList.add('hidden');
                    eyeClosed.classList.remove('hidden');
                } else {
                    pwd.type = 'password';
                    eyeOpen.classList.remove('hidden');
                    eyeClosed.classList.add('hidden');
                }
            });
        })();
    </script>
    <script>
        (function(){
            const btn = document.getElementById('registerBtn');
            if (!btn) return;
            const normal = '#d1fae5';
            const hover = '#bdebd6';
            btn.addEventListener('mouseenter', () => btn.style.backgroundColor = hover);
            btn.addEventListener('mouseleave', () => btn.style.backgroundColor = normal);
        })();
    </script>
@endsection
