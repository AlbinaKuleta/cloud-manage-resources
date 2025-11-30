<div>
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">RBAC Testing</h1>
                <p class="mt-2 text-sm text-gray-600">Testoni funksionalitetin e sistemit RBAC</p>
            </div>
            <button wire:click="runAllTests" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Ekzekuto Të Gjitha Testet
            </button>
        </div>
    </div>

    <!-- User Info Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Informacioni i Përdoruesit Aktual</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600">ID</p>
                <p class="text-lg font-semibold text-gray-900">{{ $userInfo['id'] ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Emri</p>
                <p class="text-lg font-semibold text-gray-900">{{ $userInfo['name'] ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Email</p>
                <p class="text-lg font-semibold text-gray-900">{{ $userInfo['email'] ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Roli</p>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold border {{ $this->getRoleBadgeClass($userInfo['role'] ?? 'viewer') }}">
                    {{ strtoupper($userInfo['role'] ?? 'VIEWER') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Test Results -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Token Validation Test -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Token Validation</h3>
                <button wire:click="testTokenValidation" class="text-blue-600 hover:text-blue-800 text-sm">
                    Test
                </button>
            </div>
            <div class="space-y-2">
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full {{ 
                        $tokenStatus === 'valid' ? 'bg-green-500' : 
                        ($tokenStatus === 'invalid' ? 'bg-red-500' : 
                        ($tokenStatus === 'testing' ? 'bg-yellow-500 animate-pulse' : 'bg-gray-400')) 
                    }}"></span>
                    <span class="text-sm text-gray-600">Status: {{ ucfirst($tokenStatus) }}</span>
                </div>
                @if(isset($testResults['token_validation']))
                    <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-600 mb-2">Result:</p>
                        <pre class="text-xs text-gray-800 overflow-auto">{{ json_encode($testResults['token_validation'], JSON_PRETTY_PRINT) }}</pre>
                    </div>
                @endif
            </div>
        </div>

        <!-- Role Detection Test -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Role Detection</h3>
                <button wire:click="testRoleDetection" class="text-blue-600 hover:text-blue-800 text-sm">
                    Test
                </button>
            </div>
            <div class="space-y-2">
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                    <span class="text-sm text-gray-600">Session Role: {{ session('user_role', 'not_set') }}</span>
                </div>
                @if(isset($testResults['role_detection']))
                    <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-600 mb-2">Result:</p>
                        <pre class="text-xs text-gray-800 overflow-auto">{{ json_encode($testResults['role_detection'], JSON_PRETTY_PRINT) }}</pre>
                    </div>
                @endif
            </div>
        </div>

        <!-- Cross-Service Communication Test -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Service Communication</h3>
                <button wire:click="testCrossServiceCommunication" class="text-blue-600 hover:text-blue-800 text-sm">
                    Test
                </button>
            </div>
            <div class="space-y-2">
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full {{ 
                        $authServiceStatus === 'connected' ? 'bg-green-500' : 
                        ($authServiceStatus === 'disconnected' ? 'bg-red-500' : 
                        ($authServiceStatus === 'error' ? 'bg-red-500' : 'bg-gray-400')) 
                    }}"></span>
                    <span class="text-sm text-gray-600">Auth Service: {{ ucfirst($authServiceStatus) }}</span>
                </div>
                @if(isset($testResults['cross_service']))
                    <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-600 mb-2">Result:</p>
                        <pre class="text-xs text-gray-800 overflow-auto">{{ json_encode($testResults['cross_service'], JSON_PRETTY_PRINT) }}</pre>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Session Data -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Session Data</h2>
        <div class="bg-gray-50 rounded-lg p-4">
            <pre class="text-xs text-gray-800 overflow-auto">{{ json_encode([
                'user_id' => session('user_id'),
                'user_name' => session('user_name'),
                'user_email' => session('user_email'),
                'user_role' => session('user_role'),
                'has_token' => session()->has('token'),
            ], JSON_PRETTY_PRINT) }}</pre>
        </div>
    </div>
</div>


