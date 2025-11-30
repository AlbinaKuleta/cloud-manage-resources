<div>
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Profili im</h1>
                <p class="mt-2 text-sm text-gray-600">Shikoni informacionet dhe aktivitetet tuaja</p>
            </div>
            <a href="/dashboard" class="text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- User Info Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="text-center">
                    <div class="flex justify-center mb-4">
                        <div class="w-24 h-24 bg-blue-600 rounded-full flex items-center justify-center shadow-lg">
                            <span class="text-white text-3xl font-bold">{{ strtoupper(substr($userName, 0, 1)) }}</span>
                        </div>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ $userName }}</h2>
                    <p class="text-gray-600 mb-4">{{ $userEmail }}</p>
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full border {{ $this->getRoleBadgeClass() }}">
                        {{ strtoupper($userRole) }}
                    </span>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">ID:</span>
                        <span class="text-sm font-medium text-gray-900">#{{ $userId }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Roli:</span>
                        <span class="text-sm font-medium text-gray-900">{{ ucfirst($userRole) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Logs Card -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-900">Activity Logs</h3>
                    <span class="text-sm text-gray-500">{{ count($activityLogs) }} aktivitete</span>
                </div>
                
                <div class="space-y-3 max-h-[600px] overflow-y-auto">
                    @forelse($activityLogs as $index => $log)
                    <div class="bg-gray-50 rounded-lg p-4 border-l-4 {{ 
                        $log['action'] === 'Login' ? 'border-green-500' : 
                        (str_contains($log['action'], 'View Dashboard') ? 'border-blue-500' : 
                        (str_contains($log['action'], 'View') ? 'border-indigo-500' : 
                        'border-gray-400')) 
                    }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-1">
                                    <span class="text-sm font-semibold text-gray-900">{{ $log['action'] }}</span>
                                    <span class="text-xs text-gray-500">•</span>
                                    <span class="text-xs text-gray-500">
                                        @if(isset($log['time']) && $log['time'] instanceof \Carbon\Carbon)
                                            {{ $log['time']->format('d/m/Y H:i') }}
                                        @elseif(isset($log['time']))
                                            {{ \Carbon\Carbon::parse($log['time'])->format('d/m/Y H:i') }}
                                        @else
                                            {{ now()->format('d/m/Y H:i') }}
                                        @endif
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600">{{ $log['description'] }}</p>
                                @if(isset($log['ip']))
                                <p class="text-xs text-gray-400 mt-1">IP: {{ $log['ip'] }}</p>
                                @endif
                            </div>
                            <div class="ml-4">
                                @if($log['action'] === 'Login')
                                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @elseif(str_contains($log['action'], 'View'))
                                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="bg-gray-50 rounded-lg p-12 text-center">
                        <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Nuk ka aktivitete</h3>
                        <p class="mt-2 text-sm text-gray-500">Aktivitete tuaja do të shfaqen këtu.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

