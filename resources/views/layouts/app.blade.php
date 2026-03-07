<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-background text-foreground">
    <div class="min-h-screen flex flex-col">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-card border-b border-border">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main class="flex-1">
            {{ $slot }}
        </main>
    </div>
    @stack('scripts')

    {{-- Global Flatpickr Init --}}
    <script>
        function initFlatpickr(root) {
            var targets = (root || document).querySelectorAll('[data-datepicker]');
            targets.forEach(function (el) {
                if (el._flatpickr) return; // already initialized
                var opts = {
                    locale: 'id',
                    dateFormat: 'Y-m-d',
                    allowInput: true,
                    disableMobile: true,
                };
                // range mode when both data-datepicker-range-start / end present
                if (el.dataset.datepickerMode === 'range') {
                    opts.mode = 'range';
                }
                flatpickr(el, opts);
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            initFlatpickr();
        });
    </script>
</body>

</html>