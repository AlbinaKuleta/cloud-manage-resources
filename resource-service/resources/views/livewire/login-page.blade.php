<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-8 rounded shadow w-full max-w-md">

        <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>

        @if($errorMessage)
            <div class="bg-red-200 text-red-800 px-4 py-2 rounded mb-4">
                {{ $errorMessage }}
            </div>
        @endif

        <form wire:submit.prevent="login" class="space-y-4">

            <div>
                <label>Email</label>
                <input type="email" wire:model="email" class="w-full px-3 py-2 border rounded">
            </div>

            <div>
                <label>Password</label>
                <input type="password" wire:model="password" class="w-full px-3 py-2 border rounded">
            </div>

            <button class="w-full bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded">
                Login
            </button>

        </form>
    </div>
</div>
