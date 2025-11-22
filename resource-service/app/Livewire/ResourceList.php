<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Resource;

class ResourceList extends Component
{
    public $resources;

    public function mount()
    {
        $this->resources = Resource::all();
    }

    public function delete($id)
    {
        Resource::findOrFail($id)->delete();
        $this->resources = Resource::all();
    }

    public function render()
    {
        return view('livewire.resource-list');
    }
}
