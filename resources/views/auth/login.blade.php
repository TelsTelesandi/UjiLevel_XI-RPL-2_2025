<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Pengajuan Event Ekstrakurikuler</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-8">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900">
                    Event Management System
                </h1>
                <p class="mt-2 text-sm text-gray-600">
                    Sistem Pengajuan Event Ekstrakurikuler
                </p>
            </div>
            
            <div class="rounded-xl bg-white shadow-lg p-8">
                <div class="mb-6 text-center">
                    <h2 class="text-xl font-semibold text-gray-900">
                        Login ke Akun Anda
                    </h2>
                </div>
                
                @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-700">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                
                <form class="space-y-6" action="{{ route('login.post') }}" method="POST">
                    @csrf
                    
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                        <div class="mt-1">
                            <input id="username" name="username" type="text" required 
                                class="block w-full appearance-none rounded-lg border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm"
                                value="{{ old('username') }}">
                        </div>
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <div class="mt-1">
                            <input id="password" name="password" type="password" required 
                                class="block w-full appearance-none rounded-lg border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm">
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember_me" name="remember" type="checkbox" 
                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                                Ingat saya
                            </label>
                        </div>
                    </div>
                    
                    <div>
                        <button type="submit" 
                            class="group relative flex w-full justify-center rounded-lg bg-blue-600 py-2 px-4 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fas fa-lock text-blue-500 group-hover:text-blue-400"></i>
                            </span>
                            Masuk
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Login credentials -->
           
            
            <div class="mt-6 text-center text-sm text-gray-600">
                <p>
                    &copy; 2024 Event Management System. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</body>
</html> 