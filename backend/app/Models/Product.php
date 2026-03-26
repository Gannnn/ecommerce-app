<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'stock_quantity',
        'is_featured',
    ];

    protected $appends = ['image_url'];

    protected function casts(): array
    {
        return [
            'price'          => 'float',
            'is_featured'    => 'boolean',
            'stock_quantity' => 'integer',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')->singleFile();
    }

    public function getImageUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('images')
            ?: 'https://placehold.co/600x600/f5f5f7/1d1d1f?text=' . urlencode($this->name);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}
