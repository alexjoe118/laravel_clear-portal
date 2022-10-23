<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Factories\Factory;

class PartnerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
		$ssn = $this->faker->numerify('#########');
		$ssn1 = Crypt::encrypt(Str::substr($ssn, 0, 5));
		$ssn2 = (string) Str::of($ssn)->substr(5, 4);

        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
			'title' => $this->faker->jobTitle(),
			'address_line_1' => $this->faker->streetAddress(),
			'address_line_2' => $this->faker->secondaryAddress(),
			'city' => $this->faker->city(),
			'state' => $this->faker->stateAbbr(),
			'zip_code' => $this->faker->postcode(),
			'phone_number' => $this->faker->numerify('##########'),
			'date_of_birth' => $this->faker->date(),
			'ssn_1' => $ssn1,
			'ssn_2' => $ssn2,
			'approximate_credit_score' => '800+',
			'business_ownership' => 100,
			'signature' => 'signatures/test-signature.png'
        ];
    }
}
