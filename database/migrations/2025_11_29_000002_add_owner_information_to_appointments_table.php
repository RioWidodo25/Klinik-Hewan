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
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('owner_name')->after('complaint')->nullable();
            $table->string('owner_phone', 20)->after('owner_name')->nullable();
            $table->text('owner_address')->after('owner_phone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['owner_name', 'owner_phone', 'owner_address']);
        });
    }
};
