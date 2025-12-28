<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Http\Controllers\Controller;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /////////////////////////////////////
    //Début Tests pour la methode register
    ////////////////////////////////////

        public function test_user_can_register_with_valid_data() : void
    {
        $this->seed();

        $response = $this->postJson('/api/signup', [
            'login' => 'testuser',
            'password' => 'validpassword',
            'email' => 'test@test.com',
            'first_name' => 'Test',
            'last_name' => 'User'
        ]);
        $response->assertStatus(CREATED)
                 ->assertJsonStructure(['message', 'access_token', 'token_type']);
        $this->assertDatabaseHas('users', [
            'login' => 'testuser',
            'email' => 'test@test.com'
        ]);
    }

    public function test_user_registration_requires_valid_data() : void
    {
        $this->seed();

        $json = [
            'login' => '',
            'password' => 'short',
            'email' => 'not-an-email',
            'first_name' => '',
            'last_name' => ''
        ];

        $response = $this->postJson('/api/signup', $json);

        $response->assertStatus(INVALID_DATA)
                 ->assertJsonValidationErrors(['login', 'password', 'email', 'first_name', 'last_name']);
    }

    public function test_password_must_be_at_least_8_characters() : void
    {
        $this->seed();

        $json = [
            'login' => 'testuser2',
            'password' => 'short',
            'email' => 'test@test.com',
            'first_name' => 'Test',
            'last_name' => 'User'
        ];

        $response = $this->postJson('/api/signup', $json);

        $response->assertStatus(INVALID_DATA)
                    ->assertJsonValidationErrors(['password']);
    }

    public function test_user_cannot_register_with_existing_login() : void
    {
        $this->seed();

        $user = User::create([
            'login' => 'existinguser',
            'password' => bcrypt('validpassword'),
            'email' => 'test@test.com',
            'first_name' => 'Existing',
            'last_name' => 'User'
        ]);

        $json = [
            'login' => $user->login,
            'password' => 'anotherpassword',
            'email' => 'test2@test.com',
            'first_name' => 'Test',
            'last_name' => 'User'
        ];

        $response = $this->postJson('/api/signup', $json);

        $response->assertStatus(INVALID_DATA)
                    ->assertJsonValidationErrors(['login']);
    }

    public function test_user_cannot_register_with_existing_email() : void
    {
        $this->seed();

        $user = User::create([
            'login' => 'testuser',
            'password' => bcrypt('validpassword'),
            'email' => 'test@test.com',
            'first_name' => 'Existing',
            'last_name' => 'User'
        ]);

        $json = [
            'login' => 'testuser2',
            'password' => 'anotherpassword',
            'email' => $user->email,
            'first_name' => 'Test',
            'last_name' => 'User'
        ];

        $response = $this->postJson('/api/signup', $json);

        $response->assertStatus(INVALID_DATA)
                    ->assertJsonValidationErrors(['email']);
    }

    /////////////////////////////////////
    //Fin Tests pour la methode register
    ////////////////////////////////////


    /////////////////////////////////////
    //Début Tests pour la methode login
    ////////////////////////////////////

    public function test_user_can_login_with_correct_credentials() : void
    {
        $this->seed();

        $user = User::create([
            'login' => 'testuser',
            'password' => bcrypt('validpassword'),
            'email' => 'test@test.com',
            'first_name' => 'Test',
            'last_name' => 'User'
        ]);

        $json = [
            'login' => 'testuser',
            'password' => 'validpassword',
        ];

        $response = $this->postJson('/api/signin', $json);

        $response->assertStatus(OK)
                 ->assertJsonStructure(['access_token', 'token_type']);
    }

    public function test_user_cannot_login_with_incorrect_credentials() : void
    {
        $this->seed();

        $user = User::create([
            'login' => 'testuser',
            'password' => bcrypt('validpassword'),
            'email' => 'test@test.com',
            'first_name' => 'Test',
            'last_name' => 'User'
        ]);

        $json = [
            'login' => 'testuser',
            'password' => 'wrongpassword',
        ];

        $response = $this->postJson('/api/signin', $json);

        $response->assertStatus(UNAUTHORIZED)
                 ->assertJson(['message' => 'Authentication failed']);
    }

    public function test_login_requires_valid_data() : void
    {
        $this->seed();

        $json = [
            'login' => '',
            'password' => ''
        ];

        $response = $this->postJson('/api/signin', $json);

        $response->assertStatus(INVALID_DATA)
                 ->assertJsonValidationErrors(['login', 'password']);
    }

    /////////////////////////////////////
    //Fin Tests pour la methode login
    ////////////////////////////////////

    /////////////////////////////////////
    //Début Tests pour la methode logout
    ////////////////////////////////////

    public function test_authenticated_user_can_logout() : void
    {
        $this->seed();

        $user = User::create([
            'login' => 'testuser',
            'password' => bcrypt('validpassword'),
            'email' => 'test@test.com',
            'first_name' => 'Test',
            'last_name' => 'User'
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/signout');

        $response->assertStatus(NO_CONTENT);
    }

    public function test_unauthenticated_user_cannot_logout() : void
    {
        $this->seed();

        $response = $this->postJson('/api/signout');

        $response->assertStatus(UNAUTHORIZED);
    }

    /////////////////////////////////////
    //Fin Tests pour la methode logout
    ////////////////////////////////////

    /////////////////////////////////////
    //Début Tests de throttling
    ////////////////////////////////////

    public function test_throttling_on_auth_login_route() : void
    {
        $this->seed();

        $json = [
            'login' => 'nonexistentuser',
            'password' => 'wrongpassword'
        ];

        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/api/signin', $json);

            $response->assertStatus(UNAUTHORIZED);
        }

        // 6em demande devrait être bloquée
        $response = $this->postJson('/api/signin', $json);

        $response->assertStatus(TOO_MANY_REQUESTS);
    }

    public function test_throttling_on_auth_register_route() : void
    {
        $this->seed();

        for ($i = 0; $i < 5; $i++) {

        $json = [
                'login' => "user$i",
                'password' => 'validpassword',
                'email' => "test$i@test.com",
                'first_name' => 'User',
                'last_name' => 'User'
        ];

            $response = $this->postJson('/api/signup', $json);

            $response->assertStatus(CREATED);
        }


        $json = [
                'login' => 'user6',
                'password' => 'validpassword',
                'email' => 'test6@test.com',
                'first_name' => 'User',
                'last_name' => 'User'
        ];

        // 6em demande devrait être bloquée
        $response = $this->postJson('/api/signup', $json);

        $response->assertStatus(TOO_MANY_REQUESTS);
    }

    public function test_throttling_on_auth_logout_route() : void
    {
        $this->seed();

        $user = User::create([
            'login' => 'testuser',
            'password' => bcrypt('validpassword'),
            'email' => 'test@test.com',
            'first_name' => 'Test',
            'last_name' => 'User'
        ]);

        for ($i = 0; $i < 5; $i++) {

            $token = $user->createToken('auth_token')->plainTextToken;

            $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])->postJson('/api/signout');

            $response->assertStatus(NO_CONTENT);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        // 6em demande devrait être bloquée
                $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/signout');

        $response->assertStatus(TOO_MANY_REQUESTS);
    }

    /////////////////////////////////////
    //Fin Tests de throttling
    ////////////////////////////////////
}
