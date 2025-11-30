<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\MonitoringService;

class ServiceCommunication extends Component
{
    public $status = [];
    public $metrics = null;

    public function mount()
    {
        $this->loadStatus();
    }

    public function loadStatus()
    {
        $monitoringService = new MonitoringService();
        $this->status = $monitoringService->getServiceStatus();
        $this->metrics = $monitoringService->getSystemMetrics();
    }

    public function refresh()
    {
        $this->loadStatus();
        session()->flash('success', 'Statusi u rifreskuan!');
    }

    public function render()
    {
        return view('livewire.service-communication');
    }
}

