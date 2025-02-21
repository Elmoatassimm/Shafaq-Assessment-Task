<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Payment;
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
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
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

    /**
     * Indicate that the user has a successful payment.
     */
    public function withSuccessfulPayment(): static
    {
        return $this->afterCreating(function (User $user) {
            Payment::factory()->create([
                'user_id' => $user->id,
                'status' => 'success', // Set the payment status to success
            ]);
        });
    }

    /**
     * Indicate that the user has a failed payment.
     */
    public function withFailedPayment(): static
    {
        return $this->afterCreating(function (User $user) {
            Payment::factory()->create([
                'user_id' => $user->id,
                'status' => 'failed', // Set the payment status to failed
            ]);
        });
    }
}