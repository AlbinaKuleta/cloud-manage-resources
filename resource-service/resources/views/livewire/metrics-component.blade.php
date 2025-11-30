<div wire:poll.2s="fetchLiveMetrics">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Monitorimi i Metrikave</h1>
                <p class="mt-2 text-sm text-gray-600">Analizë e performancës së sistemit në kohë reale</p>
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

    @if($liveMetrics)
    @php
        $stats = $this->getMetricsStatistics();
    @endphp
    
    <!-- Overview Section - Metrikat Aktuale -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Përmbledhje e Metrikave</h2>
        
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
                            <p class="text-sm font-medium text-gray-600">CPU Usage</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($liveMetrics['cpu_usage'] ?? 0, 1) }}%</p>
                        </div>
                    </div>
                </div>
                <div class="w-full bg-blue-200 rounded-full h-3 mb-2">
                    <div class="bg-blue-600 h-3 rounded-full transition-all duration-500" style="width: {{ min($liveMetrics['cpu_usage'] ?? 0, 100) }}%"></div>
                </div>
                <div class="flex justify-between text-xs text-gray-600">
                    <span>Min: {{ $stats['cpu']['min'] }}%</span>
                    <span>Max: {{ $stats['cpu']['max'] }}%</span>
                    <span>Avg: {{ $stats['cpu']['avg'] }}%</span>
                </div>
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
                            <p class="text-sm font-medium text-gray-600">Memory Usage</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($liveMetrics['memory_usage'] ?? 0, 1) }}%</p>
                        </div>
                    </div>
                </div>
                <div class="w-full bg-green-200 rounded-full h-3 mb-2">
                    <div class="bg-green-600 h-3 rounded-full transition-all duration-500" style="width: {{ min($liveMetrics['memory_usage'] ?? 0, 100) }}%"></div>
                </div>
                <div class="flex justify-between text-xs text-gray-600">
                    <span>Min: {{ $stats['memory']['min'] }}%</span>
                    <span>Max: {{ $stats['memory']['max'] }}%</span>
                    <span>Avg: {{ $stats['memory']['avg'] }}%</span>
                </div>
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
                            <p class="text-sm font-medium text-gray-600">Disk Usage</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($liveMetrics['disk_usage'] ?? 0, 1) }}%</p>
                        </div>
                    </div>
                </div>
                <div class="w-full bg-purple-200 rounded-full h-3 mb-2">
                    <div class="bg-purple-600 h-3 rounded-full transition-all duration-500" style="width: {{ min($liveMetrics['disk_usage'] ?? 0, 100) }}%"></div>
                </div>
                <div class="flex justify-between text-xs text-gray-600">
                    <span>Min: {{ $stats['disk']['min'] }}%</span>
                    <span>Max: {{ $stats['disk']['max'] }}%</span>
                    <span>Avg: {{ $stats['disk']['avg'] }}%</span>
                </div>
            </div>
        </div>
        
        <div class="text-center text-sm text-gray-500">
            <p>Përditësuar: {{ $liveMetrics['timestamp'] ?? now()->format('Y-m-d H:i:s') }}</p>
        </div>
    </div>
    
    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Real-time Trends Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6" wire:ignore>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Trendet në Kohë Reale</h3>
            <div class="bg-gray-50 rounded-lg p-4" style="height: 350px;">
                <canvas id="realtimeTrendsChart"></canvas>
            </div>
            <p class="text-xs text-gray-500 mt-2 text-center">Evolucioni i CPU, Memory dhe Disk usage</p>
        </div>
        
        <!-- Capacity Comparison Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6" wire:ignore>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Krahasimi i Kapacitetit</h3>
            <div class="bg-gray-50 rounded-lg p-4" style="height: 350px;">
                <canvas id="capacityChart"></canvas>
            </div>
            <p class="text-xs text-gray-500 mt-2 text-center">Rezervuar vs i disponueshëm</p>
        </div>
    </div>
    
    <!-- Historical Analysis Chart -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8" wire:ignore>
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Analiza Historike</h3>
        <div class="bg-gray-50 rounded-lg p-4" style="height: 400px;">
            <canvas id="historyChart"></canvas>
        </div>
        <p class="text-xs text-gray-500 mt-2 text-center">Historiku i metrikave për analizë të trendeve</p>
    </div>
    
    <!-- System Capacity Overview -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Kapaciteti i Sistemit</h2>
        
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
    
    <!-- Hidden input për të dhënat e grafikëve që Livewire i përditëson automatikisht -->
    @php
        $chartData = [
            'current' => [
                'cpu' => ($liveMetrics['cpu_usage'] ?? 0),
                'memory' => ($liveMetrics['memory_usage'] ?? 0),
                'disk' => ($liveMetrics['disk_usage'] ?? 0),
            ],
            'history' => ($metricsHistory ?? []),
            'capacity' => ($systemStats ?? [])
        ];
    @endphp
    <input type="hidden" id="chartDataJson" value="@json($chartData)" wire:ignore>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    'use strict';
    
    let trendsChart = null;
    let capacityChart = null;
    let historyChart = null;
    
    // Inicializo chartData me vlera default nëse nuk ka të dhëna
    let chartData = @json($chartData);
    if (!chartData || !chartData.current) {
        chartData = {
            current: { cpu: 0, memory: 0, disk: 0 },
            history: { labels: [], cpu: [], memory: [], disk: [] },
            capacity: { total_reserved_cpu: 0, total_reserved_memory: 0, total_reserved_storage: 0, available_cpu: 0, available_memory: 0, available_storage: 0 }
        };
    }
    
    console.log('Initializing charts with data:', chartData);
    
    function waitForChartJS(callback, retries = 50) {
        if (typeof Chart !== 'undefined') {
            console.log('Chart.js is loaded');
            callback();
        } else if (retries > 0) {
            setTimeout(() => waitForChartJS(callback, retries - 1), 100);
        } else {
            console.error('Chart.js failed to load after multiple attempts');
        }
    }
    
    // Grafik për trendet në kohë reale
    function initTrendsChart() {
        const ctx = document.getElementById('realtimeTrendsChart');
        if (!ctx) {
            console.warn('realtimeTrendsChart canvas not found');
            return false;
        }
        
        if (trendsChart) {
            trendsChart.destroy();
        }
        
        let labels = [];
        let cpuData = [];
        let memoryData = [];
        let diskData = [];
        
        if (chartData.history && chartData.history.labels && chartData.history.labels.length > 0) {
            labels = chartData.history.labels;
            cpuData = chartData.history.cpu || [];
            memoryData = chartData.history.memory || [];
            diskData = chartData.history.disk || [];
        } else {
            labels = [new Date().toLocaleTimeString()];
            cpuData = [chartData.current.cpu || 0];
            memoryData = [chartData.current.memory || 0];
            diskData = [chartData.current.disk || 0];
        }
        
        try {
            trendsChart = new Chart(ctx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'CPU (%)',
                            data: cpuData,
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointRadius: 2,
                            pointHoverRadius: 5,
                            borderWidth: 2
                        },
                        {
                            label: 'Memoria (%)',
                            data: memoryData,
                            borderColor: 'rgb(34, 197, 94)',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointRadius: 2,
                            pointHoverRadius: 5,
                            borderWidth: 2
                        },
                        {
                            label: 'Disk (%)',
                            data: diskData,
                            borderColor: 'rgb(168, 85, 247)',
                            backgroundColor: 'rgba(168, 85, 247, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointRadius: 2,
                            pointHoverRadius: 5,
                            borderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 0 },
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: { usePointStyle: true, padding: 15, font: { size: 11, weight: 'bold' } }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 10,
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
                            title: { display: true, text: 'Përqindje (%)', font: { size: 12, weight: 'bold' } },
                            ticks: { font: { size: 10 }, callback: function(value) { return value + '%'; } },
                            grid: { color: 'rgba(0, 0, 0, 0.1)' }
                        },
                        x: {
                            title: { display: true, text: 'Koha', font: { size: 12, weight: 'bold' } },
                            ticks: { font: { size: 10 }, maxTicksLimit: 10 },
                            grid: { display: false }
                        }
                    }
                }
            });
            console.log('✓ Trends chart initialized');
            return true;
        } catch (error) {
            console.error('Error initializing trends chart:', error);
            return false;
        }
    }
    
    // Grafik për kapacitetin
    function initCapacityChart() {
        const ctx = document.getElementById('capacityChart');
        if (!ctx) {
            console.warn('capacityChart canvas not found');
            return false;
        }
        
        if (capacityChart) {
            capacityChart.destroy();
        }
        
        const capacity = chartData.capacity || {};
        
        try {
            capacityChart = new Chart(ctx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['CPU (%)', 'Memoria (MB)', 'Storage (GB)'],
                    datasets: [
                        {
                            label: 'Rezervuar',
                            data: [
                                capacity.total_reserved_cpu || 0,
                                capacity.total_reserved_memory || 0,
                                capacity.total_reserved_storage || 0
                            ],
                            backgroundColor: ['rgba(59, 130, 246, 0.8)', 'rgba(34, 197, 94, 0.8)', 'rgba(168, 85, 247, 0.8)'],
                            borderColor: ['rgb(59, 130, 246)', 'rgb(34, 197, 94)', 'rgb(168, 85, 247)'],
                            borderWidth: 2,
                            borderRadius: 5
                        },
                        {
                            label: 'E lirë',
                            data: [
                                capacity.available_cpu || 0,
                                capacity.available_memory || 0,
                                capacity.available_storage || 0
                            ],
                            backgroundColor: ['rgba(156, 163, 175, 0.8)', 'rgba(156, 163, 175, 0.8)', 'rgba(156, 163, 175, 0.8)'],
                            borderColor: ['rgb(156, 163, 175)', 'rgb(156, 163, 175)', 'rgb(156, 163, 175)'],
                            borderWidth: 2,
                            borderRadius: 5
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 0 },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: { usePointStyle: true, padding: 15, font: { size: 11, weight: 'bold' } }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 10,
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
                            ticks: { font: { size: 11, weight: 'bold' } }
                        },
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Vlera', font: { size: 12, weight: 'bold' } },
                            ticks: {
                                font: { size: 10 },
                                callback: function(value) {
                                    if (value >= 1000) return (value / 1000).toFixed(1) + 'K';
                                    return value;
                                }
                            },
                            grid: { color: 'rgba(0, 0, 0, 0.1)' }
                        }
                    }
                }
            });
            console.log('✓ Capacity chart initialized');
            return true;
        } catch (error) {
            console.error('Error initializing capacity chart:', error);
            return false;
        }
    }
    
    // Grafik për historikun
    function initHistoryChart() {
        const ctx = document.getElementById('historyChart');
        if (!ctx) {
            console.warn('historyChart canvas not found');
            return false;
        }
        
        if (historyChart) {
            historyChart.destroy();
        }
        
        let labels = [];
        let cpuData = [];
        let memoryData = [];
        let diskData = [];
        
        if (chartData.history && chartData.history.labels && chartData.history.labels.length > 0) {
            labels = chartData.history.labels;
            cpuData = chartData.history.cpu || [];
            memoryData = chartData.history.memory || [];
            diskData = chartData.history.disk || [];
        } else {
            labels = [new Date().toLocaleTimeString()];
            cpuData = [chartData.current.cpu || 0];
            memoryData = [chartData.current.memory || 0];
            diskData = [chartData.current.disk || 0];
        }
        
        try {
            historyChart = new Chart(ctx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'CPU (%)',
                            data: cpuData,
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointRadius: 2,
                            pointHoverRadius: 5,
                            borderWidth: 2
                        },
                        {
                            label: 'Memoria (%)',
                            data: memoryData,
                            borderColor: 'rgb(34, 197, 94)',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointRadius: 2,
                            pointHoverRadius: 5,
                            borderWidth: 2
                        },
                        {
                            label: 'Disk (%)',
                            data: diskData,
                            borderColor: 'rgb(168, 85, 247)',
                            backgroundColor: 'rgba(168, 85, 247, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointRadius: 2,
                            pointHoverRadius: 5,
                            borderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 0 },
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: { usePointStyle: true, padding: 15, font: { size: 11, weight: 'bold' } }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 10,
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
                            title: { display: true, text: 'Përqindje (%)', font: { size: 12, weight: 'bold' } },
                            ticks: { font: { size: 10 }, callback: function(value) { return value + '%'; } },
                            grid: { color: 'rgba(0, 0, 0, 0.1)' }
                        },
                        x: {
                            title: { display: true, text: 'Koha', font: { size: 12, weight: 'bold' } },
                            ticks: { font: { size: 10 }, maxTicksLimit: 15 },
                            grid: { display: false }
                        }
                    }
                }
            });
            console.log('✓ History chart initialized');
            return true;
        } catch (error) {
            console.error('Error initializing history chart:', error);
            return false;
        }
    }
    
    function getChartDataFromDOM() {
        // Merr të dhënat e reja nga hidden input që Livewire e përditëson
        const dataInput = document.getElementById('chartDataJson');
        if (dataInput && dataInput.value) {
            try {
                return JSON.parse(dataInput.value);
            } catch (e) {
                console.error('Error parsing chart data:', e);
            }
        }
        return null;
    }
    
    function updateAllCharts() {
        // Merr të dhënat e reja nga DOM
        const newChartData = getChartDataFromDOM();
        if (newChartData) {
            chartData = newChartData;
        }
        
        // Përditëso trends chart
        if (trendsChart && chartData) {
            let labels = (chartData.history && chartData.history.labels) ? chartData.history.labels : [new Date().toLocaleTimeString()];
            let cpuData = (chartData.history && chartData.history.cpu) ? chartData.history.cpu : [chartData.current.cpu || 0];
            let memoryData = (chartData.history && chartData.history.memory) ? chartData.history.memory : [chartData.current.memory || 0];
            let diskData = (chartData.history && chartData.history.disk) ? chartData.history.disk : [chartData.current.disk || 0];
            
            if (labels.length > 50) {
                labels = labels.slice(-50);
                cpuData = cpuData.slice(-50);
                memoryData = memoryData.slice(-50);
                diskData = diskData.slice(-50);
            }
            
            trendsChart.data.labels = labels;
            trendsChart.data.datasets[0].data = cpuData;
            trendsChart.data.datasets[1].data = memoryData;
            trendsChart.data.datasets[2].data = diskData;
            trendsChart.update('none');
        }
        
        // Përditëso capacity chart
        if (capacityChart && chartData && chartData.capacity) {
            const cap = chartData.capacity;
            capacityChart.data.datasets[0].data = [
                cap.total_reserved_cpu || 0,
                cap.total_reserved_memory || 0,
                cap.total_reserved_storage || 0
            ];
            capacityChart.data.datasets[1].data = [
                cap.available_cpu || 0,
                cap.available_memory || 0,
                cap.available_storage || 0
            ];
            capacityChart.update('none');
        }
        
        // Përditëso history chart
        if (historyChart && chartData) {
            let labels = (chartData.history && chartData.history.labels) ? chartData.history.labels : [new Date().toLocaleTimeString()];
            let cpuData = (chartData.history && chartData.history.cpu) ? chartData.history.cpu : [chartData.current.cpu || 0];
            let memoryData = (chartData.history && chartData.history.memory) ? chartData.history.memory : [chartData.current.memory || 0];
            let diskData = (chartData.history && chartData.history.disk) ? chartData.history.disk : [chartData.current.disk || 0];
            
            historyChart.data.labels = labels;
            historyChart.data.datasets[0].data = cpuData;
            historyChart.data.datasets[1].data = memoryData;
            historyChart.data.datasets[2].data = diskData;
            historyChart.update('none');
        }
    }
    
    function initAllCharts() {
        waitForChartJS(() => {
            setTimeout(() => {
                initTrendsChart();
                initCapacityChart();
                initHistoryChart();
            }, 100);
        });
    }
    
    // Inicializo kur Livewire është gati
    if (window.Livewire) {
        // Përditëso grafikët pas çdo update të Livewire DOM
        Livewire.hook('morph.updated', () => {
            setTimeout(() => {
                updateAllCharts();
            }, 150);
        });
        
        // Dëgjo për eventin metrics-updated
        Livewire.on('metrics-updated', (data) => {
            if (data && data[0]) {
                chartData = {
                    current: {
                        cpu: data[0].metrics?.cpu_usage || chartData.current?.cpu || 0,
                        memory: data[0].metrics?.memory_usage || chartData.current?.memory || 0,
                        disk: data[0].metrics?.disk_usage || chartData.current?.disk || 0
                    },
                    history: data[0].history || chartData.history || { labels: [], cpu: [], memory: [], disk: [] },
                    capacity: data[0].capacity || chartData.capacity || {}
                };
                updateAllCharts();
            }
        });
    }
    
    // Inicializo grafikët
    waitForChartJS(() => {
        setTimeout(initAllCharts, 500);
    });
    
    // Përditëso grafikët automatikisht çdo 2 sekonda për real-time updates
    // Kjo siguron që grafikët përditësohen automatikisht pa nevojë për refresh
    setInterval(() => {
        if (trendsChart || capacityChart || historyChart) {
            updateAllCharts();
        }
    }, 2000);
});
</script>
