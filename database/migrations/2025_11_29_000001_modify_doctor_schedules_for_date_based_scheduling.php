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
        // Check if schedule_date column doesn't exist yet
        if (!Schema::hasColumn('doctor_schedules', 'schedule_date')) {
            Schema::table('doctor_schedules', function (Blueprint $table) {
                $table->date('schedule_date')->after('doctor_id')->nullable();
            });
        }

        // Check if day_of_week column still exists
        if (Schema::hasColumn('doctor_schedules', 'day_of_week')) {
            // Remove the old column using raw SQL to handle index automatically
            DB::statement('ALTER TABLE doctor_schedules DROP COLUMN day_of_week');
        }

        // Add new index if it doesn't exist
        $indexes = DB::select("SHOW INDEX FROM doctor_schedules WHERE Key_name = 'doctor_schedules_doctor_id_schedule_date_is_active_index'");
        if (empty($indexes)) {
            Schema::table('doctor_schedules', function (Blueprint $table) {
                $table->index(['doctor_id', 'schedule_date', 'is_active']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new index if exists
        $indexes = DB::select("SHOW INDEX FROM doctor_schedules WHERE Key_name = 'doctor_schedules_doctor_id_schedule_date_is_active_index'");
        if (!empty($indexes)) {
            Schema::table('doctor_schedules', function (Blueprint $table) {
                $table->dropIndex(['doctor_id', 'schedule_date', 'is_active']);
            });
        }

        // Add back day_of_week column if it doesn't exist
        if (!Schema::hasColumn('doctor_schedules', 'day_of_week')) {
            Schema::table('doctor_schedules', function (Blueprint $table) {
                $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])->after('doctor_id')->nullable();
            });
        }

        // Drop schedule_date column if exists
        if (Schema::hasColumn('doctor_schedules', 'schedule_date')) {
            Schema::table('doctor_schedules', function (Blueprint $table) {
                $table->dropColumn('schedule_date');
            });
        }

        // Restore old index
        $indexes = DB::select("SHOW INDEX FROM doctor_schedules WHERE Key_name = 'doctor_schedules_doctor_id_day_of_week_is_active_index'");
        if (empty($indexes)) {
            Schema::table('doctor_schedules', function (Blueprint $table) {
                $table->index(['doctor_id', 'day_of_week', 'is_active']);
            });
        }
    }
};
