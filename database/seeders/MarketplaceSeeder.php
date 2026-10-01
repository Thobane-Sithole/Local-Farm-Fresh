<?php

namespace Database\Seeders;

use App\Enums\Province;
use App\Enums\UserRole;
use App\Models\Address;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Development data: 1 admin, 10 farmers, 36 products, 20 customers.
 * Every seeded account uses the password "password".
 */
class MarketplaceSeeder extends Seeder
{
    /**
     * [farmer name, email, farm name, province, municipality, town, delivery fee, description, products[]]
     * product: [name, category slug, price, unit, qty, short description]
     */
    private function farms(): array
    {
        return [
            ['Thandeka Mokoena', 'thandeka@farm.test', 'Mokoena Family Farm', Province::Limpopo, 'Polokwane', 'Polokwane', 30,
                'Three generations growing vegetables on the outskirts of Polokwane.', [
                    ['Fresh Tomatoes', 'vegetables', 18.00, 'kg', 120, 'Vine-ripened and picked the morning of delivery.'],
                    ['Green Peppers', 'vegetables', 9.50, 'item', 200, 'Crisp, thick-walled peppers.'],
                    ['Butternut', 'vegetables', 16.00, 'kg', 90, 'Sweet, dense butternut for soups and roasting.'],
                    ['Onions', 'vegetables', 15.00, 'kg', 150, 'Brown onions, dried for long keeping.'],
                ]],
            ['Sipho Dlamini', 'sipho@farm.test', 'Umvoti Valley Greens', Province::KwaZuluNatal, 'Umvoti', 'Greytown', 35,
                'Leafy greens grown with drip irrigation in the Umvoti valley.', [
                    ['Spinach', 'vegetables', 12.00, 'bunch', 80, 'Large-leaf Swiss chard, washed and bunched.'],
                    ['Cabbage', 'vegetables', 22.00, 'item', 60, 'Firm green heads, about 2 kg each.'],
                    ['Fresh Carrots', 'vegetables', 20.00, 'kg', 100, 'Sweet carrots, tops removed.'],
                    ['Beetroot', 'vegetables', 17.00, 'bunch', 50, 'Deep red beetroot with leaves on.'],
                ]],
            ['Pieter van der Merwe', 'pieter@farm.test', 'Witzenberg Orchards', Province::WesternCape, 'Witzenberg', 'Ceres', 50,
                'Family orchard in the Ceres valley, known for crisp apples and pears.', [
                    ['Apples', 'fresh-fruits', 24.00, 'kg', 200, 'Crisp Golden Delicious and Granny Smith.'],
                    ['Pears', 'fresh-fruits', 26.00, 'kg', 120, 'Packham pears, ready to ripen at home.'],
                    ['Apricot Jam', 'homemade-products', 45.00, 'bottle', 40, 'Small-batch jam from our own apricots.'],
                ]],
            ['Nomvula Khumalo', 'nomvula@farm.test', 'Khumalo Poultry & Eggs', Province::Gauteng, 'City of Tshwane', 'Bronkhorstspruit', 25,
                'Free-range hens and broilers, raised on open pasture.', [
                    ['Free Range Eggs', 'eggs', 45.00, 'dozen', 150, 'Large eggs from pasture-raised hens.'],
                    ['Whole Chicken', 'poultry', 95.00, 'item', 40, 'Free-range, about 1.8 kg, cleaned.'],
                    ['Chicken Portions', 'poultry', 85.00, 'kg', 60, 'Drumsticks and thighs, free-range.'],
                ]],
            ['Lerato Molefe', 'lerato@farm.test', 'Molefe Dairy', Province::NorthWest, 'Rustenburg', 'Rustenburg', 30,
                'A small Jersey herd producing rich milk and cultured dairy.', [
                    ['Fresh Milk', 'dairy', 22.00, 'litre', 100, 'Full-cream Jersey milk, pasteurised.'],
                    ['Amasi', 'dairy', 38.00, 'bottle', 60, 'Traditionally fermented, thick and tangy.'],
                    ['Farm Butter', 'dairy', 65.00, 'item', 30, '500 g block, lightly salted.'],
                    ['Farm Cheese', 'dairy', 120.00, 'kg', 20, 'Mild, semi-hard cheese aged six weeks.'],
                ]],
            ['Themba Nkosi', 'themba@farm.test', 'Lowveld Tropicals', Province::Mpumalanga, 'City of Mbombela', 'White River', 40,
                'Subtropical fruit from the warm Lowveld hills.', [
                    ['Bananas', 'fresh-fruits', 19.00, 'kg', 250, 'Sweet Cavendish bananas.'],
                    ['Avocados', 'fresh-fruits', 12.00, 'item', 300, 'Creamy Hass avocados.'],
                    ['Mangoes', 'fresh-fruits', 15.00, 'item', 180, 'Juicy Tommy Atkins mangoes, in season.'],
                    ['Macadamia Nuts', 'other-farm-produce', 180.00, 'kg', 25, 'Shelled, raw macadamias.'],
                ]],
            ['Ayanda Ngcobo', 'ayanda@farm.test', 'Sweetwater Bees', Province::EasternCape, 'Makana', 'Makhanda', 45,
                'Hives kept among fynbos and wild flowers around Makhanda.', [
                    ['Raw Honey', 'homemade-products', 110.00, 'bottle', 50, '500 g of raw, unfiltered wildflower honey.'],
                    ['Honeycomb', 'homemade-products', 150.00, 'item', 15, 'A 400 g piece of fresh comb.'],
                    ['Beeswax Candles', 'other-farm-produce', 60.00, 'item', 30, 'Hand-poured pure beeswax.'],
                ]],
            ['Johan Botha', 'johan@farm.test', 'Karoo Hills Farm', Province::NorthernCape, 'Ubuntu', 'Victoria West', 60,
                'Free-roaming Dorper sheep grazing on Karoo bush.', [
                    ['Karoo Lamb Chops', 'meat', 165.00, 'kg', 40, 'Tender chops from free-roaming lamb.'],
                    ['Lamb Stewing Meat', 'meat', 140.00, 'kg', 35, 'Bone-in, ideal for potjie.'],
                    ['Beef Mince', 'meat', 110.00, 'kg', 50, 'Lean grass-fed beef mince.'],
                ]],
            ['Mpho Sithebe', 'mpho@farm.test', 'Rooiberg Grain', Province::FreeState, 'Dihlabeng', 'Bethlehem', 50,
                'Dryland maize and beans from the eastern Free State.', [
                    ['White Maize', 'grains-pulses', 95.00, 'bag', 80, '10 kg bag of whole white maize.'],
                    ['Sugar Beans', 'grains-pulses', 42.00, 'kg', 100, 'Dried speckled sugar beans.'],
                    ['Potatoes', 'vegetables', 85.00, 'bag', 70, '7 kg bag of washed potatoes.'],
                    ['Sorghum', 'grains-pulses', 38.00, 'kg', 60, 'Whole red sorghum grain.'],
                ]],
            ['Zanele Mahlangu', 'zanele@farm.test', 'Kasi Herb Garden', Province::Gauteng, 'City of Johannesburg', 'Soweto', 20,
                'An urban garden in Soweto growing herbs in raised beds.', [
                    ['Fresh Coriander', 'herbs', 10.00, 'bunch', 60, 'Fragrant coriander, cut to order.'],
                    ['Fresh Basil', 'herbs', 12.00, 'bunch', 40, 'Sweet basil for pesto and salads.'],
                    ['Rosemary', 'herbs', 10.00, 'bunch', 45, 'Woody rosemary sprigs for roasts.'],
                    ['Rooibos Tea', 'herbs', 55.00, 'bag', 30, '250 g loose-leaf organic rooibos.'],
                ]],
        ];
    }

    public function run(): void
    {
        $categories = Category::pluck('id', 'slug');

        User::factory()->admin()->create([
            'name' => 'Platform Admin',
            'email' => 'admin@localfarmfresh.test',
        ]);

        foreach ($this->farms() as [$name, $email, $farmName, $province, $municipality, $town, $fee, $description, $products]) {
            $user = User::factory()->farmer()->create(['name' => $name, 'email' => $email]);

            $farm = $user->farmerProfile()->create([
                'farm_name' => $farmName,
                'description' => $description,
                'province' => $province,
                'municipality' => $municipality,
                'town' => $town,
                'delivery_fee' => $fee,
            ]);
            $farm->forceFill(['is_verified' => true])->save();

            foreach ($products as [$productName, $categorySlug, $price, $unit, $qty, $short]) {
                $product = new Product([
                    'category_id' => $categories[$categorySlug],
                    'name' => $productName,
                    'short_description' => $short,
                    'description' => $short.' Grown and packed by '.$farmName.' in '.$town.'.',
                    'price' => $price,
                    'unit' => $unit,
                    'quantity_available' => $qty,
                    'is_available' => true,
                ]);
                $product->farmer_profile_id = $farm->id;
                $product->save();
            }
        }

        // A known customer account for manual testing, plus 19 more.
        $customers = User::factory()->count(19)->create()->prepend(
            User::factory()->create([
                'name' => 'Test Customer',
                'email' => 'customer@localfarmfresh.test',
                'role' => UserRole::Customer,
            ])
        );

        $customers->each(fn (User $customer) => Address::factory()->create([
            'user_id' => $customer->id,
            'recipient_name' => $customer->name,
            'phone' => $customer->phone,
        ]));

        // Sample orders are seeded in Phase 4, through OrderService, so they
        // follow exactly the same rules as real checkouts.
    }
}
