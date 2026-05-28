<?php

namespace Database\Seeders;

use App\Domain\Store\Models\Product;
use App\Domain\Store\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = ProductCategory::query()->get()->keyBy('slug');

        $products = [
            [
                'sku' => 'VU-LOCAL-2024',
                'name' => 'Camiseta Local 2024',
                'slug' => 'camiseta-local-2024',
                'category' => 'camisetas',
                'badge' => 'EDICION 2024',
                'short_description' => 'El blanco de la provincia de Veraguas',
                'description' => 'Jersey oficial de local para la temporada 2024.',
                'price' => '65.00',
                'compare_at_price' => null,
                'is_featured' => true,
                'image_path' => 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=1200&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=1200&q=80'],
                'sort_order' => 1,
            ],
            [
                'sku' => 'VU-AWAY-2024',
                'name' => 'Camiseta Alterna',
                'slug' => 'camiseta-alterna',
                'category' => 'camisetas',
                'badge' => 'VISITANTE',
                'short_description' => 'Navy & Sky: el color de la victoria',
                'description' => 'Version visitante del uniforme oficial.',
                'price' => '65.00',
                'compare_at_price' => null,
                'is_featured' => false,
                'image_path' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=1200&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=1200&q=80'],
                'sort_order' => 2,
            ],
            [
                'sku' => 'VU-CAP-001',
                'name' => 'Gorra Oficial 9Forty',
                'slug' => 'gorra-oficial-9forty',
                'category' => 'accesorios',
                'badge' => 'NUEVO',
                'short_description' => 'Edicion de grada con ajuste premium',
                'description' => 'Gorra oficial para matchday y uso diario.',
                'price' => '20.00',
                'compare_at_price' => '25.00',
                'is_featured' => false,
                'image_path' => 'https://images.unsplash.com/photo-1521369909029-2afed882baee?auto=format&fit=crop&w=900&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1521369909029-2afed882baee?auto=format&fit=crop&w=900&q=80'],
                'sort_order' => 3,
            ],
            [
                'sku' => 'VU-SCARF-001',
                'name' => 'Bufanda Orgullo Indio',
                'slug' => 'bufanda-orgullo-indio',
                'category' => 'accesorios',
                'badge' => 'LIMITADA',
                'short_description' => 'Edicion coleccionista de temporada',
                'description' => 'Bufanda de grada con identidad United.',
                'price' => '18.00',
                'compare_at_price' => null,
                'is_featured' => false,
                'image_path' => 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?auto=format&fit=crop&w=900&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?auto=format&fit=crop&w=900&q=80'],
                'sort_order' => 4,
            ],
            [
                'sku' => 'VU-MUG-001',
                'name' => 'Taza Black Edition',
                'slug' => 'taza-black-edition',
                'category' => 'accesorios',
                'badge' => 'EDICION ESPECIAL',
                'short_description' => 'Coleccion premium para escritorio',
                'description' => 'Taza de coleccion para oficina o hogar.',
                'price' => '12.00',
                'compare_at_price' => null,
                'is_featured' => false,
                'image_path' => 'https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?auto=format&fit=crop&w=900&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?auto=format&fit=crop&w=900&q=80'],
                'sort_order' => 5,
            ],
            [
                'sku' => 'VU-BALL-001',
                'name' => 'Balon Entrenamiento',
                'slug' => 'balon-entrenamiento',
                'category' => 'accesorios',
                'badge' => 'OFICIAL',
                'short_description' => 'Listo para cancha y coleccion',
                'description' => 'Balon de entrenamiento del club.',
                'price' => '35.00',
                'compare_at_price' => null,
                'is_featured' => false,
                'image_path' => 'https://images.unsplash.com/photo-1517466787929-bc90951d0974?auto=format&fit=crop&w=900&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1517466787929-bc90951d0974?auto=format&fit=crop&w=900&q=80'],
                'sort_order' => 6,
            ],
            [
                'sku' => 'VU-RETRO-001',
                'name' => 'Jersey Retro Veraguas',
                'slug' => 'jersey-retro-veraguas',
                'category' => 'edicion-especial',
                'badge' => 'RETRO',
                'short_description' => 'Inspirada en los origenes del club',
                'description' => 'Pieza especial de coleccion inspirada en el origen del club.',
                'price' => '68.00',
                'compare_at_price' => '75.00',
                'is_featured' => false,
                'image_path' => 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?auto=format&fit=crop&w=1200&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?auto=format&fit=crop&w=1200&q=80'],
                'sort_order' => 7,
            ],
        ];

        foreach ($products as $product) {
            Product::query()->updateOrCreate(
                ['slug' => $product['slug']],
                [
                    'product_category_id' => $categories[$product['category']]?->id,
                    'sku' => $product['sku'],
                    'name' => $product['name'],
                    'description' => $product['description'],
                    'short_description' => $product['short_description'],
                    'price' => $product['price'],
                    'compare_at_price' => $product['compare_at_price'],
                    'currency' => 'USD',
                    'stock_quantity' => 20,
                    'track_stock' => false,
                    'is_featured' => $product['is_featured'],
                    'is_active' => true,
                    'badge' => $product['badge'],
                    'image_path' => $product['image_path'],
                    'gallery' => $product['gallery'],
                    'metadata' => null,
                    'sort_order' => $product['sort_order'],
                ],
            );
        }
    }
}
