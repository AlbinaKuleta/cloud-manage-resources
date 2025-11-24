<x-layouts.app title="Resources">

    <h1 class="text-3xl font-bold mb-6">Resources</h1>

    <a href="/resources/create"
       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded mb-4 inline-block">
       + Create Resource
    </a>

    <table class="min-w-full bg-white rounded shadow mt-4">
        <thead>
            <tr class="bg-gray-200 border-b text-left">
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Name</th>
                <th class="px-4 py-2">Type</th>
                <th class="px-4 py-2">Status</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach($resources as $resource)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-2">{{ $resource->id }}</td>
                    <td class="px-4 py-2">{{ $resource->name }}</td>
                    <td class="px-4 py-2">{{ $resource->type }}</td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-1 rounded bg-green-100 text-green-700">
                            {{ $resource->status }}
                        </span>
                    </td>
                    <td class="px-4 py-2 flex gap-2">
                        <a href="/resources/{{ $resource->id }}/edit"
                           class="bg-yellow-500 text-white px-3 py-1 rounded">Edit</a>

                        <button wire:click="delete({{ $resource->id }})"
                                class="bg-red-600 text-white px-3 py-1 rounded">
                            Delete
                        </button>

                        <a href="/resources/{{ $resource->id }}/metrics"
                           class="bg-green-600 text-white px-3 py-1 rounded">
                           Metrics
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</x-layouts.app>
