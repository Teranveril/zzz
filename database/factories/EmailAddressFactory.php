<?php

namespace Database\Factories;

use App\Models\EmailAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmailAddressFactory extends Factory
{
    protected $model = EmailAddress::class;

    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            ];
    }
}
