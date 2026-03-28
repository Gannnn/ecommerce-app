<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // Drop the old session-based unique constraint
            $table->dropUnique('cart_items_session_id_product_id_unique');
            // Make session_id nullable — authenticated users don't need it
            $table->string('session_id')->nullable()->change();
            // Enforce uniqueness per user (MySQL allows multiple NULLs, so guests are unaffected)
            $table->unique(['user_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'product_id']);
            $table->string('session_id')->nullable(false)->change();
            $table->unique(['session_id', 'product_id']);
        });
    }
};
