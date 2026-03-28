<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('currency_code', 3)->default('MYR')->after('notes');
            $table->decimal('currency_rate', 16, 6)->nullable()->after('currency_code');
            $table->unsignedSmallInteger('currency_unit')->default(1)->after('currency_rate');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['currency_code', 'currency_rate', 'currency_unit']);
        });
    }
};
