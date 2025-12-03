<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Resource;
use App\Services\MonitoringService;

class ResourceCreate extends Component
{
    public $name;
    public $type;
    public $status = 'active';
    public $cpu_capacity;
    public $memory_capacity;
    public $storage_capacity;
    public $region;

    public function mount()
    {
        // Ensure user is admin
        if (session('user_role') !== 'admin') {
            abort(403, 'Unauthorized. Admin access required.');
        }
    }

    public function getAvailableCapacity()
    {
        $monitoringService = new MonitoringService();
        $metrics = $monitoringService->getSystemMetrics();
        
        if (!$metrics) {
            return [
                'cpu' => 0,
                'memory' => 0,
                'storage' => 0
            ];
        }

        // Llogarit kapacitetin e lirë bazuar në rezervimet ekzistuese
        $totalReservedCpu = Resource::sum('cpu_capacity') ?? 0;
        $totalReservedMemory = Resource::sum('memory_capacity') ?? 0;
        $totalReservedStorage = Resource::sum('storage_capacity') ?? 0;

        // Supozojmë që sistemi ka 100% CPU, 10000 MB memory, 1000 GB storage total
        $totalCpu = 100;
        $totalMemory = 10000; // MB
        $totalStorage = 1000; // GB

        return [
            'cpu' => max(0, $totalCpu - $totalReservedCpu),
            'memory' => max(0, $totalMemory - $totalReservedMemory),
            'storage' => max(0, $totalStorage - $totalReservedStorage)
        ];
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'cpu_capacity' => 'nullable|integer|min:0|max:100',
            'memory_capacity' => 'nullable|integer|min:0',
            'storage_capacity' => 'nullable|integer|min:0',
            'region' => 'nullable|string|max:255',
        ]);

        // Kontrollo kapacitetin e lirë
        $availableCapacity = $this->getAvailableCapacity();
        $requestedCpu = $this->cpu_capacity ?? 0;
        $requestedMemory = $this->memory_capacity ?? 0;
        $requestedStorage = $this->storage_capacity ?? 0;

        // Verifikon nëse ka mjaftueshëm kapacitet të lirë
        $errors = [];
        
        if ($requestedCpu > $availableCapacity['cpu']) {
            $errors[] = "CPU: Kërkohet {$requestedCpu}%, por ka vetëm {$availableCapacity['cpu']}% të lirë.";
        }
        
        if ($requestedMemory > $availableCapacity['memory']) {
            $errors[] = "Memoria: Kërkohet {$requestedMemory} MB, por ka vetëm {$availableCapacity['memory']} MB të lirë.";
        }
        
        if ($requestedStorage > $availableCapacity['storage']) {
            $errors[] = "Storage: Kërkohet {$requestedStorage} GB, por ka vetëm {$availableCapacity['storage']} GB të lirë.";
        }

        if (!empty($errors)) {
            session()->flash('error', 'Nuk ka kapacitet të mjaftueshëm: ' . implode(' ', $errors));
            return;
        }

        // Verifikon me monitoring-service nëse sistemi është i ngarkuar
        $monitoringService = new MonitoringService();
        $capacityCheck = $monitoringService->checkCapacityAvailability(
            $requestedCpu,
            $requestedMemory,
            $requestedStorage
        );

        if (!$capacityCheck['available']) {
            session()->flash('error', $capacityCheck['message']);
            return;
        }

        // Krijo resursin
        Resource::create([
            'name' => $this->name,
            'type' => $this->type,
            'status' => $this->status,
            'cpu_capacity' => $requestedCpu,
            'memory_capacity' => $requestedMemory,
            'storage_capacity' => $requestedStorage,
            'region' => $this->region ?? 'eu-west',
        ]);

        session()->flash('success', 'Resursi u krijua me sukses! Kapaciteti u rezervua.');
        return redirect('/resources');
    }

    public function render()
    {
        // Ensure user is admin
        if (session('user_role') !== 'admin') {
            abort(403, 'Unauthorized. Admin access required.');
        }
        
        return view('livewire.resource-create');
    }
}
