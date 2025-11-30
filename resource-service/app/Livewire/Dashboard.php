<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Resource;
use App\Services\MonitoringService;
use Illuminate\Support\Facades\Http;

class Dashboard extends Component
{
    public $resources = [];
    public $liveMetrics = null;
    public $stats = [
        'total_resources' => 0,
        'active_resources' => 0,
        'total_cpu_capacity' => 0,
        'total_memory_capacity' => 0,
        'total_storage_capacity' => 0,
    ];

    public function mount()
    {
        $this->loadResources();
        $this->fetchLiveMetrics();
        $this->calculateStats();
    }

    public function loadResources()
    {
        $this->resources = Resource::all();
    }

    public function fetchLiveMetrics()
    {
        $monitoringService = new MonitoringService();
        $this->liveMetrics = $monitoringService->getSystemMetrics();
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
     * Llogarit kapacitetin e lirë në sistem
     */
    public function getAvailableCapacity()
    {
        $maxCapacity = $this->getSystemMaxCapacity();
        
        $totalReserved = [
            'cpu' => $this->resources->where('status', 'active')->sum('cpu_capacity'),
            'memory' => $this->resources->where('status', 'active')->sum('memory_capacity'),
            'storage' => $this->resources->where('status', 'active')->sum('storage_capacity'),
        ];
        
        return [
            'cpu' => max(0, $maxCapacity['cpu'] - $totalReserved['cpu']),
            'memory' => max(0, $maxCapacity['memory'] - $totalReserved['memory']),
            'storage' => max(0, $maxCapacity['storage'] - $totalReserved['storage']),
        ];
    }

    public function calculateStats()
    {
        $maxCapacity = $this->getSystemMaxCapacity();
        $available = $this->getAvailableCapacity();
        
        $this->stats['total_resources'] = $this->resources->count();
        $this->stats['active_resources'] = $this->resources->where('status', 'active')->count();
        $this->stats['total_cpu_capacity'] = $this->resources->where('status', 'active')->sum('cpu_capacity');
        $this->stats['total_memory_capacity'] = $this->resources->where('status', 'active')->sum('memory_capacity');
        $this->stats['total_storage_capacity'] = $this->resources->where('status', 'active')->sum('storage_capacity');
        
        // Shto informacion për kapacitetin e lirë
        $this->stats['available_cpu'] = $available['cpu'];
        $this->stats['available_memory'] = $available['memory'];
        $this->stats['available_storage'] = $available['storage'];
        $this->stats['max_cpu'] = $maxCapacity['cpu'];
        $this->stats['max_memory'] = $maxCapacity['memory'];
        $this->stats['max_storage'] = $maxCapacity['storage'];
    }

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
        
        $capacity = match($metricType) {
            'cpu' => $resource->cpu_capacity ?? 0,
            'memory' => $resource->memory_capacity ?? 0,
            'disk' => $resource->storage_capacity ?? 0,
            default => 0
        };
        
        if ($capacity == 0) {
            return null;
        }
        
        $usage = ($globalMetric * $capacity) / 100;
        return round($usage, 1);
    }

    public function refreshMetrics()
    {
        $this->fetchLiveMetrics();
        session()->flash('success', 'Metrikat u rifreskuan!');
    }

    public function getUserRole()
    {
        return session('user_role', 'viewer');
    }
    
    public function isAdmin()
    {
        return $this->getUserRole() === 'admin';
    }
    
    public function isOperator()
    {
        return in_array($this->getUserRole(), ['admin', 'operator']);
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
