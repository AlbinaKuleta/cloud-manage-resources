<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Resource;

class ResourceCreate extends Component
{
    public $name;
    public $type;

    public function save()
    {
        $this->validate([
            'name' => 'required',
            'type' => 'required'
        ]);

        Resource::create([
            'name' => $this->name,
            'type' => $this->type
        ]);

        return redirect('/resources');
    }

    public function render()
    {
        return view('livewire.resource-create');
    }
}
