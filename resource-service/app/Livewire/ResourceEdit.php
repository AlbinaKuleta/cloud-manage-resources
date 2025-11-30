<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Resource;

class ResourceEdit extends Component
{
    public $resourceId;
    public $name;
    public $type;
    public $status;

    public function mount($id)
    {
        // Ensure user is admin
        if (session('user_role') !== 'admin') {
            abort(403, 'Unauthorized. Admin access required.');
        }
        
        $resource = Resource::findOrFail($id);

        $this->resourceId = $resource->id;
        $this->name = $resource->name;
        $this->type = $resource->type;
        $this->status = $resource->status;
    }
    
    public function save()
    {
        // Ensure user is admin
        if (session('user_role') !== 'admin') {
            abort(403, 'Unauthorized. Admin access required.');
        }
        
        $resource = Resource::findOrFail($this->resourceId);

        $resource->update([
            'name' => $this->name,
            'type' => $this->type,
            'status' => $this->status
        ]);

        return redirect('/resources');
    }

    public function render()
    {
        return view('livewire.resource-edit');
    }
}
