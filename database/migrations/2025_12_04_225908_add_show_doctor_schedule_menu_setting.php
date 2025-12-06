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
        // Insert default setting for showing doctor schedule menu
        DB::table('settings')->insert([
            'key' => 'show_doctor_schedule_menu',
            'value' => '1', // Default: show menu
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->where('key', 'show_doctor_schedule_menu')->delete();
    }
};
