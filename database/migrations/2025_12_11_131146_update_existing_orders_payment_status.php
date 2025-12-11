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
        // Update orders with status 'pending' and NULL or empty payment_status to 'unpaid'
        DB::table('orders')
            ->where('status', 'pending')
            ->whereNull('payment_status')
            ->update(['payment_status' => 'unpaid']);
        
        // Update orders with status other than 'pending' and NULL payment_status to 'paid'
        DB::table('orders')
            ->where('status', '!=', 'pending')
            ->where('status', '!=', 'cancelled')
            ->whereNull('payment_status')
            ->update(['payment_status' => 'paid']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse this data migration
    }
};
