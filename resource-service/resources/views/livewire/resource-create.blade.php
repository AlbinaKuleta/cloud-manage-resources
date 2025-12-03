<div class="p-8 max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Krijo Burim të Ri</h1>

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

    <!-- Kapaciteti i lirë -->
    @php
        $available = $this->getAvailableCapacity();
    @endphp
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-5">
        <h3 class="text-sm font-medium text-gray-700 mb-2">Kapaciteti i lirë në sistem:</h3>
        <div class="flex gap-4 text-sm">
            <div><span class="text-gray-600">CPU:</span> <span class="font-semibold text-gray-900">{{ $available['cpu'] }}%</span></div>
            <div><span class="text-gray-600">Memory:</span> <span class="font-semibold text-gray-900">{{ $available['memory'] }} MB</span></div>
            <div><span class="text-gray-600">Storage:</span> <span class="font-semibold text-gray-900">{{ $available['storage'] }} GB</span></div>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-5 bg-white p-6 rounded-xl shadow">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Emri</label>
                <input type="text" wire:model="name"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Shkruani emrin">
                @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipi</label>
                <select wire:model="type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                    <option value="">Zgjedh...</option>
                    <option value="Instance">Instance</option>
                    <option value="Storage">Storage</option>
                    <option value="Database">Database</option>
                    <option value="API">API</option>
                    <option value="Container">Container</option>
                </select>
                @error('type') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">CPU të Rezervuar (%)</label>
                <input type="number" wire:model="cpu_capacity"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="0"
                       min="0" max="100">
                @error('cpu_capacity') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Memoria e Rezervuar (MB)</label>
                <input type="number" wire:model="memory_capacity"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="0"
                       min="0">
                @error('memory_capacity') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Storage i Rezervuar (GB)</label>
                <input type="number" wire:model="storage_capacity"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="0"
                       min="0">
                @error('storage_capacity') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rajoni</label>
                <input type="text" wire:model="region"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="eu-west, us-east, etj.">
                @error('region') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="flex gap-3 pt-5 mt-5 border-t border-gray-200">
            <a href="/resources" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium px-4 py-2 rounded-md transition-colors text-center">
                Anulo
            </a>
            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-md transition-colors">
                Krijo
            </button>
        </div>
    </form>
</div>
