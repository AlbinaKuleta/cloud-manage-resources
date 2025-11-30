<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Cloud Manager</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
    .bg-grid-pattern {
        background-image: 
            linear-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
        background-size: 50px 50px;
    }
    </style>
</head>

<body class="min-h-screen flex flex-col relative overflow-hidden">
    <!-- Dark Background matching sidebar -->
    <div class="absolute inset-0 bg-gray-800">
        <!-- Subtle Pattern -->
        <div class="absolute inset-0 bg-grid-pattern opacity-5"></div>
    </div>
    
    <!-- Main Content -->
    <main class="flex-1 flex items-center justify-center relative z-10 px-4 py-12">
        {{ $slot }}
    </main>

    <!-- Footer - Fixed at bottom -->
    <footer class="bg-gray-900 border-t border-gray-700 py-3 mt-auto relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-center">
                <p class="text-sm text-gray-400 mb-2 sm:mb-0">
                    © 2025 Cloud Manager. Të gjitha të drejtat e rezervuara.
                </p>
                <div class="text-sm text-gray-400">
                    <span>Version 1.0.0</span>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>

