<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script> --}}
    <!-- Подключение стилей -->

    <!-- Scripts -->
    @vite('resources/css/app.css')

    <style>
        [x-cloak] {
            display: none;
        }
    </style>
</head>

<body class="font-sans antialiased">

    <div class="flex min-h-screen bg-gray-100" x-data="{ open: open }">
        <!-- Sidebar -->
        <x-sidebar />
        <!-- Main Content -->
        <div class="flex-1 transition-transform duration-300" :class="{ open }">

            <div x-data="{ showToast: false }"
                x-on:created.window="showToast = true; setTimeout(() => showToast = false, 3000)">
                <div x-show="showToast" class="fixed top-0 right-0 m-4 p-4 bg-green-500 text-white rounded" x-cloak>
                    Настройки успешно созданы.
                </div>
            </div>
            <div x-data="{ showToast: false }"
                x-on:updated.window="showToast = true; setTimeout(() => showToast = false, 3000)">
                <div x-show="showToast" class="fixed top-0 right-0 m-4 p-4 bg-green-500 text-white rounded" x-cloak>
                    Настройки успешно обновлены.
                </div>
            </div>
            <div x-data="{ showToast: false }"
                x-on:deleted.window="showToast = true; setTimeout(() => showToast = false, 3000)">
                <div x-show="showToast" class="fixed top-0 right-0 m-4 p-4 bg-green-500 text-white rounded" x-cloak>
                    Настройки успешно удалены.
                </div>
            </div>
            <div x-data="{ showToast: false }"
                x-on:erorr.window="showToast = true; setTimeout(() => showToast = false, 3000)">
                <div x-show="showToast" class="fixed top-0 right-0 m-4 p-4 bg-red-500 text-white rounded" x-cloak>
                    Произошла ошибка
                </div>
            </div>

            <!-- Page Content -->
            <main class="p-4">


                @include('layouts.navigation')
                <!-- Page Heading -->
                @if (isset($header))
                    <header class="bg-white shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif
                {{ $slot }}

                <div>

                </div>
            </main>
        </div>
    </div>
    @vite('resources/js/app.js')
    @stack('scripts')
    <!-- Adds the Core Table Styles -->
    @rappasoftTableStyles
    <!-- Adds any relevant Third-Party Styles (Used for DateRangeFilter (Flatpickr) and NumberRangeFilter) -->
    @rappasoftTableThirdPartyStyles
    <!-- Adds the Core Table Scripts -->
    @rappasoftTableScripts
    <!-- Adds any relevant Third-Party Scripts (e.g. Flatpickr) -->
    @rappasoftTableThirdPartyScripts

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>

</body>

</html>
