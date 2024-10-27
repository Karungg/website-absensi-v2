<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
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
            'nik' => fake()->unique()->numerify('################'),
            'nip' => fake()->unique()->numerify('######'),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'place_of_birth' => fake()->city(),
            'date_of_birth' => fake()->date(),
            'phone' => fake()->unique()->numerify('##############'),
            'address' => fake()->address(),
            'leave_allowance' => 12,
            'sick_allowance' => 14,
            'give_birth_allowance' => 90,
            'date_of_entry' => fake()->date(),
            'profile_picture' => fake()->imageUrl(640, 480, 'people'),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): Factory
    {
        return $this->state(function (array $attributes) {
            return [];
        })->afterCreating(function (User $user) {
            $user->assignRole('admin');
        });
    }

    public function director(): Factory
    {
        return $this->state(function (array $attributes) {
            return [];
        })->afterCreating(function (User $user) {
            $user->assignRole('director');
        });
    }

    public function resource(): Factory
    {
        return $this->state(function (array $attributes) {
            return [];
        })->afterCreating(function (User $user) {
            $user->assignRole('resource');
        });
    }

    public function employee(): Factory
    {
        return $this->state(function (array $attributes) {
            return [];
        })->afterCreating(function (User $user) {
            $user->assignRole('employee');
        });
    }

    public function headOfDivision(): Factory
    {
        return $this->state(function (array $attributes) {
            return [];
        })->afterCreating(function (User $user) {
            $user->assignRole('headOfDivision');
        });
    }
}
