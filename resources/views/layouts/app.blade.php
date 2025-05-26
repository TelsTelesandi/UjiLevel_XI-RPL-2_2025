<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'X-Cool Event') }}</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer components {
            .btn-gradient {
                @apply px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg 
                    hover:from-blue-600 hover:to-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 
                    focus:ring-offset-slate-900 focus:outline-none transition-all;
            }
            
            .btn-ghost {
                @apply px-4 py-2 text-gray-300 hover:text-white hover:bg-white/10 rounded-lg 
                    focus:ring-2 focus:ring-white/20 focus:outline-none transition-all;
            }
            
            .btn-lg-gradient {
                @apply inline-flex items-center px-6 py-3 rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 
                    hover:from-blue-600 hover:to-blue-700 text-white font-semibold transition-all;
            }
            
            .btn-outline {
                @apply inline-flex items-center px-6 py-3 rounded-lg border border-white/30;
            }
        }
    </style>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Styles -->
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900">
        @auth
            @include('layouts.sidebar')
            <main class="lg:ml-64 p-4 md:p-8 transition-all duration-300">
                @yield('content')
            </main>
        @else
            @yield('content')
        @endauth
    </div>

    @if (session('success'))
        <div x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 3000)"
             class="fixed top-4 right-4 z-50">
            <div class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 3000)"
             class="fixed top-4 right-4 z-50">
            <div class="bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    {{ session('error') }}
                </div>
            </div>
        </div>
    @endif

    <!-- CSRF Token untuk Ajax Request -->
    <script>
        window.csrfToken = '{{ csrf_token() }}';
    </script>
</body>
</html> 