<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name', 100);
            $table->string('symbol', 10)->nullable();
            $table->unsignedInteger('unit')->default(1);
            $table->decimal('buying_rate', 15, 8)->nullable();
            $table->decimal('selling_rate', 15, 8)->nullable();
            $table->decimal('middle_rate', 15, 8)->nullable();
            $table->date('rate_date')->nullable();
            $table->string('source', 50)->nullable();
            $table->string('quote', 10)->nullable();
            $table->string('session', 20)->nullable();
            $table->datetime('last_updated_at')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
