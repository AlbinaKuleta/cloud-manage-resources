<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Create Resource</h1>

    <form wire:submit.prevent="save" class="space-y-4">

        <div>
            <label class="block font-medium">Name</label>
            <input type="text" wire:model="name" class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block font-medium">Type</label>
            <input type="text" wire:model="type" class="w-full border rounded px-3 py-2">
        </div>

        <button class="bg-blue-600 text-white px-3 py-2 rounded">Create</button>

    </form>
</div>
