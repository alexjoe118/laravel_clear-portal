<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
		do {
            $customerId = random_int(1000000, 9999999);
        } while (User::where('customer_id', $customerId)->first());

		$ssn = $this->faker->numerify('#########');
		$ssn1 = Crypt::encrypt(Str::substr($ssn, 0, 5));
		$ssn2 = (string) Str::of($ssn)->substr(5, 4);

        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('clear-portal-dummy'),
			'customer_id' => $customerId,
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
			'business_id' => Business::factory(),
			'signature' => 'signatures/test-signature.png',
			'photo' => 'photos/test-photo.jpg'
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
