<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\GridPreset;
use App\Models\Pattern;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password')
        ]);

        // 1. Insert Categories
        $catBedroom = Category::create(['name' => 'Bedroom']);
        $catLivingRoom = Category::create(['name' => 'Living Room']);
        $catStorage = Category::create(['name' => 'Storage']);

        // 2. Insert Products
        $prodBantal = Product::create([
            'category_id' => $catBedroom->id,
            'name' => 'Bantal Tidur',
            'base_image' => 'bantal_tidur_base.png',
            'shadow_overlay' => 'bantal_tidur_shadow.png'
        ]);
        
        $prodSofa = Product::create([
            'category_id' => $catLivingRoom->id,
            'name' => 'Sofa Minimalis',
            'base_image' => 'sofa_base.png',
            'shadow_overlay' => 'sofa_shadow.png'
        ]);

        $prodLemari = Product::create([
            'category_id' => $catStorage->id,
            'name' => 'Lemari Dua Pintu',
            'base_image' => 'lemari_base.png',
            'shadow_overlay' => 'lemari_shadow.png'
        ]);

        // 3. Insert Patterns
        Pattern::insert([
            ['name' => 'Batik Megamendung', 'file_path' => 'patterns/batik_blue.jpg', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Polkadot Retro', 'file_path' => 'patterns/polka_red.jpg', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Minimalist Stripe', 'file_path' => 'patterns/stripe_gray.jpg', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Wood Texture', 'file_path' => 'patterns/oak_wood.jpg', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Marble White', 'file_path' => 'patterns/marble.jpg', 'created_at' => now(), 'updated_at' => now()]
        ]);

        // 4. Insert Grid Presets
        $grid1x1 = GridPreset::create(['label' => '1x1 Full', 'colss' => 1, 'rowss' => 1, 'scale_value' => 1.00]);
        $grid2x1 = GridPreset::create(['label' => '2x1 Horizontal', 'colss' => 2, 'rowss' => 1, 'scale_value' => 0.50]);
        $grid2x2 = GridPreset::create(['label' => '2x2 Medium', 'colss' => 2, 'rowss' => 2, 'scale_value' => 0.50]);
        $grid3x2 = GridPreset::create(['label' => '3x2 Grid', 'colss' => 3, 'rowss' => 2, 'scale_value' => 0.33]);
        $grid4x4 = GridPreset::create(['label' => '4x4 Small', 'colss' => 4, 'rowss' => 4, 'scale_value' => 0.25]);

        // 5. Associating Product to Grid Presets
        $prodBantal->gridPresets()->attach([$grid1x1->id, $grid2x2->id, $grid4x4->id]);
        $prodSofa->gridPresets()->attach([$grid1x1->id, $grid2x1->id]);
        $prodLemari->gridPresets()->attach([$grid1x1->id, $grid3x2->id]);
    }
}
