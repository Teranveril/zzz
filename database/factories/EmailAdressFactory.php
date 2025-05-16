<?php

namespace Database\Factories;

use App\Models\EmailAddress;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmailAdressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = EmailAddress::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'email'   => fake()->unique()->safeEmail(),
        ];
    }
}
