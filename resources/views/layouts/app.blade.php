<!DOCTYPE html>
<html lang="es" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GPT Services - SIGEV')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        [x-cloak] {
            display: none !important;
        }

        body {
            font-family: 'Inter', sans-serif;
        }

        .glass-nav {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(229, 231, 235, 0.5);
        }
    </style>
</head>

<body class="bg-gray-50 font-sans antialiased h-full flex flex-col">

    @auth
        <!-- Navbar -->
        <nav class="glass-nav sticky top-0 z-50 shadow-sm transition-all duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                            
                                <img src="{{ asset('storage/img/logo.png') }}" alt="Logo" style="width: 70px; height: auto;">
                                
                            </a>
                        </div>

                        <!-- Navigation Links - Desktop -->
                        <div class="hidden md:ml-10 md:flex md:space-x-8">
                            <a href="{{ route('dashboard') }}"
                                class="@if (request()->routeIs('dashboard')) border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 @endif inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors duration-200">
                                <i class="fas fa-home mr-2"></i>Dashboard
                            </a>

                            <a href="{{ route('tickets.index') }}"
                                class="@if (request()->routeIs('tickets.*')) border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 @endif inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors duration-200">
                                <i class="fas fa-ticket-alt mr-2"></i>Historial de Requisiciones
                            </a>

                            @if (Auth::user()->hasRole('encargado'))
                                <a href="{{ route('vehicles.index') }}"
                                    class="@if (request()->routeIs('vehicles.*')) border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 @endif inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors duration-200">
                                    <i class="fas fa-car mr-2"></i>Vehículos
                                </a>

                                <a href="{{ route('admin.dashboard') }}"
                                    class="@if (request()->routeIs('admin.*')) border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 @endif inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors duration-200">
                                    <i class="fas fa-chart-pie mr-2"></i>Administración
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Right Side - Desktop -->
                    <div class="hidden md:flex items-center">
                        <div class="ml-3 relative">
                            <div class="flex items-center space-x-4">
                                <div
                                    class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 rounded-full border border-gray-200">
                                    <div
                                        class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                        <i class="fas fa-user text-sm"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</span>
                                </div>
                                <a href="{{ route('login.logout') }}"
                                    class="text-gray-500 hover:text-red-600 transition-colors duration-200 p-2 rounded-full hover:bg-red-50"
                                    title="Cerrar Sesión">
                                    <i class="fas fa-sign-out-alt text-lg"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="flex items-center md:hidden">
                        <button id="mobile-menu-button" type="button"
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 transition-colors">
                            <span class="sr-only">Abrir menú</span>
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div id="mobile-menu"
                class="hidden md:hidden bg-white border-t border-gray-100 shadow-lg absolute w-full left-0 z-50">
                <div class="pt-2 pb-3 space-y-1 px-2">
                    <a href="{{ route('dashboard') }}"
                        class="@if (request()->routeIs('dashboard')) bg-blue-50 text-blue-700 @else text-gray-600 hover:bg-gray-50 hover:text-gray-900 @endif block px-3 py-2 rounded-md text-base font-medium transition-colors">
                        <i class="fas fa-home mr-2 w-6 text-center"></i>Dashboard
                    </a>

                    <a href="{{ route('tickets.index') }}"
                        class="@if (request()->routeIs('tickets.*')) bg-blue-50 text-blue-700 @else text-gray-600 hover:bg-gray-50 hover:text-gray-900 @endif block px-3 py-2 rounded-md text-base font-medium transition-colors">
                        <i class="fas fa-ticket-alt mr-2 w-6 text-center"></i>Requisiciones
                    </a>

                    @if (Auth::user()->hasRole('encargado'))
                        <a href="{{ route('vehicles.index') }}"
                            class="@if (request()->routeIs('vehicles.*')) bg-blue-50 text-blue-700 @else text-gray-600 hover:bg-gray-50 hover:text-gray-900 @endif block px-3 py-2 rounded-md text-base font-medium transition-colors">
                            <i class="fas fa-car mr-2 w-6 text-center"></i>Vehículos
                        </a>

                        <a href="{{ route('admin.dashboard') }}"
                            class="@if (request()->routeIs('admin.*')) bg-blue-50 text-blue-700 @else text-gray-600 hover:bg-gray-50 hover:text-gray-900 @endif block px-3 py-2 rounded-md text-base font-medium transition-colors">
                            <i class="fas fa-chart-pie mr-2 w-6 text-center"></i>Administración
                        </a>
                    @endif
                </div>
                <div class="pt-4 pb-4 border-t border-gray-200 bg-gray-50">
                    <div class="flex items-center px-4">
                        <div class="flex-shrink-0">
                            <div
                                class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-xl">
                                <i class="fas fa-user"></i>
                            </div>
                        </div>
                        <div class="ml-3">
                            <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                            <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
                        </div>
                    </div>
                    <div class="mt-3 px-2 space-y-1">
                        <a href="{{ route('login.logout') }}"
                            class="block px-3 py-2 rounded-md text-base font-medium text-red-600 hover:text-red-800 hover:bg-red-50 transition-colors">
                            <i class="fas fa-sign-out-alt mr-2"></i>Cerrar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    @endauth

    <!-- Page Content -->
    <main class="flex-grow py-8 px-4 sm:px-6 lg:px-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center mb-4 md:mb-0">
                    <div class="bg-gray-100 p-2 rounded-lg mr-3">
                        <i class="fas fa-car-side text-gray-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">GPT Services - SIGEV</p>
                        <p class="text-xs text-gray-500">Sistema Integral de Gestión Vehicular</p>
                    </div>
                </div>
                <div class="text-center md:text-right">
                    <p class="text-sm text-gray-500">
                        &copy; {{ date('Y') }} GPT Services. Todos los derechos reservados.
                    </p>
                    <p class="text-xs text-gray-400 mt-1">Versión 1.0.0</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Mobile Menu Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');

            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                    const icon = mobileMenuButton.querySelector('i');
                    if (mobileMenu.classList.contains('hidden')) {
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-bars');
                    } else {
                        icon.classList.remove('fa-bars');
                        icon.classList.add('fa-times');
                    }
                });
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
