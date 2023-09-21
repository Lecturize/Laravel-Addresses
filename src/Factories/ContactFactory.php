<?php

namespace Lecturize\Addresses\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Lecturize\Addresses\Models\Contact;

class ContactFactory extends Factory
{
    protected $model = Contact::class;

    /** @inheritDoc */
    public function definition(): array
    {
        $gender = $this->faker->randomElement(['male', 'female']);

        return [
            'gender'       => str($gender)->limit(1),
            'title-before' => $this->faker->randomElement([null, 'Ing.', 'Dr.']),

            'first_name'  => $this->faker->firstName($gender),
            'last_name'   => $this->faker->lastName(),

            'company' => $this->faker->company(),

            'phone'         => $this->faker->e164PhoneNumber(),
            'email'         => $this->faker->email(),
            'email_invoice' => $this->faker->companyEmail(),
        ];
    }
}
