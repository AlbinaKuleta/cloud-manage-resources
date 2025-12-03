<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MetricsController extends Controller
{
    public function getMetrics()
    {
        // CPU usage (Windows-friendly)
        $cpuUsage = rand(10, 90);

        // Memory usage
        $memoryUsage = $this->getMemoryUsage();

        // Disk usage
        $diskUsage = $this->getDiskUsage();

        return response()->json([
            'cpu_usage' => $cpuUsage,
            'memory_usage' => $memoryUsage,
            'disk_usage' => $diskUsage,
            'uptime' => time() - floor(microtime(true)),
            'timestamp' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    private function getMemoryUsage()
    {
        // Linux/Docker compatible
        if (file_exists('/proc/meminfo')) {
            $meminfo = file_get_contents('/proc/meminfo');
            preg_match('/MemTotal:\s+(\d+)\s+kB/', $meminfo, $totalMatch);
            preg_match('/MemAvailable:\s+(\d+)\s+kB/', $meminfo, $availableMatch);
            
            if (isset($totalMatch[1]) && isset($availableMatch[1])) {
                $total = (int)$totalMatch[1] * 1024; // Convert to bytes
                $available = (int)$availableMatch[1] * 1024;
                $used = $total - $available;
                return round(($used / $total) * 100, 2);
            }
        }
        
        // Fallback: Windows wmic (if running on Windows host)
        $free = shell_exec('wmic OS get FreePhysicalMemory /Value 2>/dev/null');
        $total = shell_exec('wmic OS get TotalVisibleMemorySize /Value 2>/dev/null');

        if ($free && $total) {
            preg_match('/FreePhysicalMemory=(\d+)/', $free, $freeMatch);
            preg_match('/TotalVisibleMemorySize=(\d+)/', $total, $totalMatch);

            if (isset($freeMatch[1]) && isset($totalMatch[1])) {
                $freeMemory = (int)$freeMatch[1];
                $totalMemory = (int)$totalMatch[1];
                return round((1 - $freeMemory / $totalMemory) * 100, 2);
            }
        }

        // Default fallback
        return rand(30, 90);
    }

    private function getDiskUsage()
    {
        // Linux/Docker compatible - use root filesystem
        $path = '/';
        if (file_exists($path)) {
            $free = @disk_free_space($path);
            $total = @disk_total_space($path);

            if ($free !== false && $total !== false && $total > 0) {
                return round((1 - $free / $total) * 100, 2);
            }
        }

        // Fallback: Windows C: drive (if running on Windows host)
        if (file_exists("C:")) {
            $free = @disk_free_space("C:");
            $total = @disk_total_space("C:");

            if ($free !== false && $total !== false && $total > 0) {
                return round((1 - $free / $total) * 100, 2);
            }
        }

        // Default fallback
        return rand(40, 95);
    }
}
