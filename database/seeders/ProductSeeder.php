<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $uaeProducts = [
            [
                'name' => 'Dubai Skyline Necklace',
                'description' => '18K gold necklace featuring Burj Khalifa and Palm Jumeirah',
                'base_price' => 3499.99,
                'slug' => 'dubai-skyline-necklace',
                'variants' => [
                    ['carat' => '18K', 'metal_type' => 'gold', 'price' => 3799.99, 'stock' => 2, 'sku' => 'NECK-DXB-18K'],
                    ['carat' => '18K', 'metal_type' => 'white_gold', 'price' => 3699.99, 'stock' => 3, 'sku' => 'NECK-DXB-18K-WG'],
                ]
            ],
            [
                'name' => 'Emirati Heritage Ring',
                'description' => 'Platinum ring inspired by traditional Emirati designs',
                'base_price' => 2599.99,
                'slug' => 'emirati-heritage-ring',
                'variants' => [
                    ['carat' => 'Platinum', 'metal_type' => 'platinum', 'price' => 2799.99, 'stock' => 4, 'sku' => 'RING-EMIRATI-PLT'],
                ]
            ],
            [
                'name' => 'Desert Rose Earrings',
                'description' => '14K gold earrings with rose-cut diamonds inspired by desert roses',
                'base_price' => 1999.99,
                'slug' => 'desert-rose-earrings',
                'variants' => [
                    ['carat' => '14K', 'metal_type' => 'gold', 'price' => 2199.99, 'stock' => 5, 'sku' => 'EARR-DESERT-14K'],
                    ['carat' => '14K', 'metal_type' => 'white_gold', 'price' => 2099.99, 'stock' => 2, 'sku' => 'EARR-DESERT-14K-WG'],
                ]
            ],
        ];

        foreach ($uaeProducts as $item) {
            $product = Product::create(collect($item)->except('variants')->toArray());
            $product->variants()->createMany($item['variants']);
        }
    }
}
