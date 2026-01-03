<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Cement',
                'slug'        => 'cement',
                'description' => 'Various types of cement',
                'is_active'   => true,
            ],
            [
                'name'        => 'Steel',
                'slug'        => 'steel',
                'description' => 'Rebars, steel sheets, and rods',
                'is_active'   => true,
            ],
            [
                'name'        => 'Paint',
                'slug'        => 'paint',
                'description' => 'Construction paints and coatings',
                'is_active'   => true,
            ],
            [
                'name'        => 'Wood & Lumber',
                'slug'        => 'wood-lumber',
                'description' => 'Timber, plywood, and lumber',
                'is_active'   => true,
            ],
            [
                'name'        => 'Tools & Hardware',
                'slug'        => 'tools-hardware',
                'description' => 'Hand tools, power tools, and hardware',
                'is_active'   => true,
            ],
            [
                'name'        => 'Plumbing',
                'slug'        => 'plumbing',
                'description' => 'Pipes, fittings, and plumbing supplies',
                'is_active'   => true,
            ],
            [
                'name'        => 'Electrical',
                'slug'        => 'electrical',
                'description' => 'Wires, cables, switches, and electrical parts',
                'is_active'   => true,
            ],
            [
                'name'        => 'Roofing & Tiles',
                'slug'        => 'roofing-tiles',
                'description' => 'Roofing sheets, tiles, and roof accessories',
                'is_active'   => true,
            ],
            [
                'name'        => 'Flooring',
                'slug'        => 'flooring',
                'description' => 'Tiles, vinyl, laminate, and flooring supplies',
                'is_active'   => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']], 
                $category                      
            );
        }
    }
}
