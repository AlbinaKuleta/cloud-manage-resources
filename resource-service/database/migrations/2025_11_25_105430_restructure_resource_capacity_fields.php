<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL for safer column operations
        if (Schema::hasColumn('resources', 'cpu_usage')) {
            DB::statement('ALTER TABLE resources CHANGE cpu_usage cpu_capacity INTEGER NULL DEFAULT 0');
        } else if (!Schema::hasColumn('resources', 'cpu_capacity')) {
            Schema::table('resources', function (Blueprint $table) {
                $table->integer('cpu_capacity')->nullable()->default(0)->after('capacity');
            });
        }
        
        if (Schema::hasColumn('resources', 'memory_usage')) {
            DB::statement('ALTER TABLE resources CHANGE memory_usage memory_capacity INTEGER NULL DEFAULT 0');
        } else if (!Schema::hasColumn('resources', 'memory_capacity')) {
            Schema::table('resources', function (Blueprint $table) {
                $table->integer('memory_capacity')->nullable()->default(0)->after('cpu_capacity');
            });
        }
        
        if (Schema::hasColumn('resources', 'storage')) {
            DB::statement('ALTER TABLE resources CHANGE storage storage_capacity INTEGER NULL DEFAULT 0');
        } else if (!Schema::hasColumn('resources', 'storage_capacity')) {
            Schema::table('resources', function (Blueprint $table) {
                $table->integer('storage_capacity')->nullable()->default(0)->after('memory_capacity');
            });
        }
        
        if (!Schema::hasColumn('resources', 'region')) {
            Schema::table('resources', function (Blueprint $table) {
                $table->string('region')->nullable()->default('eu-west')->after('storage_capacity');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('resources', 'cpu_capacity')) {
            DB::statement('ALTER TABLE resources CHANGE cpu_capacity cpu_usage INTEGER NULL DEFAULT 0');
        }
        
        if (Schema::hasColumn('resources', 'memory_capacity')) {
            DB::statement('ALTER TABLE resources CHANGE memory_capacity memory_usage INTEGER NULL DEFAULT 0');
        }
        
        if (Schema::hasColumn('resources', 'storage_capacity')) {
            DB::statement('ALTER TABLE resources CHANGE storage_capacity storage INTEGER NULL DEFAULT 0');
        }
    }
};
