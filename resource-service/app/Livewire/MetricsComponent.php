<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Resource;
use App\Models\MetricsHistory;
use App\Services\MonitoringService;
use Illuminate\Support\Facades\Http;

class MetricsComponent extends Component
{
    public $resources = [];
    public $liveMetrics = null;
    public $selectedResourceId = null;
    public $selectedResource = null;
    
    // System capacity stats
    public $systemStats = [
        'total_reserved_cpu' => 0,
        'total_reserved_memory' => 0,
        'total_reserved_storage' => 0,
        'available_cpu' => 0,
        'available_memory' => 0,
        'available_storage' => 0,
    ];
    
    // Metrics history for charts
    public $metricsHistory = [];

    public function mount()
    {
        $this->loadResources();
        $this->fetchLiveMetrics();
        $this->calculateSystemStats();
        $this->loadMetricsHistory();
        
        // Select first resource by default
        if ($this->resources->count() > 0) {
            $this->selectResource($this->resources->first()->id);
        }
    }
    
    /**
     * Ngarkon historikun e metrikave për grafikët
     */
    public function loadMetricsHistory()
    {
        // Merr të dhënat e fundit (50 rekordet e fundit) për performancë më të mirë
        $history = MetricsHistory::orderBy('recorded_at', 'asc')
            ->limit(50)
            ->get();
        
        if ($history->count() > 0) {
            $this->metricsHistory = [
                'labels' => $history->map(fn($m) => $m->recorded_at->format('H:i:s'))->toArray(),
                'cpu' => $history->pluck('cpu_usage')->toArray(),
                'memory' => $history->pluck('memory_usage')->toArray(),
                'disk' => $history->pluck('disk_usage')->toArray(),
            ];
        } else {
            // Nëse nuk ka historik, krijo të dhëna bosh
            $this->metricsHistory = [
                'labels' => [],
                'cpu' => [],
                'memory' => [],
                'disk' => [],
            ];
        }
    }
    
    /**
     * Llogarit statistikat e metrikave për analiza
     */
    public function getMetricsStatistics()
    {
        $history = MetricsHistory::orderBy('recorded_at', 'desc')
            ->limit(50)
            ->get();
        
        if ($history->count() === 0) {
            return [
                'cpu' => ['avg' => 0, 'min' => 0, 'max' => 0, 'trend' => 'stable'],
                'memory' => ['avg' => 0, 'min' => 0, 'max' => 0, 'trend' => 'stable'],
                'disk' => ['avg' => 0, 'min' => 0, 'max' => 0, 'trend' => 'stable'],
            ];
        }
        
        $cpuValues = $history->pluck('cpu_usage')->toArray();
        $memoryValues = $history->pluck('memory_usage')->toArray();
        $diskValues = $history->pluck('disk_usage')->toArray();
        
        // Llogarit trend (rritje, ulje, ose stabil)
        $getTrend = function($values) {
            if (count($values) < 2) return 'stable';
            $first = array_slice($values, 0, 5);
            $last = array_slice($values, -5);
            $firstAvg = array_sum($first) / count($first);
            $lastAvg = array_sum($last) / count($last);
            if ($lastAvg > $firstAvg + 2) return 'increasing';
            if ($lastAvg < $firstAvg - 2) return 'decreasing';
            return 'stable';
        };
        
        return [
            'cpu' => [
                'avg' => round(array_sum($cpuValues) / count($cpuValues), 2),
                'min' => round(min($cpuValues), 2),
                'max' => round(max($cpuValues), 2),
                'trend' => $getTrend($cpuValues)
            ],
            'memory' => [
                'avg' => round(array_sum($memoryValues) / count($memoryValues), 2),
                'min' => round(min($memoryValues), 2),
                'max' => round(max($memoryValues), 2),
                'trend' => $getTrend($memoryValues)
            ],
            'disk' => [
                'avg' => round(array_sum($diskValues) / count($diskValues), 2),
                'min' => round(min($diskValues), 2),
                'max' => round(max($diskValues), 2),
                'trend' => $getTrend($diskValues)
            ],
        ];
    }
    
    /**
     * Përcakton kapacitetin maksimal të sistemit
     */
    private function getSystemMaxCapacity()
    {
        return [
            'cpu' => 100,
            'memory' => 16000,
            'storage' => 1000,
        ];
    }
    
    /**
     * Llogarit statistikat e sistemit
     */
    public function calculateSystemStats()
    {
        $maxCapacity = $this->getSystemMaxCapacity();
        
        $this->systemStats['total_reserved_cpu'] = $this->resources->where('status', 'active')->sum('cpu_capacity');
        $this->systemStats['total_reserved_memory'] = $this->resources->where('status', 'active')->sum('memory_capacity');
        $this->systemStats['total_reserved_storage'] = $this->resources->where('status', 'active')->sum('storage_capacity');
        
        $this->systemStats['available_cpu'] = max(0, $maxCapacity['cpu'] - $this->systemStats['total_reserved_cpu']);
        $this->systemStats['available_memory'] = max(0, $maxCapacity['memory'] - $this->systemStats['total_reserved_memory']);
        $this->systemStats['available_storage'] = max(0, $maxCapacity['storage'] - $this->systemStats['total_reserved_storage']);
    }
    
    public function updatedSelectedResourceId()
    {
        if ($this->selectedResourceId) {
            $this->selectResource($this->selectedResourceId);
        }
    }

    public function loadResources()
    {
        $this->resources = Resource::all();
    }

    public function fetchLiveMetrics()
    {
        $monitoringService = new MonitoringService();
        $newMetrics = $monitoringService->getSystemMetrics();
        
        // Përditëso gjithmonë, edhe nëse të dhënat nuk kanë ndryshuar
        if ($newMetrics) {
            $this->liveMetrics = $newMetrics;
            $this->calculateSystemStats();
            // Ngarko historikun pasi të kenë u ruajtur të dhënat e reja
            $this->loadMetricsHistory();
            
            // Dispatch event për të përditësuar grafikët
            $this->dispatch('metrics-updated', [
                'metrics' => $this->liveMetrics,
                'history' => $this->metricsHistory,
                'capacity' => $this->systemStats
            ]);
        }
    }
    

    public function selectResource($resourceId)
    {
        $this->selectedResourceId = $resourceId;
        $this->selectedResource = Resource::find($resourceId);
        
        // Dispatch event për të përditësuar grafikun e resursit
        if ($this->selectedResource && !empty($this->metricsHistory)) {
            $this->dispatch('updateResourceChart', [
                'resource' => $this->selectedResource,
                'history' => $this->metricsHistory
            ]);
        }
    }

    /**
     * Llogarit përdorimin aktual për një resurs bazuar në rezervimin e tij dhe metrikat globale
     * 
     * Logjika: Përdorimi = (Metrika globale × Rezervimi) / 100
     */
    public function calculateResourceUsage($resource, $metricType)
    {
        if (!$this->liveMetrics) {
            return null;
        }
        
        $globalMetric = match($metricType) {
            'cpu' => $this->liveMetrics['cpu_usage'] ?? 0,
            'memory' => $this->liveMetrics['memory_usage'] ?? 0,
            'disk' => $this->liveMetrics['disk_usage'] ?? 0,
            default => 0
        };
        
        $reserved = match($metricType) {
            'cpu' => $resource->cpu_capacity ?? 0,
            'memory' => $resource->memory_capacity ?? 0,
            'disk' => $resource->storage_capacity ?? 0,
            default => 0
        };
        
        if ($reserved == 0) {
            return null;
        }
        
        $usage = ($globalMetric * $reserved) / 100;
        return round($usage, 1);
    }

    public function refreshMetrics()
    {
        $this->fetchLiveMetrics();
        $this->calculateSystemStats();
        $this->loadMetricsHistory();
        session()->flash('success', 'Metrikat u rifreskuan!');
        
        // Dispatch event për të përditësuar grafikët
        $this->dispatch('updateCharts', $this->metricsHistory);
    }

    public function render()
    {
        return view('livewire.metrics-component', [
            'metricsStats' => $this->getMetricsStatistics()
        ]);
    }
}
