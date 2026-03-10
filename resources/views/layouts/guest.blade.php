<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Beacon Engineering') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-foreground antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-muted">

        <!-- Logo -->
        <div class="mb-6 ">
            <a href="/" class="flex items-center gap-2">
                <x-application-logo class="w-full h-20 text-primary" />
            </a>
        </div>

        <!-- Card -->
        <div class="w-full sm:max-w-md">
            <div class="card px-8 py-8">
                {{ $slot }}
            </div>
        </div>

        <p class="mt-6 text-xs text-muted-foreground">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </p>
    </div>
</body>

</html>