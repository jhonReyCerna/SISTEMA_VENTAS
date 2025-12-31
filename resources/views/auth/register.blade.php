@extends('layouts.auth-two-column')

@section('content')
        <div class="rounded-xl p-8 relative" style="backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">
            <div class="absolute inset-0 rounded-xl" style="background: linear-gradient(135deg, rgba(255,255,255,0.04), rgba(255,255,255,0.02)); border: 1px solid rgba(255,255,255,0.06); box-shadow: 0 10px 30px rgba(0,0,0,0.25);"></div>
            <div class="relative bg-white/5 p-6 rounded-xl text-white">
                <div class="flex flex-col items-center mb-6">
                    <x-application-logo class="w-16 h-16 text-gray-800" />
                    <h2 class="mt-4 text-xl font-semibold text-gray-800">Crear cuenta</h2>
                    <p class="text-sm text-gray-500 mt-1">Regístrate para comenzar a gestionar ventas y clientes</p>
                </div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    <!-- Name -->
                    <div class="relative">
                        <label for="nombre" class="block text-sm font-medium text-white/85">Nombre</label>
                        <div class="mt-2 relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon-svg h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path d="M10 4a3 3 0 100 6 3 3 0 000-6z" /><path fill-rule="evenodd" d="M2 14a6 6 0 1116 0H2z" clip-rule="evenodd"/></svg>
                            </span>
                            <input id="nombre" name="nombre" type="text" value="{{ old('nombre') }}" required autofocus class="glass-input block w-full pl-12 bg-white/6 text-slate-900 placeholder-white/60 border border-white/10 rounded-full py-3" />
                        </div>
                        <x-input-error :messages="$errors->get('nombre')" class="mt-2 text-sm text-red-300" />
                    </div>

                    <!-- Email Address -->
                    <div class="mt-4 relative">
                        <label for="email" class="block text-sm font-medium text-white/85">Correo electrónico</label>
                        <div class="mt-2 relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon-svg h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path d="M2.94 6.94A2 2 0 014 6h12a2 2 0 011.06.94L10 11 2.94 6.94z" /><path d="M18 8.5V14a2 2 0 01-2 2H4a2 2 0 01-2-2V8.5l8 4 8-4z" /></svg>
                            </span>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required class="glass-input block w-full pl-12 bg-white/6 text-slate-900 placeholder-white/60 border border-white/10 rounded-full py-3" placeholder="correo@ejemplo.com" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-300" />
                    </div>

                    <!-- Password -->
                    <div class="mt-4 relative">
                        <label for="password" class="block text-sm font-medium text-white/85">Contraseña</label>
                        <div class="mt-2 relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon-svg h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 8a3 3 0 016 0v1h1a2 2 0 012 2v5a2 2 0 01-2 2H4a2 2 0 01-2-2v-5a2 2 0 012-2h1V8zm3-2a1 1 0 00-1 1v1h2V7a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            </span>
                            <input id="password" name="password" type="password" required class="glass-input block w-full pl-12 bg-white/6 text-slate-900 placeholder-white/60 border border-white/10 rounded-full py-3" placeholder="••••••••" />
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-300" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="mt-4 relative">
                        <label for="password_confirmation" class="block text-sm font-medium text-white/85">Confirmar contraseña</label>
                        <div class="mt-2 relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon-svg h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 8a3 3 0 016 0v1h1a2 2 0 012 2v5a2 2 0 01-2 2H4a2 2 0 01-2-2v-5a2 2 0 012-2h1V8zm3-2a1 1 0 00-1 1v1h2V7a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            </span>
                            <input id="password_confirmation" name="password_confirmation" type="password" required class="glass-input block w-full pl-12 bg-white/6 text-slate-900 placeholder-white/60 border border-white/10 rounded-full py-3" placeholder="••••••••" />
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-sm text-red-300" />
                    </div>

                    <!-- Role Selection -->
                    <div class="mt-4 relative">
                        <label for="id_rol" class="block text-sm font-medium text-white/85">Tipo de perfil</label>
                        <div class="mt-2 relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center z-10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon-svg h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/></svg>
                            </span>
                            <select id="id_rol" name="id_rol" required class="glass-input block w-full pl-12 bg-white/6 text-slate-900 border border-white/10 rounded-full py-3 pr-10" style="appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27currentColor%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 1rem center; background-size: 1em;">
                                <option value="">Selecciona un tipo de perfil</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id_rol }}" {{ old('id_rol') == $role->id_rol ? 'selected' : '' }}>
                                        {{ $role->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <x-input-error :messages="$errors->get('id_rol')" class="mt-2 text-sm text-red-300" />
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="w-full rounded-full bg-white text-slate-900 font-semibold py-3 shadow-lg hover:brightness-95 transition">Crear cuenta</button>
                    </div>

                    <div class="flex items-center justify-center mt-3">
                        <div class="or-divider">— o —</div>
                    </div>

                    <div class="mt-3">
                        <a id="loginBtnReg" href="{{ route('login') }}" class="w-full rounded-full text-slate-900 font-semibold py-3 shadow-lg transition inline-block text-center" style="background-color: #d1fae5; color: #064e3b;">Iniciar sesión</a>
                    </div>

                    <script>
                        (function(){
                            const btn = document.getElementById('loginBtnReg');
                            if (!btn) return;
                            const normal = '#d1fae5';
                            const hover = '#bdebd6';
                            btn.addEventListener('mouseenter', () => btn.style.backgroundColor = hover);
                            btn.addEventListener('mouseleave', () => btn.style.backgroundColor = normal);
                        })();
                    </script>
                </form>
            </div>
        </div>
    @endsection
