<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cloud Manager - Cloud Platform</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 h-screen flex flex-col overflow-hidden">
    <div class="flex flex-1 overflow-hidden">
        <!-- Sidebar - Fixed Size and Static -->
        <aside class="w-64 bg-gray-900 text-white flex-shrink-0 flex flex-col h-full">
            <div class="p-6 border-b border-gray-800 flex-shrink-0">
                <div class="flex items-center space-x-3">
                    <div class="h-8 w-8 bg-blue-600 rounded flex items-center justify-center">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path>
                        </svg>
                    </div>
                    <h1 class="text-xl font-semibold">Cloud Manager</h1>
                </div>
            </div>
            <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-2">
                    @php
                        $userRole = session('user_role', 'viewer');
                        $userName = session('user_name', 'User');
                        $userEmail = session('user_email', '');
                    @endphp
                    
                    <!-- Common Menu Items (All Roles) -->
                    <a href="/dashboard" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-md {{ request()->is('dashboard') || request()->is('/') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Paneli kryesor
                    </a>
                    <a href="/resources" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-md {{ request()->is('resources') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Burimet
                    </a>
                    <a href="/metrics" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-md {{ request()->is('metrics') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Monitorimi
                    </a>
                    
                    <!-- Admin Only Menu Items -->
                    @if($userRole === 'admin')
                    <div class="pt-4 border-t border-gray-800 mt-2">
                        <p class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Menaxhimi</p>
                    </div>
                    <a href="/users" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-md {{ request()->is('users*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Përdoruesit
                    </a>
                    <a href="/communication" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-md {{ request()->is('communication') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        Komunikimi
                    </a>
                    <a href="/rbac-test" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-md {{ request()->is('rbac-test') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        RBAC Test
                    </a>
                    @endif
            </nav>
                    
            <!-- User Info at Bottom of Sidebar -->
            <div class="mt-auto border-t border-gray-700 p-4 flex-shrink-0">
                <div class="relative" x-data="{ open: false }">
                    <button 
                        type="button"
                        @click="open = !open"
                        class="w-full flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-700 transition-colors cursor-pointer group"
                    >
                        <div class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-white font-medium text-sm">{{ strtoupper(substr($userName, 0, 1)) }}</span>
                        </div>
                        <div class="flex-1 min-w-0 text-left">
                            <p class="text-sm font-medium text-white truncate">{{ $userName }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ $userEmail }}</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0 transition-transform" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div 
                        x-show="open"
                        @click.away="open = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute bottom-full left-0 right-0 mb-2 bg-gray-700 rounded-lg shadow-xl border border-gray-600 overflow-hidden z-50"
                        style="display: none;"
                    >
                        <a 
                            href="{{ route('profile') }}"
                            class="flex items-center px-4 py-3 hover:bg-gray-600 transition-colors text-white"
                        >
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>Profili</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button 
                                type="submit"
                                class="w-full flex items-center px-4 py-3 hover:bg-gray-600 transition-colors text-red-300 text-left"
                            >
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span>Dil</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content - Scrollable -->
        <main class="flex-1 overflow-y-auto bg-gray-50 h-full flex flex-col">
            <div class="flex-1 p-6">
            {{ $slot }}
            </div>
            
            <!-- Footer - Only in main content area -->
            <footer class="bg-white border-t border-gray-200 py-4 flex-shrink-0 shadow-sm">
                <div class="max-w-7xl mx-auto px-6">
                    <div class="flex flex-col sm:flex-row justify-between items-center">
                        <div class="text-xs text-gray-500 mb-2 sm:mb-0">
                            <span>© 2025 Cloud Manager | v1.0.0</span>
                        </div>
                        <div class="flex items-center space-x-4 text-xs text-gray-500">
                            <span class="flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                                Sistem operativ
                            </span>
                        </div>
                    </div>
                </div>
            </footer>
        </main>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" 
            onload="console.log('Chart.js loaded successfully'); window.chartJsLoaded = true;" 
            onerror="console.error('Failed to load Chart.js');"></script>
    <!-- Alpine.js nuk duhet të ngarkohet këtu sepse Livewire e përfshin tashmë -->
</body>
</html>
