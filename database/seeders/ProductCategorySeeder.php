<?php

namespace Database\Seeders;

use App\Domain\Store\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Camisetas', 'slug' => 'camisetas', 'sort_order' => 1],
            ['name' => 'Accesorios', 'slug' => 'accesorios', 'sort_order' => 2],
            ['name' => 'Edicion especial', 'slug' => 'edicion-especial', 'sort_order' => 3],
        ];

        foreach ($categories as $category) {
            ProductCategory::query()->updateOrCreate(
                ['slug' => $category['slug']],
                [
                    'name' => $category['name'],
                    'description' => null,
                    'sort_order' => $category['sort_order'],
                    'is_active' => true,
                    'metadata' => null,
                ],
            );
        }
    }
}
