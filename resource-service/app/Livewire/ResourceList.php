<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Resource;
use App\Services\MonitoringService;
use Illuminate\Support\Facades\Http;

class ResourceList extends Component
{
    public $resources = [];
    public $filteredResources = [];
    
    // Search and Filter
    public $search = '';
    public $selectedFilter = 'all';
    public $perPage = 10;
    
    // Modal states
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $showDetailsModal = false;
    
    // Form fields
    public $resourceId;
    public $name;
    public $type;
    public $status = 'active';
    public $capacity;
    public $cpu_capacity;
    public $memory_capacity;
    public $storage_capacity;
    public $region;
    
    // Delete confirmation
    public $deleteResourceId;
    public $deleteResourceName;
    
    // Details
    public $selectedResource;
    
    // Live metrics from monitoring-service
    public $liveMetrics = null;

    public function mount()
    {
        $this->loadResources();
        $this->fetchLiveMetrics();
    }
    
    public function loadResources()
    {
        $this->resources = Resource::all();
        $this->applyFilters();
    }
    
    public function fetchLiveMetrics()
    {
        $monitoringService = new MonitoringService();
        $this->liveMetrics = $monitoringService->getSystemMetrics();
    }
    
    /**
     * Llogarit përdorimin aktual për një resurs bazuar në rezervimin e tij dhe metrikat globale
     * 
     * Logjika e re:
     * - Kapaciteti i resursit = rezervim/allocation (jo përdorim aktual)
     * - Përdorimi aktual llogaritet bazuar në metrikat globale dhe rezervimin
     * - Formula: përdorimi = (metrika globale * rezervimi) / 100
     * 
     * Shembull:
     * - Resursi ka rezervuar: CPU 20%, Memory 1000 MB, Storage 50 GB
     * - Sistemi global përdor: CPU 60%, Memory 70%, Disk 80%
     * - Përdorimi i resursit: CPU (60% * 20%)/100 = 12%, Memory (70% * 1000)/100 = 700 MB, etc.
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
        
        // Llogarit përdorimin aktual bazuar në rezervimin dhe metrikat globale
        // Formula: përdorimi = (metrika globale * rezervimi) / 100
        $usage = ($globalMetric * $reserved) / 100;
        
        return round($usage, 1);
    }
    
    public function refreshMetrics()
    {
        $this->fetchLiveMetrics();
        session()->flash('success', 'Metrikat u rifreskuan!');
    }
    
    public function applyFilters()
    {
        $query = Resource::query();
        
        // Apply search filter
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('type', 'like', '%' . $this->search . '%')
                  ->orWhere('region', 'like', '%' . $this->search . '%');
            });
        }
        
        // Apply type filter
        if ($this->selectedFilter !== 'all') {
            $query->where('type', $this->selectedFilter);
        }
        
        $this->filteredResources = $query->get();
    }
    
    public function updatedSearch()
    {
        $this->applyFilters();
    }
    
    public function filterByType($type)
    {
        $this->selectedFilter = $type;
        $this->applyFilters();
    }
    
    public function updatedPerPage()
    {
        $this->applyFilters();
    }

    // Create Modal Methods
    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }
    
    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }
    
    /**
     * Përcakton kapacitetin maksimal të sistemit
     * Këto janë vlera konstante që përcaktojnë sa maksimalisht mund të rezervohet
     */
    private function getSystemMaxCapacity()
    {
        return [
            'cpu' => 100,           // 100% CPU maksimal
            'memory' => 16000,      // 16000 MB (16 GB) memory maksimal
            'storage' => 1000,      // 1000 GB (1 TB) storage maksimal
        ];
    }

    /**
     * Llogarit kapacitetin e lirë në sistem
     * Kapaciteti i lirë = Kapaciteti maksimal - Shuma e të gjitha rezervimeve
     */
    public function getAvailableCapacity()
    {
        $maxCapacity = $this->getSystemMaxCapacity();
        
        // Llogarit shumën e të gjitha rezervimeve aktive
        $totalReserved = [
            'cpu' => Resource::where('status', 'active')->sum('cpu_capacity'),
            'memory' => Resource::where('status', 'active')->sum('memory_capacity'),
            'storage' => Resource::where('status', 'active')->sum('storage_capacity'),
        ];
        
        return [
            'cpu' => max(0, $maxCapacity['cpu'] - $totalReserved['cpu']),
            'memory' => max(0, $maxCapacity['memory'] - $totalReserved['memory']),
            'storage' => max(0, $maxCapacity['storage'] - $totalReserved['storage']),
        ];
    }

    public function create()
    {
        // Ensure user is admin
        if (session('user_role') !== 'admin') {
            abort(403, 'Unauthorized. Admin access required.');
        }
        
        $this->validate([
            'name' => 'required',
            'type' => 'required',
            'cpu_capacity' => 'nullable|integer|min:0|max:100',
            'memory_capacity' => 'nullable|integer|min:0',
            'storage_capacity' => 'nullable|integer|min:0',
            'region' => 'nullable|string',
        ]);

        // Kontrollo kapacitetin e lirë në resource-service (rezervimet)
        $availableCapacity = $this->getAvailableCapacity();
        $requestedCpu = $this->cpu_capacity ?? 0;
        $requestedMemory = $this->memory_capacity ?? 0;
        $requestedStorage = $this->storage_capacity ?? 0;

        // Verifikon nëse ka mjaftueshëm kapacitet të lirë në resource-service
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

        // Nëse ka mjaftueshëm kapacitet, rezervon dhe krijon resursin
        Resource::create([
            'name' => $this->name,
            'type' => $this->type,
            'cpu_capacity' => $requestedCpu,
            'memory_capacity' => $requestedMemory,
            'storage_capacity' => $requestedStorage,
            'region' => $this->region ?? 'eu-west',
            'status' => 'active'
        ]);

        $this->loadResources();
        $this->fetchLiveMetrics();
        $this->closeCreateModal();
        session()->flash('success', 'Resursi u krijua me sukses! Kapaciteti u rezervua.');
    }

    // Edit Modal Methods
    public function openEditModal($id)
    {
        $resource = Resource::findOrFail($id);
        
        $this->resourceId = $resource->id;
        $this->name = $resource->name;
        $this->type = $resource->type;
        $this->status = $resource->status;
        $this->capacity = $resource->capacity;
        $this->cpu_capacity = $resource->cpu_capacity;
        $this->memory_capacity = $resource->memory_capacity;
        $this->storage_capacity = $resource->storage_capacity;
        $this->region = $resource->region;
        
        $this->showEditModal = true;
    }
    
    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }
    
    public function update()
    {
        // Ensure user is admin
        if (session('user_role') !== 'admin') {
            abort(403, 'Unauthorized. Admin access required.');
        }
        
        $this->validate([
            'name' => 'required',
            'type' => 'required',
            'status' => 'required|in:active,inactive',
            'cpu_capacity' => 'nullable|integer|min:0|max:100',
            'memory_capacity' => 'nullable|integer|min:0',
            'storage_capacity' => 'nullable|integer|min:0',
            'region' => 'nullable|string',
        ]);

        $resource = Resource::findOrFail($this->resourceId);
        $resource->update([
            'name' => $this->name,
            'type' => $this->type,
            'status' => $this->status,
            'capacity' => $this->capacity,
            'cpu_capacity' => $this->cpu_capacity,
            'memory_capacity' => $this->memory_capacity,
            'storage_capacity' => $this->storage_capacity,
            'region' => $this->region,
        ]);

        $this->loadResources();
        $this->fetchLiveMetrics();
        $this->closeEditModal();
        session()->flash('success', 'Resursi u përditësua me sukses!');
    }

    // Delete Modal Methods
    public function openDeleteModal($id)
    {
        $resource = Resource::findOrFail($id);
        $this->deleteResourceId = $resource->id;
        $this->deleteResourceName = $resource->name;
        $this->showDeleteModal = true;
    }
    
    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deleteResourceId = null;
        $this->deleteResourceName = null;
    }
    
    public function confirmDelete()
    {
        // Ensure user is admin
        if (session('user_role') !== 'admin') {
            abort(403, 'Unauthorized. Admin access required.');
        }
        
        Resource::findOrFail($this->deleteResourceId)->delete();
        $this->loadResources();
        $this->closeDeleteModal();
        session()->flash('success', 'Resursi u fshi me sukses!');
    }
    
    // Resource Actions
    public function stopResource($id)
    {
        $resource = Resource::findOrFail($id);
        $resource->update(['status' => 'inactive']);
        $this->loadResources();
        session()->flash('success', 'Resursi u ndal me sukses!');
    }
    
    public function restartResource($id)
    {
        $resource = Resource::findOrFail($id);
        $resource->update(['status' => 'active']);
        $this->loadResources();
        session()->flash('success', 'Resursi u rinis me sukses!');
    }
    
    public function openDetailsModal($id)
    {
        $this->selectedResource = Resource::findOrFail($id);
        $this->showDetailsModal = true;
    }
    
    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;
        $this->selectedResource = null;
    }
    
    private function resetForm()
    {
        $this->resourceId = null;
        $this->name = null;
        $this->type = null;
        $this->status = 'active';
        $this->capacity = null;
        $this->cpu_capacity = null;
        $this->memory_capacity = null;
        $this->storage_capacity = null;
        $this->region = null;
        $this->resetErrorBag();
    }

    public function isAdmin()
    {
        return session('user_role') === 'admin';
    }
    
    public function canEdit()
    {
        return session('user_role') === 'admin';
    }

    public function render()
    {
        return view('livewire.resource-list');
    }
}
