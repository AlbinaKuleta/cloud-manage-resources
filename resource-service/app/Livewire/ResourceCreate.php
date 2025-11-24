<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Resource;
use Illuminate\Support\Facades\Http;

class ResourceCreate extends Component
{
    public $name;
    public $type;
    public $capacity;

    public function save()
    {
        $this->validate([
            'name'     => 'required',
            'type'     => 'required',
            'capacity' => 'required|integer|min:1|max:1000',
        ]);

        // Marrim metrikat nga monitoring-service
        $response = Http::timeout(5)->get('http://127.0.0.1:8002/api/metrics');

        if ($response->failed()) {
            session()->flash('error', 'Nuk u arrit lidhja me Monitoring-Service!');
            return;
        }

        $metrics = $response->json();

        if ($metrics['cpu_usage'] > 85 ||
            $metrics['memory_usage'] > 90 ||
            $metrics['disk_usage'] > 95) {

            session()->flash('error', 'Sistemi është shumë i ngarkuar për krijimin e resurseve. Provoni më vonë!');
            return;
        }

        Resource::create([
            'name'     => $this->name,
            'type'     => $this->type,
            'capacity' => $this->capacity
        ]);

        session()->flash('success', 'Resursi u krijua me sukses!');
        return redirect('/resources');
    }

    public function render()
    {
        return view('livewire.resource-create');
    }
}
