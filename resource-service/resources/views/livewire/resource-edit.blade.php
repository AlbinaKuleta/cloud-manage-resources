<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Edit Resource</h1>

    <form wire:submit.prevent="save" class="space-y-4">

        <div>
            <label class="block font-medium">Name</label>
            <input type="text" wire:model="name" class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block font-medium">Type</label>
            <input type="text" wire:model="type" class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block font-medium">Status</label>
            <select wire:model="status" class="w-full border rounded px-3 py-2">
                <option value="active">active</option>
                <option value="inactive">inactive</option>
            </select>
        </div>

        <button class="bg-green-600 text-white px-3 py-2 rounded">Save</button>

    </form>
</div>
