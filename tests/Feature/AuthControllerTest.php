<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthControllerTest extends TestCase
{


// Test user registration
public function testUserRegistration()
{
    //response structure
    $responseStructure = [
        'message',
        'user' => [
            'name',
            'email',
            'updated_at',
            'created_at',
            'id',
        ],
    ]; 

    $userData = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'secret_password',
    ];

    $response = $this->json('POST', '/api/register', $userData);

    $response->assertStatus(201)
        ->assertJsonStructure($responseStructure);
}

    // Test user login with valid credentials
    public function test_User_Login_With_Valid_Credentials()
    {
        // Create a user for testing
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('test_password'),
        ]);

        $loginData = [
            'email' => 'test@example.com',
            'password' => 'test_password',
        ];

        $response = $this->json('POST', '/api/login', $loginData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                    'email_verified_at',
                    'created_at',
                    'updated_at',
                    'token',
                ],
            ]);
    }

    // Test user login with invalid credentials
    public function test_User_Login_With_Invalid_Credentials()
    {
        $loginData = [
            'email' => 'nonexistent@example.com',
            'password' => 'invalid_password',
        ];

        $response = $this->json('POST', '/api/login', $loginData);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Invalid credentials']);
    }

    // Test user logout
    public function test_User_Logout()
    {
        // Create a user for testing
        $user = User::factory()->create();

        // Login the user
        $token = $user->createToken('TestToken')->accessToken;

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->json('POST', '/api/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Successfully logged out']);
    }
}
