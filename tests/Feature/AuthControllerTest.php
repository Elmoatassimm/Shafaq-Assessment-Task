<?php

use App\Models\User;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can register a user', function () {
    $response = $this->postJson('/api/auth/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'passwordd',
        'password_confirmation' => 'passwordd', // Ensure this is included
    ]);

    $response->assertStatus(201)
             ->assertJson([
                 'status' => true,
                 'message' => 'Registered successfully',
                 'data' => [
                     'user' => [
                         'name' => 'Test User',
                         'email' => 'test@example.com',
                         // You can also check for created_at and updated_at if needed
                     ],
                     'token' => true,  // Check if token exists
                 ],
             ]);
});

it('can login a user', function () {
    // Create a user with a successful payment
    $user = User::factory()->create(['password' => bcrypt('password')]);
    Payment::factory()->create(['user_id' => $user->id, 'status' => 'success']); // Ensure payment is successful

    $response = $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertStatus(200)
             ->assertJson([
                 'status' => true,
                 'message' => 'Login successfully',
                 'data' => [
                     'token' => true, // Check if token exists
                     'token_type' => 'bearer',
                     'expires_in' => true,
                 ],
             ]);
});

it('can fetch authenticated user', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->getJson('/api/auth/me');

    $response->assertStatus(200)
             ->assertJson(['data' => ['id' => $user->id]]);
});

it('can logout a user', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->postJson('/api/auth/logout');

    $response->assertStatus(200)
             ->assertJson(['message' => 'Successfully logged out']);
});