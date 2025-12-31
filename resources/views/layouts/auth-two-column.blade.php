<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Sistema Ventas') }}</title>

        <!-- Fonts: Inter -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Vite -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            /* small helper to show divider */
            .or-divider { display:flex; align-items:center; gap:12px; color:#9CA3AF }
            .or-divider::before, .or-divider::after { content:''; height:1px; background:rgba(156,163,175,0.3); flex:1 }
            /* glass input placeholder styling: use dark placeholder for visibility when browser autofill overlays */
            .glass-input::placeholder { color: rgba(0,0,0,0.65) !important; opacity: 1 !important; }
            .glass-input:-ms-input-placeholder { color: rgba(0,0,0,0.65) !important; }
            .glass-input::-ms-input-placeholder { color: rgba(0,0,0,0.65) !important; }
            
            /* Evitar que el navegador cambie estilos en autocomplete */
            input:-webkit-autofill,
            input:-webkit-autofill:hover,
            input:-webkit-autofill:focus,
            input:-webkit-autofill:active {
                -webkit-box-shadow: 0 0 0 30px white inset !important;
                -webkit-text-fill-color: #1f2937 !important;
                background-color: white !important;
                color: #1f2937 !important;
            }
            
            /* Asegurar que los inputs mantengan el color */
            input.glass-input {
                color: #1f2937 !important;
            }
            
            input.glass-input:disabled,
            input.glass-input[readonly] {
                background-color: rgba(255,255,255,0.05) !important;
                opacity: 0.6 !important;
            }
        </style>
    </head>
    <body style="font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto;" class="antialiased text-gray-900 bg-gray-50">
        <div class="min-h-screen grid grid-cols-1 md:grid-cols-5">
            <!-- Left visual column (60%) -->
            <div class="hidden md:flex md:col-span-3 items-center justify-center p-12 relative overflow-hidden" style="background: #f5f5f5; color: white;">
                <!-- decorative stars / mountains background -->
                <svg class="absolute inset-0 w-full h-full" viewBox="0 0 800 600" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg" style="opacity:0.12">
                    <defs>
                        <linearGradient id="g1" x1="0" x2="1">
                            <stop offset="0" stop-color="#ffffff" stop-opacity="0.12" />
                            <stop offset="1" stop-color="#000000" stop-opacity="0" />
                        </linearGradient>
                    </defs>
                    <rect width="800" height="600" fill="url(#g1)" />
                </svg>
                <div class="relative z-10 w-full flex items-center justify-center">
                    <img src="{{ asset('storage/img/img1.png') }}" alt="Imagen" loading="eager" style="width: 950px; height: 810px;" class="rounded-md shadow-lg object-contain" />
                </div>
            </div>

            <!-- Right form column (40%) -->
            <div class="flex items-center justify-center md:col-span-2 p-6">
                <main class="w-full max-w-md">
                    @yield('content')
                </main>
            </div>
        </div>
    </body>
    <script>
        // Toggle icon color on input focus for inputs with a left icon
        (function(){
            function activateIconToggles(){
                document.querySelectorAll('label + .mt-1.relative, .mt-1.relative').forEach(function(wrapper){
                    const input = wrapper.querySelector('input');
                    const icon = wrapper.querySelector('.icon-svg');
                    if (!input || !icon) return;
                    function setActive(active){
                        if (active) {
                            icon.classList.remove('text-gray-400');
                            icon.classList.add('text-blue-600');
                        } else {
                            icon.classList.remove('text-blue-600');
                            icon.classList.add('text-gray-400');
                        }
                    }
                    // initial state (if input has value)
                    setActive(!!input.value);
                    input.addEventListener('focus', function(){ setActive(true); });
                    input.addEventListener('blur', function(){ if (!input.value) setActive(false); });
                    // for autofill detection after load
                    setTimeout(function(){ if (input.value) setActive(true); }, 300);
                });
            }
            if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', activateIconToggles); else activateIconToggles();
        })();
    </script>
</html>
