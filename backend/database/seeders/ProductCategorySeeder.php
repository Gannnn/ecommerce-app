<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'iPhone',      'description' => 'The world\'s most powerful smartphones.',        'sort_order' => 1],
            ['name' => 'Mac',         'description' => 'Powerful computers for every kind of user.',     'sort_order' => 2],
            ['name' => 'iPad',        'description' => 'Versatile tablets for work and play.',           'sort_order' => 3],
            ['name' => 'AirPods',     'description' => 'Wireless audio reimagined.',                     'sort_order' => 4],
            ['name' => 'Apple Watch', 'description' => 'The ultimate device for a healthy life.',        'sort_order' => 5],
            ['name' => 'Accessories', 'description' => 'Essential accessories for your Apple devices.',  'sort_order' => 6],
        ];

        foreach ($categories as $cat) {
            ProductCategory::create(array_merge($cat, [
                'slug' => Str::slug($cat['name']),
            ]));
        }
    }
}
