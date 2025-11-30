<div>
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center space-x-3">
                    <h1 class="text-3xl font-bold text-gray-900">Paneli Kryesor</h1>
                    @php
                        $userRole = session('user_role', 'viewer');
                    @endphp
                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border {{ 
                        $userRole === 'admin' ? 'bg-red-100 text-red-800 border-red-200' : 
                        ($userRole === 'operator' ? 'bg-blue-100 text-blue-800 border-blue-200' : 
                        'bg-gray-100 text-gray-800 border-gray-200') 
                    }}">
                        {{ strtoupper($userRole) }}
                    </span>
                </div>
                <p class="mt-2 text-sm text-gray-600">
                    @if($userRole === 'admin')
                        Përmbledhje e plotë e burimeve dhe metrikave të sistemit
                    @elseif($userRole === 'operator')
                        Përmbledhje e burimeve dhe metrikave (shikim dhe menaxhim)
                    @else
                        Përmbledhje e burimeve dhe metrikave (vetëm shikim)
                    @endif
                </p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                @if($liveMetrics)
                    <div class="bg-green-50 border border-green-200 rounded-lg px-4 py-2">
                        <div class="flex items-center space-x-2">
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                            <span class="text-xs text-green-700">Metrikat aktive</span>
                        </div>
                    </div>
                @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg px-4 py-2">
                        <div class="flex items-center space-x-2">
                            <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                            <span class="text-xs text-yellow-700">Monitoring-service jo i disponueshëm</span>
                        </div>
                    </div>
                @endif
                <button wire:click="refreshMetrics" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Rifresko
                </button>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 3000)"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm">
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

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Burime</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_resources'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Burime Aktive</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['active_resources'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">CPU i Rezervuar</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_cpu_capacity'] }}%</p>
                    <p class="text-xs text-gray-500 mt-1">E lirë: {{ $stats['available_cpu'] ?? 0 }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Storage i Rezervuar</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_storage_capacity'] }} GB</p>
                    <p class="text-xs text-gray-500 mt-1">E lirë: {{ $stats['available_storage'] ?? 0 }} GB</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Resources Overview with Metrics -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-900">Përmbledhje e Burimeve</h2>
            <a href="/resources" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                Shiko të gjitha →
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @forelse($resources->take(6) as $resource)
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $resource->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $resource->type }}</p>
                        </div>
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $resource->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ $resource->status === 'active' ? 'Running' : 'Stopped' }}
                        </span>
                    </div>

                    <!-- CPU Metrics -->
                    <div class="mb-3">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">CPU</span>
                            <span class="font-medium text-gray-900">
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
                            <div class="bg-blue-600 h-2 rounded-full transition-all" style="width: {{ min($cpuPercent, 100) }}%"></div>
                        </div>
                    </div>

                    <!-- Memory Metrics -->
                    <div class="mb-3">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">Memoria</span>
                            <span class="font-medium text-gray-900">
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
                            <div class="bg-green-600 h-2 rounded-full transition-all" style="width: {{ min($memoryPercent, 100) }}%"></div>
                        </div>
                    </div>

                    <!-- Storage Metrics -->
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">Storage</span>
                            <span class="font-medium text-gray-900">
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
                            <div class="bg-purple-600 h-2 rounded-full transition-all" style="width: {{ min($diskPercent, 100) }}%"></div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-2 text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Nuk ka burime</h3>
                    <p class="mt-1 text-sm text-gray-500">Krijoni burime të reja për të filluar.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Veprime të Shpejta</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="/resources" 
               class="group relative overflow-hidden rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 p-6 text-white hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-2">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <svg class="h-5 w-5 opacity-50 group-hover:opacity-100 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold mb-1">Menaxho Burimet</h3>
                    <p class="text-sm opacity-90">Shiko dhe menaxho të gjitha burimet cloud</p>
                </div>
            </a>

            <a href="/metrics" 
               class="group relative overflow-hidden rounded-lg bg-gradient-to-r from-green-600 to-emerald-600 p-6 text-white hover:from-green-700 hover:to-emerald-700 transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-2">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <svg class="h-5 w-5 opacity-50 group-hover:opacity-100 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold mb-1">Shiko Metrikat</h3>
                    <p class="text-sm opacity-90">Monitoro performancën dhe metrikat e sistemit</p>
                </div>
            </a>

            @if($this->isAdmin())
            <a href="/resources/create" 
               class="group relative overflow-hidden rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 p-6 text-white hover:from-purple-700 hover:to-pink-700 transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-2">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <svg class="h-5 w-5 opacity-50 group-hover:opacity-100 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold mb-1">Krijo Burim</h3>
                    <p class="text-sm opacity-90">Shto një burim të ri cloud</p>
                </div>
            </a>
            @endif
        </div>
    </div>
</div>
