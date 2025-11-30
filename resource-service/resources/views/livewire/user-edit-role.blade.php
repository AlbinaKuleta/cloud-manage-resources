<div>
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Ndrysho Rolin e Përdoruesit</h1>
                <p class="mt-2 text-sm text-gray-600">Përditësoni rolin e përdoruesit në sistem</p>
            </div>
            <a href="{{ route('users') }}" class="text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </a>
        </div>
    </div>

    @if (session('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if ($loadingUser)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <p class="mt-4 text-sm text-gray-600">Duke ngarkuar të dhënat...</p>
        </div>
    @else
        <!-- Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="mb-6 pb-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Informacioni i Përdoruesit</h3>
                <div class="space-y-2">
                    <p class="text-sm text-gray-600">
                        <span class="font-medium">Emri:</span> {{ $userName }}
                    </p>
                    <p class="text-sm text-gray-600">
                        <span class="font-medium">Email:</span> {{ $userEmail }}
                    </p>
                    <p class="text-sm text-gray-600">
                        <span class="font-medium">Roli Aktual:</span>
                        <span class="ml-2 px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border {{ 
                            $currentRole === 'admin' ? 'bg-red-100 text-red-800 border-red-200' : 
                            ($currentRole === 'operator' ? 'bg-blue-100 text-blue-800 border-blue-200' : 
                            'bg-gray-100 text-gray-800 border-gray-200') 
                        }}">
                            {{ strtoupper($currentRole) }}
                        </span>
                    </p>
                </div>
            </div>

            <form wire:submit="updateRole">
                <!-- Role Selection -->
                <div class="mb-6">
                    <label for="newRole" class="block text-sm font-medium text-gray-700 mb-2">
                        Roli i Ri <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="newRole"
                        wire:model="newRole"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('newRole') border-red-500 @enderror"
                    >
                        <option value="viewer">Viewer (Shikues)</option>
                        <option value="operator">Operator</option>
                        <option value="admin">Admin</option>
                    </select>
                    @error('newRole')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500">
                        <strong>Admin:</strong> Akses i plotë në të gjitha funksionalitetet<br>
                        <strong>Operator:</strong> Shikim dhe menaxhim i resurseve (pa krijim/fshirje)<br>
                        <strong>Viewer:</strong> Vetëm shikim (read-only)
                    </p>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('users') }}" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Anulo
                    </a>
                    <button 
                        type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove>Përditëso Rolin</span>
                        <span wire:loading>
                            <span class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>
                            Duke përditësuar...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>


