<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $cat = fn(string $name) => ProductCategory::where('slug', Str::slug($name))->value('id');
        $imgs = public_path('imgs');

        $products = [
            // ── iPhone ──────────────────────────────────────────────────────
            [
                'category'    => 'iPhone',
                'name'        => 'iPhone 16 Pro Max',
                'description' => 'The most advanced iPhone ever. A18 Pro chip, titanium design, 48 MP camera system with 5× optical zoom, and a massive 6.9-inch Super Retina XDR display with ProMotion.',
                'price'       => 1599.00,
                'image'       => 'iphone-16-pro-max',
                'stock'       => 50,
                'featured'    => true,
            ],
            [
                'category'    => 'iPhone',
                'name'        => 'iPhone 16 Pro',
                'description' => 'Pro performance in a refined 6.3-inch form. A18 Pro chip, Pro camera system with 48 MP Fusion camera and 5× optical zoom, and Camera Control.',
                'price'       => 1199.00,
                'image'       => 'iphone-16-pro',
                'stock'       => 60,
                'featured'    => true,
            ],
            [
                'category'    => 'iPhone',
                'name'        => 'iPhone 16 Plus',
                'description' => 'Big and beautiful. The 6.7-inch iPhone 16 Plus features the A18 chip, 48 MP Fusion camera, Camera Control, and all-day battery life.',
                'price'       => 1099.00,
                'image'       => 'iphone-16-plus',
                'stock'       => 70,
                'featured'    => false,
            ],
            [
                'category'    => 'iPhone',
                'name'        => 'iPhone 16',
                'description' => 'Meet iPhone 16. A18 chip, 48 MP Fusion camera, Camera Control, and a gorgeous 6.1-inch Super Retina XDR display — all in a beautiful aluminium design.',
                'price'       => 999.00,
                'image'       => 'iphone-16',
                'stock'       => 80,
                'featured'    => true,
            ],
            [
                'category'    => 'iPhone',
                'name'        => 'iPhone 15',
                'description' => 'iPhone 15 with Dynamic Island, 48 MP Main camera, USB-C, and an all-day battery in a durable colour-infused glass and aluminium design.',
                'price'       => 799.00,
                'image'       => 'iphone-15',
                'stock'       => 45,
                'featured'    => false,
            ],

            // ── Mac ─────────────────────────────────────────────────────────
            [
                'category'    => 'Mac',
                'name'        => 'MacBook Pro 16" M4 Max',
                'description' => 'The most powerful MacBook Pro ever. M4 Max chip with up to 16-core CPU and 40-core GPU, Liquid Retina XDR display, and up to 24 hours battery life.',
                'price'       => 3999.00,
                'image'       => 'macbook-pro-16-m4-max',
                'stock'       => 15,
                'featured'    => true,
            ],
            [
                'category'    => 'Mac',
                'name'        => 'MacBook Pro 16" M4 Pro',
                'description' => 'Supercharged by M4 Pro. Blazing performance, a stunning Liquid Retina XDR display, up to 24 hours battery life — in Space Black or Silver.',
                'price'       => 2499.00,
                'image'       => 'macbook-pro-16-m4-pro',
                'stock'       => 20,
                'featured'    => false,
            ],
            [
                'category'    => 'Mac',
                'name'        => 'MacBook Pro 14" M4',
                'description' => 'Pro performance in a compact 14-inch design. M4 chip, Liquid Retina XDR display, up to 24 hours battery, and Thunderbolt 5 ports.',
                'price'       => 1599.00,
                'image'       => 'macbook-pro-14-m4',
                'stock'       => 25,
                'featured'    => false,
            ],
            [
                'category'    => 'Mac',
                'name'        => 'MacBook Air 15" M3',
                'description' => 'The world\'s best 15-inch laptop. Impossibly thin and light with M3 chip, 18-hour battery, a stunning Liquid Retina display, and MagSafe charging.',
                'price'       => 1499.00,
                'image'       => 'macbook-air-15-m3',
                'stock'       => 30,
                'featured'    => true,
            ],
            [
                'category'    => 'Mac',
                'name'        => 'MacBook Air 13" M3',
                'description' => 'Strikingly thin. Incredibly capable. MacBook Air with M3 — the world\'s best consumer laptop with 18-hour battery life and fanless design.',
                'price'       => 1299.00,
                'image'       => 'macbook-air-13-m3',
                'stock'       => 40,
                'featured'    => false,
            ],
            [
                'category'    => 'Mac',
                'name'        => 'iMac 24" M4',
                'description' => 'Say hello to iMac. Powered by M4, a brilliant 24-inch 4.5K Retina display, 12 MP Centre Stage camera, and an incredibly thin design in seven colours.',
                'price'       => 1299.00,
                'image'       => 'imac-24-m4',
                'stock'       => 18,
                'featured'    => false,
            ],
            [
                'category'    => 'Mac',
                'name'        => 'Mac mini M4',
                'description' => 'The most popular Mac gets even more powerful. Mac mini with M4 chip is remarkably small at just 5 inches square — and starts from an approachable price.',
                'price'       => 699.00,
                'image'       => 'mac-mini-m4',
                'stock'       => 35,
                'featured'    => false,
            ],

            // ── iPad ─────────────────────────────────────────────────────────
            [
                'category'    => 'iPad',
                'name'        => 'iPad Pro 13" M4',
                'description' => 'Unbelievably thin. Incredibly powerful. iPad Pro with M4 chip and Ultra Retina XDR OLED display is the thinnest Apple product ever made.',
                'price'       => 1299.00,
                'image'       => 'ipad-pro-13-m4',
                'stock'       => 25,
                'featured'    => false,
            ],
            [
                'category'    => 'iPad',
                'name'        => 'iPad Air 13" M2',
                'description' => 'Serious performance in a thin, light design. iPad Air with M2 chip, a 13-inch Liquid Retina display, and Apple Pencil Pro support.',
                'price'       => 1099.00,
                'image'       => 'ipad-air-13-m2',
                'stock'       => 30,
                'featured'    => false,
            ],
            [
                'category'    => 'iPad',
                'name'        => 'iPad mini A17 Pro',
                'description' => 'Powerful. Portable. Playful. iPad mini with A17 Pro chip, 8.3-inch Liquid Retina display, Apple Intelligence, and Apple Pencil Pro support.',
                'price'       => 499.00,
                'image'       => 'ipad-mini-a17-pro',
                'stock'       => 50,
                'featured'    => false,
            ],

            // ── AirPods ──────────────────────────────────────────────────────
            [
                'category'    => 'AirPods',
                'name'        => 'AirPods Pro (2nd generation)',
                'description' => 'Up to 2× more Active Noise Cancellation, Transparency mode, Adaptive Audio, and personalised Spatial Audio. Now with a hearing health feature.',
                'price'       => 249.00,
                'image'       => 'airpods-pro-2',
                'stock'       => 100,
                'featured'    => true,
            ],
            [
                'category'    => 'AirPods',
                'name'        => 'AirPods 4',
                'description' => 'Redesigned for a better fit. AirPods 4 with Active Noise Cancellation delivers a magical audio experience with personalized Spatial Audio and Siri.',
                'price'       => 179.00,
                'image'       => 'airpods-4',
                'stock'       => 90,
                'featured'    => false,
            ],
            [
                'category'    => 'AirPods',
                'name'        => 'AirPods Max',
                'description' => 'A perfect balance of exquisite design and high-fidelity audio. AirPods Max with H2 chip, USB-C, and industry-leading Active Noise Cancellation.',
                'price'       => 549.00,
                'image'       => 'airpods-max',
                'stock'       => 40,
                'featured'    => false,
            ],

            // ── Apple Watch ──────────────────────────────────────────────────
            [
                'category'    => 'Apple Watch',
                'name'        => 'Apple Watch Series 10',
                'description' => 'The thinnest Apple Watch ever with the largest display. Series 10 features faster charging, sleep apnoea detection, and advanced health sensors.',
                'price'       => 399.00,
                'image'       => 'apple-watch-series-10',
                'stock'       => 55,
                'featured'    => true,
            ],
            [
                'category'    => 'Apple Watch',
                'name'        => 'Apple Watch Ultra 2',
                'description' => 'The most rugged and capable Apple Watch. Built for endurance athletes and adventurers with a 49mm titanium case, up to 36-hour battery, and precision dual-frequency GPS.',
                'price'       => 799.00,
                'image'       => 'apple-watch-ultra-2',
                'stock'       => 20,
                'featured'    => false,
            ],
            [
                'category'    => 'Apple Watch',
                'name'        => 'Apple Watch SE',
                'description' => 'An incredible smartwatch at an incredible value. Apple Watch SE with S9 chip, fall detection, crash detection, and Emergency SOS.',
                'price'       => 249.00,
                'image'       => 'apple-watch-se',
                'stock'       => 65,
                'featured'    => false,
            ],

            // ── Accessories ──────────────────────────────────────────────────
            [
                'category'    => 'Accessories',
                'name'        => 'AirTag (4 Pack)',
                'description' => 'Lose your knack for losing things. AirTag uses the Find My network to help you keep track of keys, wallet, luggage, and more.',
                'price'       => 99.00,
                'image'       => 'airtag-4pack',
                'stock'       => 150,
                'featured'    => false,
            ],
            [
                'category'    => 'Accessories',
                'name'        => 'Apple Pencil Pro',
                'description' => 'Apple Pencil Pro with squeeze gesture, barrel roll, hover, and Find My support — designed for the latest iPad Air and iPad Pro.',
                'price'       => 129.00,
                'image'       => 'apple-pencil-pro',
                'stock'       => 80,
                'featured'    => false,
            ],
            [
                'category'    => 'Accessories',
                'name'        => 'MagSafe Charger (1 m)',
                'description' => 'The MagSafe Charger makes wireless charging easy and fast for iPhone. The magnetic alignment enables safe, secure, optimised wireless charging.',
                'price'       => 39.00,
                'image'       => 'magsafe-charger-1m',
                'stock'       => 200,
                'featured'    => false,
            ],
        ];

        foreach ($products as $data) {
            $product = Product::create([
                'category_id'    => $cat($data['category']),
                'name'           => $data['name'],
                'slug'           => Str::slug($data['name']),
                'description'    => $data['description'],
                'price'          => $data['price'],
                'stock_quantity' => $data['stock'],
                'is_featured'    => $data['featured'],
            ]);

            // Find matching image file in public/imgs/ (any extension)
            $matches = glob("{$imgs}/{$data['image']}.*");
            if (!empty($matches)) {
                $product->addMedia($matches[0])
                        ->preservingOriginal()
                        ->toMediaCollection('images');
            }
        }
    }
}
