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
        Schema::create('scaling_history', function (Blueprint $table) {
            $table->id();
            $table->string('action'); // 'scale_up' ose 'scale_down'
            $table->unsignedBigInteger('resource_id')->nullable();
            $table->string('resource_name');
            $table->text('metrics')->nullable(); // JSON me metrikat
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scaling_history');
    }
};

