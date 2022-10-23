<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessFactory extends Factory
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
			'dba' => $this->faker->company(),
			'address_line_1' => $this->faker->streetAddress(),
			'address_line_2' => $this->faker->secondaryAddress(),
			'city' => $this->faker->city(),
			'state' => $this->faker->stateAbbr(),
			'zip_code' => $this->faker->postcode(),
			'phone_number' => $this->faker->numerify('##########'),
			'federal_tax_id' => $this->faker->numerify('#########'),
			'start_date' => $this->faker->date(),
			'website' => $this->faker->url(),
			'type_of_entity' => 'corporation',
			'industry' => 'accounting',
			'gross_annual_sales' => 'less-100000',
			'monthly_sales_volume' => 'less-15000'
        ];
    }
}
