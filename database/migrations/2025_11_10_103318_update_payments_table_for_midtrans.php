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
        Schema::table('payments', function (Blueprint $table) {
            // Drop old Tripay columns
            $table->dropColumn([
                'tripay_reference',
                'tripay_merchant_ref',
                'payment_code',
                'qr_url',
                'checkout_url',
                'tripay_response',
            ]);

            // Add Midtrans columns
            $table->string('midtrans_order_id')->nullable()->after('order_id');
            $table->string('midtrans_transaction_id')->nullable()->after('midtrans_order_id');
            $table->string('midtrans_payment_type')->nullable()->after('payment_channel');
            $table->string('snap_token')->nullable()->after('midtrans_payment_type');
            $table->string('snap_redirect_url')->nullable()->after('snap_token');
            $table->string('transaction_status')->nullable()->after('status');
            $table->string('fraud_status')->nullable()->after('transaction_status');
            $table->text('midtrans_response')->nullable()->after('fraud_status');
            $table->string('bank')->nullable()->after('payment_name');
            $table->string('va_number')->nullable()->after('bank');
            $table->text('bill_key')->nullable()->after('va_number');
            $table->text('biller_code')->nullable()->after('bill_key');
            $table->text('pdf_url')->nullable()->after('biller_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Drop Midtrans columns
            $table->dropColumn([
                'midtrans_order_id',
                'midtrans_transaction_id',
                'midtrans_payment_type',
                'snap_token',
                'snap_redirect_url',
                'transaction_status',
                'fraud_status',
                'midtrans_response',
                'bank',
                'va_number',
                'bill_key',
                'biller_code',
                'pdf_url',
            ]);

            // Restore old Tripay columns
            $table->string('tripay_reference')->nullable()->after('order_id');
            $table->string('tripay_merchant_ref')->nullable()->after('tripay_reference');
            $table->string('payment_code')->nullable()->after('status');
            $table->text('qr_url')->nullable()->after('payment_code');
            $table->text('checkout_url')->nullable()->after('qr_url');
            $table->json('tripay_response')->nullable()->after('paid_at');
        });
    }
};
