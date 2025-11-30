<div wire:poll.2s="fetchLiveMetrics">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Monitorimi i Metrikave</h1>
                <p class="mt-2 text-sm text-gray-600">Rezervimi vs përdorimi aktual në kohë reale</p>
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
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm">
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

    <!-- Global System Metrics -->
    @if($liveMetrics)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Metrikat Globale të Sistemit</h2>
        
        <!-- Global Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-200 rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="bg-blue-600 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">CPU Global</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($liveMetrics['cpu_usage'] ?? 0, 1) }}%</p>
                        </div>
                    </div>
                </div>
                <div class="w-full bg-blue-200 rounded-full h-3">
                    <div class="bg-blue-600 h-3 rounded-full transition-all duration-500" style="width: {{ min($liveMetrics['cpu_usage'] ?? 0, 100) }}%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-2">Përdorimi aktual i CPU-së në sistem</p>
            </div>
            
            <div class="bg-gradient-to-br from-green-50 to-green-100 border-2 border-green-200 rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="bg-green-600 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Memoria Global</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($liveMetrics['memory_usage'] ?? 0, 1) }}%</p>
                        </div>
                    </div>
                </div>
                <div class="w-full bg-green-200 rounded-full h-3">
                    <div class="bg-green-600 h-3 rounded-full transition-all duration-500" style="width: {{ min($liveMetrics['memory_usage'] ?? 0, 100) }}%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-2">Përdorimi aktual i memories në sistem</p>
            </div>
            
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 border-2 border-purple-200 rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="bg-purple-600 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Disk Global</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($liveMetrics['disk_usage'] ?? 0, 1) }}%</p>
                        </div>
                    </div>
                </div>
                <div class="w-full bg-purple-200 rounded-full h-3">
                    <div class="bg-purple-600 h-3 rounded-full transition-all duration-500" style="width: {{ min($liveMetrics['disk_usage'] ?? 0, 100) }}%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-2">Përdorimi aktual i diskut në sistem</p>
            </div>
        </div>
        
        <!-- Timestamp -->
        <div class="text-center text-sm text-gray-500 mt-4">
            <p>Përditësuar: {{ $liveMetrics['timestamp'] ?? now()->format('Y-m-d H:i:s') }}</p>
        </div>
        
        <!-- Global Metrics Charts - Gauge Charts -->
        <div class="mt-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Grafiku i Metrikave Globale</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6" wire:ignore>
                <!-- CPU Chart -->
                <div class="bg-white border-2 border-blue-200 rounded-xl p-6 shadow-sm">
                    <h4 class="text-sm font-semibold text-gray-700 mb-4 text-center">CPU Global</h4>
                    <div class="relative" style="height: 200px;">
                        <canvas id="globalCpuChart"></canvas>
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-blue-600" id="globalCpuValue">{{ number_format($liveMetrics['cpu_usage'] ?? 0, 1) }}%</div>
                                <div class="text-xs text-gray-500 mt-1">Përdorim</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Memory Chart -->
                <div class="bg-white border-2 border-green-200 rounded-xl p-6 shadow-sm">
                    <h4 class="text-sm font-semibold text-gray-700 mb-4 text-center">Memoria Global</h4>
                    <div class="relative" style="height: 200px;">
                        <canvas id="globalMemoryChart"></canvas>
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-green-600" id="globalMemoryValue">{{ number_format($liveMetrics['memory_usage'] ?? 0, 1) }}%</div>
                                <div class="text-xs text-gray-500 mt-1">Përdorim</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Disk Chart -->
                <div class="bg-white border-2 border-purple-200 rounded-xl p-6 shadow-sm">
                    <h4 class="text-sm font-semibold text-gray-700 mb-4 text-center">Disk Global</h4>
                    <div class="relative" style="height: 200px;">
                        <canvas id="globalDiskChart"></canvas>
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-purple-600" id="globalDiskValue">{{ number_format($liveMetrics['disk_usage'] ?? 0, 1) }}%</div>
                                <div class="text-xs text-gray-500 mt-1">Përdorim</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2 text-center">Grafikët përditësohen automatikisht çdo 2 sekonda</p>
        </div>
        
        <!-- System Capacity Chart - Bar Chart -->
        <div class="mt-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Grafiku i Kapacitetit të Sistemit</h3>
            <div class="bg-gray-50 rounded-lg p-4" style="height: 350px;">
                <canvas id="capacityChart"></canvas>
            </div>
            <p class="text-xs text-gray-500 mt-2 text-center">Krahasimi i kapacitetit të rezervuar me atë të lirë</p>
        </div>
        
        <!-- Metrics History Chart -->
        @if(!empty($metricsHistory['labels']))
        <div class="mt-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Historiku i Metrikave (Koha Reale)</h3>
            <div class="bg-gray-50 rounded-lg p-4" style="height: 400px;">
                <canvas id="historyChart"></canvas>
            </div>
            <p class="text-xs text-gray-500 mt-2 text-center">Trendi i metrikave në kohë reale</p>
        </div>
        @endif
    </div>
    
    <!-- System Capacity Overview -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Përmbledhje e Kapacitetit të Sistemit</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- CPU -->
            <div class="bg-gray-50 border-2 border-gray-200 rounded-xl p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">CPU</h3>
                    <span class="text-2xl font-bold text-blue-600">{{ $systemStats['total_reserved_cpu'] }}%</span>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Rezervuar:</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $systemStats['total_reserved_cpu'] }}%</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">E lirë:</span>
                        <span class="text-sm font-semibold text-green-600">{{ $systemStats['available_cpu'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4 mt-3">
                        <div class="bg-blue-600 h-4 rounded-full flex items-center justify-end pr-2" style="width: {{ min(($systemStats['total_reserved_cpu'] / 100) * 100, 100) }}%">
                            <span class="text-xs text-white font-medium">{{ $systemStats['total_reserved_cpu'] }}%</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Kapaciteti maksimal: 100%</p>
                </div>
            </div>
            
            <!-- Memory -->
            <div class="bg-gray-50 border-2 border-gray-200 rounded-xl p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Memoria</h3>
                    <span class="text-2xl font-bold text-green-600">{{ $systemStats['total_reserved_memory'] }} MB</span>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Rezervuar:</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $systemStats['total_reserved_memory'] }} MB</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">E lirë:</span>
                        <span class="text-sm font-semibold text-green-600">{{ $systemStats['available_memory'] }} MB</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4 mt-3">
                        <div class="bg-green-600 h-4 rounded-full flex items-center justify-end pr-2" style="width: {{ min(($systemStats['total_reserved_memory'] / 16000) * 100, 100) }}%">
                            <span class="text-xs text-white font-medium">{{ round(($systemStats['total_reserved_memory'] / 16000) * 100, 1) }}%</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Kapaciteti maksimal: 16,000 MB</p>
                </div>
            </div>
            
            <!-- Storage -->
            <div class="bg-gray-50 border-2 border-gray-200 rounded-xl p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Storage</h3>
                    <span class="text-2xl font-bold text-purple-600">{{ $systemStats['total_reserved_storage'] }} GB</span>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Rezervuar:</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $systemStats['total_reserved_storage'] }} GB</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">E lirë:</span>
                        <span class="text-sm font-semibold text-green-600">{{ $systemStats['available_storage'] }} GB</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4 mt-3">
                        <div class="bg-purple-600 h-4 rounded-full flex items-center justify-end pr-2" style="width: {{ min(($systemStats['total_reserved_storage'] / 1000) * 100, 100) }}%">
                            <span class="text-xs text-white font-medium">{{ round(($systemStats['total_reserved_storage'] / 1000) * 100, 1) }}%</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Kapaciteti maksimal: 1,000 GB</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- All Resources Summary Table -->
    @if($resources->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Përmbledhje e të Gjitha Resurseve</h2>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Emri</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statusi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CPU Rezervuar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Memory Rezervuar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Storage Rezervuar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rajoni</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($resources as $resource)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $resource->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-600">{{ $resource->type }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $resource->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $resource->status === 'active' ? 'Running' : 'Stopped' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $resource->cpu_capacity ?? 0 }}%</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $resource->memory_capacity ?? 0 }} MB</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $resource->storage_capacity ?? 0 }} GB</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-600">{{ $resource->region ?? 'N/A' }}</div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart === 'undefined') {
        console.error('Chart.js nuk është i ngarkuar!');
        return;
    }

    let globalCpuChart = null;
    let globalMemoryChart = null;
    let globalDiskChart = null;
    let capacityChart = null;
    let historyChart = null;

    // Funksion për të krijuar gauge chart
    function createGaugeChart(canvasId, value, color, chartVar) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;
        
        if (chartVar) {
            try {
                chartVar.destroy();
            } catch (e) {
                console.warn('Error destroying chart:', e);
            }
        }
        
        const val = Math.min(Math.max(parseFloat(value) || 0, 0), 100);
        const remaining = 100 - val;
        
        const valueElement = document.getElementById(canvasId.replace('Chart', 'Value'));
        if (valueElement) {
            valueElement.textContent = val.toFixed(1) + '%';
        }
        
        try {
            return new Chart(ctx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Përdorim', 'E lirë'],
                    datasets: [{
                        data: [val, remaining],
                        backgroundColor: [color, 'rgb(229, 231, 235)'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    animation: {
                        duration: 500
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => ctx.label + ': ' + ctx.parsed.toFixed(1) + '%'
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error creating gauge chart:', error);
            return null;
        }
    }

    // Funksion për të inicializuar të gjitha grafikët
    function initializeCharts() {
        @if($liveMetrics)
        const metrics = @json($liveMetrics);
        
        // Krijo gauge charts
        globalCpuChart = createGaugeChart('globalCpuChart', metrics.cpu_usage, 'rgb(59, 130, 246)', globalCpuChart);
        globalMemoryChart = createGaugeChart('globalMemoryChart', metrics.memory_usage, 'rgb(34, 197, 94)', globalMemoryChart);
        globalDiskChart = createGaugeChart('globalDiskChart', metrics.disk_usage, 'rgb(168, 85, 247)', globalDiskChart);
        
        // Capacity Chart
        const capacityCtx = document.getElementById('capacityChart');
        if (capacityCtx) {
            const systemStats = @json($systemStats);
            
            if (capacityChart) {
                capacityChart.destroy();
            }
            
            capacityChart = new Chart(capacityCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['CPU', 'Memoria', 'Storage'],
                    datasets: [
                        {
                            label: 'Rezervuar',
                            data: [
                                systemStats.total_reserved_cpu || 0,
                                systemStats.total_reserved_memory || 0,
                                systemStats.total_reserved_storage || 0
                            ],
                            backgroundColor: ['rgba(239, 68, 68, 0.9)', 'rgba(234, 179, 8, 0.9)', 'rgba(249, 115, 22, 0.9)'],
                            borderColor: ['rgb(239, 68, 68)', 'rgb(234, 179, 8)', 'rgb(249, 115, 22)'],
                            borderWidth: 2,
                            borderRadius: 5
                        },
                        {
                            label: 'E lirë',
                            data: [
                                systemStats.available_cpu || 0,
                                systemStats.available_memory || 0,
                                systemStats.available_storage || 0
                            ],
                            backgroundColor: ['rgba(34, 197, 94, 0.9)', 'rgba(34, 197, 94, 0.9)', 'rgba(34, 197, 94, 0.9)'],
                            borderColor: ['rgb(34, 197, 94)', 'rgb(34, 197, 94)', 'rgb(34, 197, 94)'],
                            borderWidth: 2,
                            borderRadius: 5
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 15,
                                font: { size: 12, weight: 'bold' }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) label += ': ';
                                    if (context.dataIndex === 0) {
                                        label += context.parsed.y.toFixed(1) + '%';
                                    } else if (context.dataIndex === 1) {
                                        label += context.parsed.y.toFixed(0) + ' MB';
                                    } else {
                                        label += context.parsed.y.toFixed(0) + ' GB';
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 12, weight: 'bold' } }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Vlera',
                                font: { size: 14, weight: 'bold' }
                            },
                            ticks: {
                                font: { size: 11 },
                                callback: function(value) {
                                    if (value >= 1000) {
                                        return (value / 1000).toFixed(1) + 'K';
                                    }
                                    return value;
                                }
                            },
                            grid: { color: 'rgba(0, 0, 0, 0.1)' }
                        }
                    }
                }
            });
        }
        
        // History Chart
        @if(!empty($metricsHistory['labels']))
        const historyCtx = document.getElementById('historyChart');
        if (historyCtx) {
            const history = @json($metricsHistory);
            
            if (historyChart) {
                historyChart.destroy();
            }
            
            historyChart = new Chart(historyCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: history.labels || [],
                    datasets: [
                        {
                            label: 'CPU (%)',
                            data: history.cpu || [],
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointRadius: 2,
                            pointHoverRadius: 5
                        },
                        {
                            label: 'Memoria (%)',
                            data: history.memory || [],
                            borderColor: 'rgb(34, 197, 94)',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointRadius: 2,
                            pointHoverRadius: 5
                        },
                        {
                            label: 'Disk (%)',
                            data: history.disk || [],
                            borderColor: 'rgb(168, 85, 247)',
                            backgroundColor: 'rgba(168, 85, 247, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointRadius: 2,
                            pointHoverRadius: 5
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 15,
                                font: { size: 12, weight: 'bold' }
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y.toFixed(2) + '%';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Përqindje (%)',
                                font: { size: 14, weight: 'bold' }
                            },
                            ticks: {
                                font: { size: 11 },
                                callback: function(value) {
                                    return value + '%';
                                }
                            },
                            grid: { color: 'rgba(0, 0, 0, 0.1)' }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Koha',
                                font: { size: 14, weight: 'bold' }
                            },
                            ticks: {
                                font: { size: 11 },
                                maxTicksLimit: 20
                            },
                            grid: { display: false }
                        }
                    }
                }
            });
        }
        @endif
        @endif
    }

    // Përditëso grafikët kur Livewire përditësohet
    Livewire.hook('morph.updated', () => {
        setTimeout(() => {
            @if($liveMetrics)
            const metrics = @json($liveMetrics);
            
            // Përditëso gauge charts
            if (globalCpuChart) {
                const cpu = Math.min(Math.max(parseFloat(metrics.cpu_usage) || 0, 0), 100);
                globalCpuChart.data.datasets[0].data = [cpu, 100 - cpu];
                globalCpuChart.update('none');
                const cpuValueEl = document.getElementById('globalCpuValue');
                if (cpuValueEl) cpuValueEl.textContent = cpu.toFixed(1) + '%';
            }
            
            if (globalMemoryChart) {
                const mem = Math.min(Math.max(parseFloat(metrics.memory_usage) || 0, 0), 100);
                globalMemoryChart.data.datasets[0].data = [mem, 100 - mem];
                globalMemoryChart.update('none');
                const memValueEl = document.getElementById('globalMemoryValue');
                if (memValueEl) memValueEl.textContent = mem.toFixed(1) + '%';
            }
            
            if (globalDiskChart) {
                const disk = Math.min(Math.max(parseFloat(metrics.disk_usage) || 0, 0), 100);
                globalDiskChart.data.datasets[0].data = [disk, 100 - disk];
                globalDiskChart.update('none');
                const diskValueEl = document.getElementById('globalDiskValue');
                if (diskValueEl) diskValueEl.textContent = disk.toFixed(1) + '%';
            }
            
            // Përditëso capacity chart
            if (capacityChart) {
                const systemStats = @json($systemStats);
                capacityChart.data.datasets[0].data = [
                    systemStats.total_reserved_cpu || 0,
                    systemStats.total_reserved_memory || 0,
                    systemStats.total_reserved_storage || 0
                ];
                capacityChart.data.datasets[1].data = [
                    systemStats.available_cpu || 0,
                    systemStats.available_memory || 0,
                    systemStats.available_storage || 0
                ];
                capacityChart.update('none');
            }
            
            // Përditëso history chart
            @if(!empty($metricsHistory['labels']))
            if (historyChart) {
                const history = @json($metricsHistory);
                historyChart.data.labels = history.labels || [];
                historyChart.data.datasets[0].data = history.cpu || [];
                historyChart.data.datasets[1].data = history.memory || [];
                historyChart.data.datasets[2].data = history.disk || [];
                historyChart.update('none');
            }
            @endif
            @endif
        }, 100);
    });

    // Inicializo grafikët pas ngarkimit
    setTimeout(initializeCharts, 500);
});
</script>

