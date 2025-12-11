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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('delivery_type')->nullable()->after('shipping_postal_code'); // 'delivery' or 'pickup'
            $table->string('delivery_option')->nullable()->after('delivery_type'); // 'instant', 'scheduled'
            $table->date('delivery_date')->nullable()->after('delivery_option');
            $table->string('delivery_time')->nullable()->after('delivery_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['delivery_type', 'delivery_option', 'delivery_date', 'delivery_time']);
        });
    }
};
