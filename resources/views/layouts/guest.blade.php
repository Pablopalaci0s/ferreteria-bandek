<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('img/favicon-32.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col justify-center items-center px-4 py-10 bg-gray-100">

            <a href="{{ route('inicio') }}" class="flex items-center mb-8" style="gap: 0.6rem;">
                <img src="{{ asset('img/logo-icono.png') }}" alt="Ferretería BANDEK" style="width:44px; height:44px; object-fit:contain; flex-shrink:0;">
                <span class="text-2xl font-bold text-red-800 tracking-tight">BANDEK</span>
            </a>

            <div class="w-full sm:max-w-md bg-white border border-gray-200 shadow-sm rounded-lg overflow-hidden">
                <div class="px-6 sm:px-8 py-3 border-b bg-gray-50">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Panel administrativo</p>
                </div>
                <div class="px-6 sm:px-8 py-6">
                    {{ $slot }}
                </div>
            </div>

            <a href="{{ route('inicio') }}" class="mt-6 text-sm text-gray-500 hover:text-red-800 transition">
                &larr; Volver a la tienda
            </a>

        </div>
    </body>
</html>
