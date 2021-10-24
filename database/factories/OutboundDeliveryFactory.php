<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\OutboundDelivery;
use Illuminate\Database\Eloquent\Factories\Factory;

class OutboundDeliveryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OutboundDelivery::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'reference' => 'OUT-' . strtoupper($this->faker->lexify('??????')) . '-' . $this->faker->randomNumber(6),
            'client_id' => Client::factory(),
            'destination_name' => $this->faker->name(),
            'destination_phone' => $this->faker->phoneNumber(),
            'destination_address_1' => $this->faker->address(),
            'destination_address_2' => $this->faker->optional()->address(),
            'request_delivery_date' => $this->faker->dateTime()->format('Y-m-d H:i:s'),
            'po_reference' => $this->faker->boolean() ? 'PO-' . strtoupper($this->faker->lexify('??????')) . '-' . $this->faker->randomNumber(6) : null,
            'status' => OutboundDelivery::STATUS_UNCOMMITTED
        ];
    }
}
