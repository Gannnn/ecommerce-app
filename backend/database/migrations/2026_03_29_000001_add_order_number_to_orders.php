<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_number', 16)->nullable()->unique()->after('id');
        });

        // Backfill existing orders
        $orders = DB::table('orders')->whereNull('order_number')->get(['id', 'created_at']);
        foreach ($orders as $order) {
            $date = date('Ymd', strtotime($order->created_at));
            do {
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                $random = '';
                for ($i = 0; $i < 8; $i++) {
                    $random .= $chars[random_int(0, strlen($chars) - 1)];
                }
                $number = $date . $random;
            } while (DB::table('orders')->where('order_number', $number)->exists());

            DB::table('orders')->where('id', $order->id)->update(['order_number' => $number]);
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_number', 16)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['order_number']);
            $table->dropColumn('order_number');
        });
    }
};
