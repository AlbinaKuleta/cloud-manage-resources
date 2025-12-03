<div>
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Cloud Manager</h1>
                <p class="text-gray-600">Platformë për menaxhimin e burimeve cloud përmes mikrosherbimeve.</p>
            </div>
            <div class="mt-4 lg:mt-0 flex items-center space-x-4">
                @if($liveMetrics)
                    <div class="bg-green-50 border border-green-200 rounded-lg px-4 py-2">
                        <div class="flex items-center space-x-2">
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                            <span class="text-xs text-green-700">Metrikat në kohë reale aktive</span>
                        </div>
                    </div>
                @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg px-4 py-2">
                        <div class="flex items-center space-x-2">
                            <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                            <span class="text-xs text-yellow-700">Monitoring-service nuk është i disponueshëm</span>
                        </div>
                    </div>
                @endif
                <button wire:click="refreshMetrics" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Rifresko
                </button>
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-900">{{ session('user_name', 'User') }}</p>
                </div>
                <a href="/login" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Dil
                </a>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0 lg:space-x-4">
                <!-- Search Bar -->
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" 
                               wire:model.live.debounce.300ms="search"
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 focus:bg-white transition-colors"
                               placeholder="Kërko burime...">
                    </div>
                </div>

                <!-- Filter Buttons -->
                <div class="flex items-center space-x-2 overflow-x-auto">
                    <button wire:click="filterByType('all')" 
                            class="px-4 py-2 text-sm font-medium rounded-full whitespace-nowrap transition-colors {{ $selectedFilter === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Të gjitha
                    </button>
                    <button wire:click="filterByType('Instance')" 
                            class="px-4 py-2 text-sm font-medium rounded-full whitespace-nowrap transition-colors {{ $selectedFilter === 'Instance' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Instance
                    </button>
                    <button wire:click="filterByType('Storage')" 
                            class="px-4 py-2 text-sm font-medium rounded-full whitespace-nowrap transition-colors {{ $selectedFilter === 'Storage' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Storage
                    </button>
                    <button wire:click="filterByType('Database')" 
                            class="px-4 py-2 text-sm font-medium rounded-full whitespace-nowrap transition-colors {{ $selectedFilter === 'Database' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Database
                    </button>
                    <button wire:click="filterByType('API')" 
                            class="px-4 py-2 text-sm font-medium rounded-full whitespace-nowrap transition-colors {{ $selectedFilter === 'API' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        API
                    </button>
                    <button wire:click="filterByType('Container')" 
                            class="px-4 py-2 text-sm font-medium rounded-full whitespace-nowrap transition-colors {{ $selectedFilter === 'Container' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Container
                    </button>
                </div>

                <!-- Pagination Dropdown -->
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700 whitespace-nowrap">Shfaq:</label>
                    <select wire:model="perPage" 
                            class="px-6 py-2 text-sm font-medium border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white shadow-sm hover:border-gray-400 transition-colors">
                        <option value="10">10 për faqe</option>
                        <option value="25">25 për faqe</option>
                        <option value="50">50 për faqe</option>
                        <option value="100">100 për faqe</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Add Resource Button (Admin only) -->
        @if($this->isAdmin())
        <div class="mt-4 flex justify-end">
            <button wire:click="openCreateModal"
                    class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Shto Burim
            </button>
        </div>
        @endif
    </div>

    <!-- Flash Messages -->
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

    @if (session('error'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 3000)"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Section Title -->
    <div class="mb-4">
        <h2 class="text-xl font-bold text-gray-900">Menaxhimi i Burimeve</h2>
        <p class="text-sm text-gray-600 mt-1">Krijoni, modifikoni dhe menaxhoni burimet cloud.</p>
    </div>

    <!-- Resources Table -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b border-gray-200">
                            EMRI
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b border-gray-200">
                            TIPI
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b border-gray-200">
                            STATUSI
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b border-gray-200">
                            CPU
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b border-gray-200">
                            MEMORIA
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b border-gray-200">
                            STORAGE
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b border-gray-200">
                            RAJONI
                        </th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider border-b border-gray-200">
                            VEPRIMET
                        </th>
            </tr>
        </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($filteredResources as $resource)
                        <tr class="hover:bg-blue-50 transition-colors duration-150 border-b border-gray-100">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">{{ $resource->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $resource->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($resource->status === 'active')
                                    <span class="inline-flex items-center text-sm font-medium text-green-700">
                                        <span class="w-2.5 h-2.5 mr-2 rounded-full bg-green-500 animate-pulse"></span>
                                        Running
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-sm font-medium text-gray-600">
                                        <span class="w-2.5 h-2.5 mr-2 rounded-full bg-gray-400"></span>
                                        Stopped
                        </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm">
                                    <div class="font-semibold text-gray-900">
                                        {{ $resource->cpu_capacity ?? 0 }}%
                                    </div>
                                    @php
                                        $cpuUsage = $this->calculateResourceUsage($resource, 'cpu');
                                    @endphp
                                    @if($cpuUsage !== null)
                                        <div class="text-xs font-medium {{ $cpuUsage > ($resource->cpu_capacity ?? 0) * 0.8 ? 'text-red-600' : 'text-gray-500' }}">
                                            Përdorim: {{ $cpuUsage }}%
                                        </div>
                                    @elseif($liveMetrics)
                                        <div class="text-xs text-gray-400">Sistemi: {{ $liveMetrics['cpu_usage'] ?? 0 }}%</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm">
                                    <div class="font-semibold text-gray-900">
                                        {{ $resource->memory_capacity ?? 0 }} MB
                                    </div>
                                    @php
                                        $memoryUsage = $this->calculateResourceUsage($resource, 'memory');
                                    @endphp
                                    @if($memoryUsage !== null)
                                        <div class="text-xs font-medium {{ $memoryUsage > ($resource->memory_capacity ?? 0) * 0.8 ? 'text-red-600' : 'text-gray-500' }}">
                                            Përdorim: {{ round($memoryUsage, 1) }} MB
                                        </div>
                                    @elseif($liveMetrics)
                                        <div class="text-xs text-gray-400">Sistemi: {{ round($liveMetrics['memory_usage'] ?? 0, 1) }}%</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm">
                                    <div class="font-semibold text-gray-900">
                                        {{ $resource->storage_capacity ?? 0 }} GB
                                    </div>
                                    @php
                                        $diskUsage = $this->calculateResourceUsage($resource, 'disk');
                                    @endphp
                                    @if($diskUsage !== null)
                                        <div class="text-xs font-medium {{ $diskUsage > ($resource->storage_capacity ?? 0) * 0.8 ? 'text-red-600' : 'text-gray-500' }}">
                                            Përdorim: {{ round($diskUsage, 1) }} GB
                                        </div>
                                    @elseif($liveMetrics)
                                        <div class="text-xs text-gray-400">Sistemi: {{ round($liveMetrics['disk_usage'] ?? 0, 1) }}%</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-700">{{ $resource->region ?? 'eu-west' }}</span>
                    </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-1">
                                    <button wire:click="stopResource({{ $resource->id }})"
                                            class="inline-flex items-center justify-center p-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors"
                                            title="Ndal">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="restartResource({{ $resource->id }})"
                                            class="inline-flex items-center justify-center p-2 border border-purple-300 rounded-md text-purple-700 bg-purple-50 hover:bg-purple-100 transition-colors"
                                            title="Restart">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="openDetailsModal({{ $resource->id }})"
                                            class="inline-flex items-center justify-center p-2 border border-blue-300 rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors"
                                            title="Detaje">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </button>
                                    @if($this->canEdit())
                                    <button wire:click="openEditModal({{ $resource->id }})"
                                            class="inline-flex items-center justify-center p-2 border border-blue-300 rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors"
                                            title="Përditëso">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="openDeleteModal({{ $resource->id }})"
                                            class="inline-flex items-center justify-center p-2 border border-red-300 rounded-md text-red-700 bg-red-50 hover:bg-red-100 transition-colors"
                                            title="Fshi">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Nuk ka burime</h3>
                                <p class="mt-1 text-sm text-gray-500">Filloni duke krijuar një burim të ri.</p>
                    </td>
                </tr>
                    @endforelse
        </tbody>
    </table>
        </div>
    </div>

    <!-- Create Modal -->
    @if($showCreateModal)
    <div wire:key="create-modal" 
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" 
         style="z-index: 9999;"
         onclick="if(event.target === this) @this.closeCreateModal()">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full">
            <div class="p-5 border-b border-gray-200">
                <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-900">Shto Burim të Ri</h2>
                    <button wire:click="closeCreateModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <form wire:submit.prevent="create" class="p-5">
                <!-- Kapaciteti i lirë -->
                @php
                    $available = $this->getAvailableCapacity();
                @endphp
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-5">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Kapaciteti i lirë në sistem:</h3>
                    <div class="flex gap-4 text-sm">
                        <div><span class="text-gray-600">CPU:</span> <span class="font-semibold text-gray-900">{{ $available['cpu'] }}%</span></div>
                        <div><span class="text-gray-600">Memory:</span> <span class="font-semibold text-gray-900">{{ $available['memory'] }} MB</span></div>
                        <div><span class="text-gray-600">Storage:</span> <span class="font-semibold text-gray-900">{{ $available['storage'] }} GB</span></div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Emri</label>
                    <input type="text" wire:model="name"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Shkruani emrin">
                    @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipi</label>
                        <select wire:model="type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                        <option value="">Zgjedh...</option>
                        <option value="Instance">Instance</option>
                        <option value="Storage">Storage</option>
                        <option value="Database">Database</option>
                        <option value="API">API</option>
                        <option value="Container">Container</option>
                    </select>
                    @error('type') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">CPU të Rezervuar (%)</label>
                    <input type="number" wire:model="cpu_capacity"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0"
                           min="0" max="100">
                    @error('cpu_capacity') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Memoria e Rezervuar (MB)</label>
                    <input type="number" wire:model="memory_capacity"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0"
                           min="0">
                    @error('memory_capacity') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Storage i Rezervuar (GB)</label>
                    <input type="number" wire:model="storage_capacity"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0"
                           min="0">
                    @error('storage_capacity') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rajoni</label>
                    <input type="text" wire:model="region"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="eu-west, us-east, etj.">
                    @error('region') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex gap-3 pt-5 mt-5 border-t border-gray-200">
                    <button type="button" wire:click="closeCreateModal" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium px-4 py-2 rounded-md transition-colors">
                        Anulo
                    </button>
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-md transition-colors">
                        Krijo
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Edit Modal -->
    @if($showEditModal)
    <div wire:key="edit-modal" 
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" 
         style="z-index: 9999;"
         onclick="if(event.target === this) @this.closeEditModal()">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-900">Përditëso Burim</h2>
                    <button wire:click="closeEditModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <form wire:submit.prevent="update" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Emri</label>
                    <input type="text" wire:model="name"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipi</label>
                    <select wire:model="type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="Instance">Instance</option>
                        <option value="Storage">Storage</option>
                        <option value="Database">Database</option>
                        <option value="API">API</option>
                        <option value="Container">Container</option>
                    </select>
                    @error('type') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">CPU Kapaciteti (%)</label>
                    <input type="number" wire:model="cpu_capacity"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           min="0" max="100">
                    @error('cpu_capacity') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Memoria Kapaciteti (MB)</label>
                    <input type="number" wire:model="memory_capacity"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           min="0">
                    @error('memory_capacity') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Storage Kapaciteti (GB)</label>
                    <input type="number" wire:model="storage_capacity"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           min="0">
                    @error('storage_capacity') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rajoni</label>
                    <input type="text" wire:model="region"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="eu-west, us-east, etj.">
                    @error('region') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statusi</label>
                    <select wire:model="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="active">Running</option>
                        <option value="inactive">Stopped</option>
                    </select>
                    @error('status') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-3 rounded-lg shadow-sm transition-all duration-200">
                        Ruaj
                    </button>
                    <button type="button" wire:click="closeEditModal" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold px-4 py-3 rounded-lg transition-colors duration-200">
                        Anulo
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Details Modal -->
    @if($showDetailsModal && $selectedResource)
    <div wire:key="details-modal" 
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" 
         style="z-index: 9999;"
         onclick="if(event.target === this) @this.closeDetailsModal()">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full transform transition-all">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-900">Detaje të Burimit</h2>
                    <button wire:click="closeDetailsModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="text-sm font-semibold text-gray-500">Emri</label>
                        <p class="mt-1 text-lg font-medium text-gray-900">{{ $selectedResource->name }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-500">Tipi</label>
                        <p class="mt-1 text-lg font-medium text-gray-900">{{ $selectedResource->type }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-500">Statusi</label>
                        <p class="mt-1">
                            <span class="inline-flex items-center text-sm font-medium text-green-600">
                                <span class="w-2 h-2 mr-2 rounded-full bg-green-500"></span>
                                Running
                            </span>
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-500">Kapaciteti</label>
                        <p class="mt-1 text-lg font-medium text-gray-900">{{ $selectedResource->capacity ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-500">CPU Kapaciteti</label>
                        <p class="mt-1 text-lg font-medium text-gray-900">{{ $selectedResource->cpu_capacity ?? 0 }}%</p>
                        @php
                            $cpuUsage = $this->calculateResourceUsage($selectedResource, 'cpu');
                        @endphp
                        @if($cpuUsage !== null)
                            <p class="text-xs {{ $cpuUsage > ($selectedResource->cpu_capacity ?? 0) * 0.8 ? 'text-red-600 font-semibold' : 'text-gray-500' }} mt-1">
                                Përdorim aktual: {{ $cpuUsage }}%
                            </p>
                        @elseif($liveMetrics)
                            <p class="text-xs text-gray-400 mt-1">Metrika globale: {{ $liveMetrics['cpu_usage'] ?? 0 }}%</p>
                        @endif
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-500">Memoria Kapaciteti</label>
                        <p class="mt-1 text-lg font-medium text-gray-900">{{ $selectedResource->memory_capacity ?? 0 }} MB</p>
                        @php
                            $memoryUsage = $this->calculateResourceUsage($selectedResource, 'memory');
                        @endphp
                        @if($memoryUsage !== null)
                            <p class="text-xs {{ $memoryUsage > ($selectedResource->memory_capacity ?? 0) * 0.8 ? 'text-red-600 font-semibold' : 'text-gray-500' }} mt-1">
                                Përdorim aktual: {{ round($memoryUsage, 1) }} MB
                            </p>
                        @elseif($liveMetrics)
                            <p class="text-xs text-gray-400 mt-1">Metrika globale: {{ round($liveMetrics['memory_usage'] ?? 0, 1) }}%</p>
                        @endif
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-500">Storage Kapaciteti</label>
                        <p class="mt-1 text-lg font-medium text-gray-900">{{ $selectedResource->storage_capacity ?? 0 }} GB</p>
                        @php
                            $diskUsage = $this->calculateResourceUsage($selectedResource, 'disk');
                        @endphp
                        @if($diskUsage !== null)
                            <p class="text-xs {{ $diskUsage > ($selectedResource->storage_capacity ?? 0) * 0.8 ? 'text-red-600 font-semibold' : 'text-gray-500' }} mt-1">
                                Përdorim aktual: {{ round($diskUsage, 1) }} GB
                            </p>
                        @elseif($liveMetrics)
                            <p class="text-xs text-gray-400 mt-1">Metrika globale: {{ round($liveMetrics['disk_usage'] ?? 0, 1) }}%</p>
                        @endif
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-500">Rajoni</label>
                        <p class="mt-1 text-lg font-medium text-gray-900">{{ $selectedResource->region ?? 'eu-west' }}</p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button wire:click="closeDetailsModal" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold rounded-lg transition-colors">
                        Mbyll
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
    <div wire:key="delete-modal" 
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" 
         style="z-index: 9999;"
         onclick="if(event.target === this) @this.closeDeleteModal()">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-900">Fshi Burim</h2>
                    <button wire:click="closeDeleteModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="text-center mb-6">
                    <p class="text-gray-700 mb-2 font-medium">Jeni të sigurt që dëshironi të fshini këtë burim?</p>
                    <p class="font-semibold text-lg text-gray-900">{{ $deleteResourceName }}</p>
                    <p class="text-red-600 text-sm mt-2">Ky veprim nuk mund të zhbëhet.</p>
                </div>

                <div class="flex gap-3">
                    <button wire:click="confirmDelete" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-3 rounded-lg shadow-sm transition-all duration-200">
                        Fshi
                    </button>
                    <button wire:click="closeDeleteModal" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold px-4 py-3 rounded-lg transition-colors duration-200">
                        Anulo
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
