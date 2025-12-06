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
        Schema::table('product_reviews', function (Blueprint $table) {
            // First, drop foreign keys that depend on the unique constraint
            $table->dropForeign(['product_id']);
            $table->dropForeign(['user_id']);
            
            // Drop old unique constraint (product_id + user_id)
            $table->dropUnique(['product_id', 'user_id']);
            
            // Recreate foreign keys
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Add new unique constraint (product_id + user_id + order_id)
            // This allows user to review same product if bought in different orders
            $table->unique(['product_id', 'user_id', 'order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_reviews', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign(['product_id']);
            $table->dropForeign(['user_id']);
            
            // Drop new unique constraint
            $table->dropUnique(['product_id', 'user_id', 'order_id']);
            
            // Recreate foreign keys
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Restore old unique constraint
            $table->unique(['product_id', 'user_id']);
        });
    }
};
