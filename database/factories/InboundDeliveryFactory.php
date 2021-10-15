<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\InboundDelivery;
use Illuminate\Database\Eloquent\Factories\Factory;

class InboundDeliveryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = InboundDelivery::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'reference' => 'INB-' . strtoupper($this->faker->lexify('??????')) . '-' . $this->faker->randomNumber(6),
            'client_id' => Client::factory(),
            'arrival_date' => $this->faker->optional()->dateTime(),
            'po_date' => $this->faker->optional()->dateTime(),
            'notes' => $this->faker->optional()->text(),
            'status' => InboundDelivery::STATUS_UNRECEIVED
        ];
    }
}
