<div class="space-y-6">
    @if (session('success'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 3000)"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Page Header -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
        <div class="px-6 py-5 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 bg-blue-600 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">Paneli kryesor</h1>
                        <p class="mt-1 text-sm text-gray-500">
                            @php
                                $userRole = session('user_role', 'viewer');
                            @endphp
                            @if($userRole === 'admin')
                                Përmbledhje e plotë e infrastrukturës cloud
                            @elseif($userRole === 'operator')
                                Monitorim dhe menaxhim i resurseve
                            @else
                                Shikim i resurseve dhe metrikave
                            @endif
                        </p>
                    </div>
                </div>
                <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                    @if($liveMetrics)
                        <div class="inline-flex items-center px-3 py-1.5 bg-green-50 border border-green-200 rounded-md">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            <span class="text-xs font-medium text-green-700">Sistem operativ</span>
                        </div>
                    @else
                        <div class="inline-flex items-center px-3 py-1.5 bg-yellow-50 border border-yellow-200 rounded-md">
                            <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                            <span class="text-xs font-medium text-yellow-700">Monitoring i kufizuar</span>
                        </div>
                    @endif
                    <button wire:click="refreshMetrics" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Rifresko
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-500">Total Burime</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['total_resources'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-500">Burime Aktive</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['active_resources'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-500">CPU i Rezervuar</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['total_cpu_capacity'] }}%</p>
                        <p class="mt-1 text-xs text-gray-500">E lirë: {{ $stats['available_cpu'] ?? 0 }}%</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-500">Storage i Rezervuar</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['total_storage_capacity'] }} GB</p>
                        <p class="mt-1 text-xs text-gray-500">E lirë: {{ $stats['available_storage'] ?? 0 }} GB</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resources Overview with Metrics -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Burimet e Fundit</h2>
                    <p class="mt-1 text-sm text-gray-500">Dy burimet më të reja të krijuara</p>
                </div>
                <a href="/resources" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Shiko të gjitha
                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @forelse($resources as $resource)
                    <div class="border border-gray-200 rounded-lg hover:border-gray-300 transition-colors">
                        <div class="p-6">
                            <!-- Header -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-semibold text-gray-900">{{ $resource->name }}</h3>
                                        <p class="text-sm text-gray-500">{{ ucfirst($resource->type) }}</p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $resource->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $resource->status === 'active' ? 'Aktiv' : 'I ndalur' }}
                                </span>
                            </div>
                            
                            @if($resource->region)
                                <div class="mb-4 flex items-center text-sm text-gray-500">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $resource->region }}
                                </div>
                            @endif

                            <!-- Metrics Section -->
                            <div class="space-y-4 pt-4 border-t border-gray-200">
                                <!-- CPU Metrics -->
                                <div>
                                    <div class="flex justify-between items-center mb-1.5">
                                        <span class="text-sm font-medium text-gray-700">CPU</span>
                                        <span class="text-sm font-semibold text-gray-900">
                                            @php
                                                $cpuUsage = $this->calculateResourceUsage($resource, 'cpu');
                                            @endphp
                                            @if($cpuUsage !== null)
                                                {{ $cpuUsage }}% / {{ $resource->cpu_capacity ?? 0 }}%
                                            @else
                                                {{ $resource->cpu_capacity ?? 0 }}%
                                            @endif
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        @php
                                            $cpuPercent = $cpuUsage !== null ? ($cpuUsage / ($resource->cpu_capacity ?? 1)) * 100 : 0;
                                        @endphp
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min($cpuPercent, 100) }}%"></div>
                                    </div>
                                </div>

                                <!-- Memory Metrics -->
                                <div>
                                    <div class="flex justify-between items-center mb-1.5">
                                        <span class="text-sm font-medium text-gray-700">Memoria</span>
                                        <span class="text-sm font-semibold text-gray-900">
                                            @php
                                                $memoryUsage = $this->calculateResourceUsage($resource, 'memory');
                                            @endphp
                                            @if($memoryUsage !== null)
                                                {{ round($memoryUsage, 1) }} MB / {{ $resource->memory_capacity ?? 0 }} MB
                                            @else
                                                {{ $resource->memory_capacity ?? 0 }} MB
                                            @endif
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        @php
                                            $memoryPercent = $memoryUsage !== null && ($resource->memory_capacity ?? 0) > 0 ? ($memoryUsage / ($resource->memory_capacity ?? 1)) * 100 : 0;
                                        @endphp
                                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ min($memoryPercent, 100) }}%"></div>
                                    </div>
                                </div>

                                <!-- Storage Metrics -->
                                <div>
                                    <div class="flex justify-between items-center mb-1.5">
                                        <span class="text-sm font-medium text-gray-700">Storage</span>
                                        <span class="text-sm font-semibold text-gray-900">
                                            @php
                                                $diskUsage = $this->calculateResourceUsage($resource, 'disk');
                                            @endphp
                                            @if($diskUsage !== null)
                                                {{ round($diskUsage, 1) }} GB / {{ $resource->storage_capacity ?? 0 }} GB
                                            @else
                                                {{ $resource->storage_capacity ?? 0 }} GB
                                            @endif
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        @php
                                            $diskPercent = $diskUsage !== null && ($resource->storage_capacity ?? 0) > 0 ? ($diskUsage / ($resource->storage_capacity ?? 1)) * 100 : 0;
                                        @endphp
                                        <div class="bg-purple-600 h-2 rounded-full" style="width: {{ min($diskPercent, 100) }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <h3 class="mt-4 text-sm font-medium text-gray-900">Nuk ka burime</h3>
                        <p class="mt-2 text-sm text-gray-500">Krijoni burime të reja për të filluar.</p>
                        @if($this->isAdmin())
                            <div class="mt-6">
                                <a href="/resources/create" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Krijo Burim të Ri
                                </a>
                            </div>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Veprime të Shpejta</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="/resources" class="relative group bg-white border border-gray-200 rounded-lg p-6 hover:border-gray-300 hover:shadow-md transition-all">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-sm font-semibold text-gray-900">Menaxho Burimet</h3>
                            <p class="mt-1 text-sm text-gray-500">Shiko dhe menaxho të gjitha burimet</p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </a>

                <a href="/metrics" class="relative group bg-white border border-gray-200 rounded-lg p-6 hover:border-gray-300 hover:shadow-md transition-all">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-sm font-semibold text-gray-900">Shiko Metrikat</h3>
                            <p class="mt-1 text-sm text-gray-500">Monitoro performancën e sistemit</p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </a>

                @if($this->isAdmin())
                <a href="/resources/create" class="relative group bg-white border border-gray-200 rounded-lg p-6 hover:border-gray-300 hover:shadow-md transition-all">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-sm font-semibold text-gray-900">Krijo Burim</h3>
                            <p class="mt-1 text-sm text-gray-500">Shto një burim të ri cloud</p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
