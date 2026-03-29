<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'subtotal',
        'total',
        'notes',
        'currency_code',
        'currency_rate',
        'currency_unit',
    ];

    public static function generateOrderNumber(): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        do {
            $random = '';
            for ($i = 0; $i < 8; $i++) {
                $random .= $chars[random_int(0, strlen($chars) - 1)];
            }
            $number = date('Ymd') . $random;
        } while (DB::table('orders')->where('order_number', $number)->exists());

        return $number;
    }

    protected function casts(): array
    {
        return [
            'subtotal'      => 'float',
            'total'         => 'float',
            'currency_rate' => 'float',
            'currency_unit' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
