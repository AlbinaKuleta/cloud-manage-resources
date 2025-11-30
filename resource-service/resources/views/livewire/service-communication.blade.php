<div>
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Komunikimi i Mikrosherbimeve</h1>
                <p class="mt-2 text-sm text-gray-600">Demonstrim i komunikimit midis resource-service dhe monitoring-service</p>
            </div>
            <button wire:click="refresh" class="mt-4 sm:mt-0 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Rifresko
            </button>
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

    <!-- Service Status -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Statusi i Komunikimit</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-semibold text-gray-900">Monitoring-Service</h3>
                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ !empty($status) && $status['available'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ !empty($status) && $status['available'] ? 'Aktiv' : 'Jo i disponueshëm' }}
                    </span>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">URL:</span>
                        <span class="font-mono text-gray-900">{{ $status['url'] ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status:</span>
                        <span class="font-medium {{ !empty($status) && $status['available'] ? 'text-green-600' : 'text-red-600' }}">
                            {{ !empty($status) && $status['available'] ? 'Lidhur' : 'Nuk është i lidhur' }}
                        </span>
                    </div>
                    @if(!empty($status['last_metrics']))
                    <div class="flex justify-between">
                        <span class="text-gray-600">CPU:</span>
                        <span class="font-medium">{{ $status['last_metrics']['cpu_usage'] ?? 0 }}%</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Memory:</span>
                        <span class="font-medium">{{ round($status['last_metrics']['memory_usage'] ?? 0, 1) }}%</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Disk:</span>
                        <span class="font-medium">{{ round($status['last_metrics']['disk_usage'] ?? 0, 1) }}%</span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Komunikimi</h3>
                <div class="space-y-3">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">HTTP REST API</p>
                            <p class="text-xs text-gray-500">Komunikim sinkron përmes HTTP requests</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Service Layer</p>
                            <p class="text-xs text-gray-500">MonitoringService klasë për centralizimin e komunikimit</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Cache & Logging</p>
                            <p class="text-xs text-gray-500">Cache për 5 sekonda dhe logging i të gjitha komunikimeve</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Communication Flow -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Rrjedha e Komunikimit</h2>
        
        <div class="space-y-6">
            <!-- Step 1 -->
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">1</div>
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-900 mb-2">Krijimi i Resursit</h3>
                    <p class="text-sm text-gray-600 mb-2">Përdoruesi specifikon kapacitetin e resursit (CPU, Memory, Storage)</p>
                    <div class="bg-gray-50 rounded-lg p-3 font-mono text-xs">
                        Resource-Service → Kontrollon kapacitetin e lirë lokal
                    </div>
                </div>
            </div>

            <!-- Arrow -->
            <div class="flex justify-center">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                </svg>
            </div>

            <!-- Step 2 -->
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center text-white font-bold">2</div>
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-900 mb-2">Komunikim me Monitoring-Service</h3>
                    <p class="text-sm text-gray-600 mb-2">Resource-Service dërgon HTTP GET request në Monitoring-Service</p>
                    <div class="bg-gray-50 rounded-lg p-3 font-mono text-xs">
                        GET http://127.0.0.1:8002/api/metrics
                    </div>
                </div>
            </div>

            <!-- Arrow -->
            <div class="flex justify-center">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                </svg>
            </div>

            <!-- Step 3 -->
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center text-white font-bold">3</div>
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-900 mb-2">Verifikim i Kapacitetit</h3>
                    <p class="text-sm text-gray-600 mb-2">Monitoring-Service kthen metrikat globale (CPU, Memory, Disk usage)</p>
                    <div class="bg-gray-50 rounded-lg p-3 font-mono text-xs">
                        {<br>
                        &nbsp;&nbsp;"cpu_usage": 45,<br>
                        &nbsp;&nbsp;"memory_usage": 34,<br>
                        &nbsp;&nbsp;"disk_usage": 93.07<br>
                        }
                    </div>
                </div>
            </div>

            <!-- Arrow -->
            <div class="flex justify-center">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                </svg>
            </div>

            <!-- Step 4 -->
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-orange-600 rounded-full flex items-center justify-center text-white font-bold">4</div>
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-900 mb-2">Vendim për Krijim</h3>
                    <p class="text-sm text-gray-600 mb-2">Resource-Service vendos nëse mund të krijojë resursin bazuar në metrikat</p>
                    <div class="bg-gray-50 rounded-lg p-3 font-mono text-xs">
                        Nëse CPU &lt; 85% dhe Memory &lt; 90% dhe Disk &lt; 95%<br>
                        → Krijo resursin<br>
                        Përndryshe → Refuzo krijimin
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- API Endpoints -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">API Endpoints për Komunikim</h2>
        
        <div class="space-y-4">
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center space-x-3">
                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded">GET</span>
                        <code class="text-sm font-mono text-gray-900">/api/monitoring/status</code>
                    </div>
                </div>
                <p class="text-sm text-gray-600">Merr statusin e komunikimit me monitoring-service</p>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center space-x-3">
                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded">GET</span>
                        <code class="text-sm font-mono text-gray-900">/api/monitoring/metrics</code>
                    </div>
                </div>
                <p class="text-sm text-gray-600">Merr metrikat e sistemit nga monitoring-service</p>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center space-x-3">
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded">POST</span>
                        <code class="text-sm font-mono text-gray-900">/api/monitoring/check-capacity</code>
                    </div>
                </div>
                <p class="text-sm text-gray-600">Verifikon nëse ka kapacitet për krijimin e resursit</p>
                <div class="mt-2 bg-gray-50 rounded p-2">
                    <p class="text-xs font-mono text-gray-600">Body: { "cpu": 20, "memory": 1000, "storage": 50 }</p>
                </div>
            </div>
        </div>
    </div>
</div>

