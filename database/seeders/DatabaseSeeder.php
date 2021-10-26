<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory(['username' => 'Admin', 'client_id' => null])->create();
        $this->runLenovoSeeder();
        $this->runAsusSeeder();
        $this->runAppleSeeder();
        $this->runHPSeeder();
        $this->runDellSeeder();
        $this->runLocationSeeder();
    }

    /**
     * Create Lenovo client and products
     *
     * @return void
     */
    protected function runLenovoSeeder()
    {
        $client = Client::factory([
            'code' => 'LENOVO',
            'name' => 'Lenovo'
        ])->create();

        $products = [
            'Lenovo ThinkPad X1 Carbon',
            'Lenovo Yoga 9i',
            'Lenovo ThinkPad X1 Yoga',
            'Lenovo ThinkPad X13',
            'Lenovo ThinkPad X1 Nano',
            'Lenovo ThinkPad X12 Detachable',
            'Lenovo Legion 5 Pro',
            'Lenovo Chromebook Duet',
            'Lenovo ThinkPad P15'
        ];

        $this->generate($client, $products);
    }

    /**
     * Create Asus client and products
     *
     * @return void
     */
    protected function runAsusSeeder()
    {
        $client = Client::factory([
            'code' => 'ASUS',
            'name' => 'Asus'
        ])->create();

        $products = [
            'Asus ZenBook 13 UX325EA',
            'Asus Chromebook Flip C436',
            'Asus ROG Strix Scar III',
            'Asus ROG Zephyrus G14',
            'Asus ZenBook Pro Duo',
            'Asus ROG Flow X13',
            'Asus ProArt StudioBook 15',
            'Asus ExpertBook B9450',
            'Asus ZenBook 13'
        ];

        $this->generate($client, $products);
    }

    /**
     * Create Apple client and products
     *
     * @return void
     */
    protected function runAppleSeeder()
    {
        $client = Client::factory([
            'code' => 'APPLE',
            'name' => 'Apple'
        ])->create();

        $products = [
            'Apple Macbook Pro 13" (M1 2020)',
            'Apple Macbook Air (M1 2020)',
            'Apple Macbook Pro 16-inch (2019)'
        ];

        $this->generate($client, $products);
    }

    /**
     * Create HP client and products
     *
     * @return void
     */
    protected function runHPSeeder()
    {
        $client = Client::factory([
            'code' => 'HP',
            'name' => 'HP'
        ])->create();

        $products = [
            'HP Envy 13',
            'HP Omen 15',
            'HP Spectre x360 14',
            'HP EliteBook x360 1040 G7',
            'HP Chromebook x2'
        ];

        $this->generate($client, $products);
    }

    /**
     * Create HP client and products
     *
     * @return void
     */
    protected function runDellSeeder()
    {
        $client = Client::factory([
            'code' => 'DELL',
            'name' => 'Dell'
        ])->create();

        $products = [
            'Dell XPS 15',
            'Dell Vostro 3400 14"',
            'Dell Inspiron 3502 15.6"',
            'Alienware M15 R3',
            'Dell Precision 5750',
            'Dell G5 15 SE'
        ];

        $this->generate($client, $products);
    }

    /**
     * Generate data
     *
     * @param Client $client
     * @param array $products
     * @return void
     */
    protected function generate(Client $client, array $products)
    {
        foreach ($products as $product) {
            Product::factory(['description_1' => $product])
                ->for($client)
                ->create();
        }
    }
    
    /**
     * Create location data
     *
     * @return void
     */
    protected function runLocationSeeder()
    {
        $locations = [];
        $count = 999;
        
        while ($count) {
            array_push(
                $locations,
                Location::factory([
                    'code' => 'A' . sprintf('%03d', $count),
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            )->raw());

            $count--;
        }

        Location::insert($locations);
    }
}
