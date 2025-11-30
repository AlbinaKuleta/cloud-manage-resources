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
        Schema::table('resources', function (Blueprint $table) {
            $table->integer('cpu_usage')->nullable()->default(0);
            $table->integer('memory_usage')->nullable()->default(0);
            $table->integer('storage')->nullable()->default(0);
            $table->string('region')->nullable()->default('eu-west');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->dropColumn(['cpu_usage', 'memory_usage', 'storage', 'region']);
        });
    }
};
