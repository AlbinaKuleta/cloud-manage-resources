<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class MetricsComponent extends Component
{
    public $labels = [];
    public $cpu = [];
    public $memory = [];
    public $disk = [];

    public function fetchMetrics()
    {
        $response = Http::get('http://127.0.0.1:8002/api/metrics');

        if ($response->successful()) {

            $data = $response->json();

            $this->labels[] = now()->format('H:i:s');
            $this->cpu[] = $data['cpu_usage'];
            $this->memory[] = $data['memory_usage'];
            $this->disk[] = $data['disk_usage'];

            $this->dispatch(
                'updateChart',
                labels: $this->labels,
                cpu: $this->cpu,
                memory: $this->memory,
                disk: $this->disk
            );
        }
    }

    public function render()
    {
        return view('livewire.metrics-component');
    }
}
