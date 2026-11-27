<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            // School rule: every account uses a @crc.pshs.edu.ph email.
            'email' => fake()->unique()->userName().'@crc.pshs.edu.ph',
            'role' => 'member',
            'must_change_password' => false,
            // A finished welcome setup, so tests can open member pages right away.
            'codename' => fake()->unique()->bothify('Coder####'),
            'track' => 'py',
            'avatar' => 'bot-200',
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * A member who was just added: default password, no codename/track/avatar yet.
     */
    public function newMember(): static
    {
        return $this->state(fn (array $attributes) => [
            'must_change_password' => true,
            'codename' => null,
            'track' => null,
            'avatar' => null,
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
