<div class="p-8 max-w-xl mx-auto">

    <h1 class="text-3xl font-bold mb-6 text-gray-800">Create Resource</h1>

    @if (session('error'))
        <div class="bg-red-500 text-white px-4 py-3 rounded mb-4 shadow">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="bg-green-500 text-white px-4 py-3 rounded mb-4 shadow">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-5 bg-white p-6 rounded-xl shadow">

        <div>
            <label class="block font-semibold text-gray-700 mb-1">Name</label>
            <input type="text" wire:model="name"
                   class="w-full border-gray-300 rounded-lg px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block font-semibold text-gray-700 mb-1">Type</label>
            <input type="text" wire:model="type"
                   class="w-full border-gray-300 rounded-lg px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            @error('type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block font-semibold text-gray-700 mb-1">Capacity</label>
            <input type="number" wire:model="capacity"
                   class="w-full border-gray-300 rounded-lg px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            @error('capacity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg shadow">
            Create Resource
        </button>

    </form>

</div>
