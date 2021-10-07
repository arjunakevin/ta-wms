<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Location::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'code' => 'LOC-' . strtoupper($this->faker->lexify('??????')) . '-' . $this->faker->randomNumber(6),
            'pick_blocked' => $this->faker->boolean(),
            'put_blocked' => $this->faker->boolean()
        ];
    }
}
