<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'code',
        'name',
        'symbol',
        'unit',
        'buying_rate',
        'selling_rate',
        'middle_rate',
        'rate_date',
        'source',
        'quote',
        'session',
        'last_updated_at',
        'is_active',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'unit'            => 'integer',
            'buying_rate'     => 'float',
            'selling_rate'    => 'float',
            'middle_rate'     => 'float',
            'rate_date'       => 'date:Y-m-d',
            'last_updated_at' => 'datetime',
            'is_active'       => 'boolean',
            'is_default'      => 'boolean',
        ];
    }
}
