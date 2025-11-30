<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MonitoringService;
use App\Services\ScalingService;
use Illuminate\Support\Facades\Log;

/**
 * MonitorMetrics - Monitoron metrikat dhe vendos për scaling
 */
class MonitorMetrics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autoscaler:monitor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitoron metrikat e sistemit dhe vendos për scaling';

    private $monitoringService;
    private $scalingService;

    public function __construct(MonitoringService $monitoringService, ScalingService $scalingService)
    {
        parent::__construct();
        $this->monitoringService = $monitoringService;
        $this->scalingService = $scalingService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!config('autoscaler.scaling.enabled', true)) {
            $this->info('Autoscaler është i çaktivizuar');
            return 0;
        }

        $this->info('Duke monitoruar metrikat...');

        // Analizo metrikat
        $result = $this->scalingService->analyzeMetrics();

        if ($result) {
            $this->info("Vendim scaling: {$result['action']} - {$result['reason']}");
            
            if ($result['action'] === 'scale_up') {
                $this->info('Duke ekzekutuar scale up...');
                $success = $this->scalingService->executeScaleUp();
                
                if ($success) {
                    $this->info('Scale up u ekzekutua me sukses!');
                } else {
                    $this->error('Scale up dështoi!');
                }
            } elseif ($result['action'] === 'scale_down') {
                $this->info('Duke ekzekutuar scale down...');
                $success = $this->scalingService->executeScaleDown();
                
                if ($success) {
                    $this->info('Scale down u ekzekutua me sukses!');
                } else {
                    $this->error('Scale down dështoi!');
                }
            }
        } else {
            $this->info('Nuk ka nevojë për scaling në këtë moment');
        }

        return 0;
    }
}

