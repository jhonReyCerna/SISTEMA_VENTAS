<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts: Inter (professional sans-serif) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="text-gray-900 antialiased" style="font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;">
        <div class="min-h-screen flex items-center justify-center bg-gray-100">
            <div class="w-full sm:max-w-lg mx-4">
                <div class="flex flex-col items-center mb-8">
                    <a href="/" class="flex items-center gap-3">
                        <x-application-logo class="w-20 h-20 fill-current text-gray-700" />
                    </a>
                    <div class="text-xl font-medium text-gray-800 mt-2">{{ config('app.name', 'Sistema Ventas') }}</div>
                </div>

                <div class="bg-white border border-gray-200 shadow-sm rounded-lg px-8 py-8">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
