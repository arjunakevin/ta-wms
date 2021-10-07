<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Client::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'code' => 'CLT-' . strtoupper($this->faker->lexify('??????')) . '-' . $this->faker->randomNumber(6),
            'name' => $this->faker->company(),
            'address_1' => $this->faker->address(),
            'address_2' => $this->faker->optional()->address(),
        ];
    }
}
