<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LenderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->company(),
            'email' => $this->faker->unique()->safeEmail(),
			'phone_number' => $this->faker->numerify('##########')
        ];
    }
}
