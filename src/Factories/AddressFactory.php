<?php

namespace Lecturize\Addresses\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Lecturize\Addresses\Models\Address;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    /** @inheritDoc */
    public function definition(): array
    {
        $gender = $this->faker->randomElement(['male', 'female']);

        return [
            'gender'       => substr($gender, 0, 1),
            'title_before' => $this->faker->randomElement([null, 'Ing.', 'Dr.']),

            'first_name'  => $this->faker->firstName($gender),
            'last_name'   => $this->faker->lastName(),

            'company' => $this->faker->company(),

            'street'        => $this->faker->streetAddress(),
            'city'          => $this->faker->city,
            'post_code'     => $this->faker->postcode(),
            'country_id'    => 40,

            'contact_phone' => $this->faker->e164PhoneNumber(),
            'contact_email' => $this->faker->email(),
            'billing_email' => $this->faker->companyEmail(),
        ];
    }
}
