<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('metrics_history', function (Blueprint $table) {
            $table->id();
            $table->decimal('cpu_usage', 5, 2)->default(0);
            $table->decimal('memory_usage', 5, 2)->default(0);
            $table->decimal('disk_usage', 5, 2)->default(0);
            $table->timestamp('recorded_at');
            $table->timestamps();
            
            $table->index('recorded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metrics_history');
    }
};

