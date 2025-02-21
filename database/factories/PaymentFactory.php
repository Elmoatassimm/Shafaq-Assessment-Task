<?php
namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'payment_intent_id' => $this->faker->uuid,
            'status' => 'pending', // Default status
            // Add other necessary fields
        ];
    }
}