<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

/** Safe to run in production: idempotent, keyed by slug. */
class CategorySeeder extends Seeder
{
    public const CATEGORIES = [
        ['Fresh Fruits', 'fresh-fruits', 'Seasonal fruit picked close to home.'],
        ['Vegetables', 'vegetables', 'Everyday vegetables, harvested fresh.'],
        ['Herbs', 'herbs', 'Fresh and dried herbs for cooking and tea.'],
        ['Eggs', 'eggs', 'Free-range eggs from small flocks.'],
        ['Dairy', 'dairy', 'Milk, amasi, butter and cheese.'],
        ['Grains & Pulses', 'grains-pulses', 'Maize, beans, sorghum and more.'],
        ['Meat', 'meat', 'Beef, lamb, goat and pork from local farms.'],
        ['Poultry', 'poultry', 'Whole chickens and portions.'],
        ['Homemade Products', 'homemade-products', 'Honey, jams, chutneys and baked goods.'],
        ['Other Farm Produce', 'other-farm-produce', 'Everything else our farmers grow and make.'],
    ];

    public function run(): void
    {
        foreach (self::CATEGORIES as $i => [$name, $slug, $description]) {
            Category::updateOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'description' => $description, 'sort_order' => $i + 1, 'is_active' => true],
            );
        }
    }
}
