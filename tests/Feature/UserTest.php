<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Http\Controllers\Controller;
use Laravel\Sanctum\Sanctum;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /////////////////////////////////////
    //Début Tests pour la methode register
    ////////////////////////////////////

        public function test_user_can_register_with_valid_data() : void
    {
        //prepare
        $this->seed();

        $json = User::factory()->make()->toArray();
        $json['password'] = 'validpassword';

        //act
        $response = $this->postJson('/api/signup', $json);

        //assert
        $response->assertStatus(CREATED)
                 ->assertJsonStructure(['message']);

        $this->assertDatabaseHas('users', [
            'login' => $json['login'],
            'email' => $json['email']
        ]);
    }

    public function test_user_registration_requires_valid_data() : void
    {
        //prepare
        $this->seed();

        $json = User::factory()->make([
            'login' => '',
            'email' => 'not-an-email',
            'first_name' => '',
            'last_name' => ''
        ])->toArray();
        $json['password'] = '';

        //act
        $response = $this->postJson('/api/signup', $json);

        //assert
        $response->assertStatus(INVALID_DATA)
                 ->assertJsonValidationErrors(['login', 'password', 'email', 'first_name', 'last_name']);
    }

    public function test_password_must_be_at_least_8_characters() : void
    {
        //prepare
        $this->seed();

        $json = User::factory()->make()->toArray();
        $json['password'] = 'short';

        //act
        $response = $this->postJson('/api/signup', $json);

        //assert
        $response->assertStatus(INVALID_DATA)
                    ->assertJsonValidationErrors(['password']);
    }

    public function test_user_cannot_register_with_existing_login() : void
    {
        //prepare
        $this->seed();

        $user = User::factory()->create([
            'login' => 'testuser'
        ]);
        $json = User::factory()->make([
            'login' => $user->login,
        ])->toArray();

        //act
        $response = $this->postJson('/api/signup', $json);

        //assert
        $response->assertStatus(INVALID_DATA)
                    ->assertJsonValidationErrors(['login']);
    }

    public function test_user_cannot_register_with_existing_email() : void
    {
        //prepare
        $this->seed();

        $user = User::factory()->create([
            'email' => 'test@test.com'
        ]);
        $json = User::factory()->make([
            'email' => $user->email
        ])->toArray();

        //act
        $response = $this->postJson('/api/signup', $json);

        //assert
        $response->assertStatus(INVALID_DATA)
                    ->assertJsonValidationErrors(['email']);
    }

    public function test_user_cannot_register_when_already_authenticated() : void
    {
        //prepare
        $this->seed();

        $user = User::factory()->create([
            'login' => 'newuser',
            'password' => bcrypt('validpassword'),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $json = User::factory()->make()->toArray();
        $json['password'] = 'validpassword';

        //act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/signup', $json);

        //assert
        $response->assertStatus(FORBIDDEN)
                 ->assertJson(['message' => 'User already logged in']);
    }

    /////////////////////////////////////
    //Fin Tests pour la methode register
    ////////////////////////////////////


    /////////////////////////////////////
    //Début Tests pour la methode login
    ////////////////////////////////////

    public function test_user_can_login_with_correct_credentials() : void
    {
        //prepare
        $this->seed();

        $json = User::factory()->make()->toArray();
        $json['password'] = 'validpassword';

        $signupResponse = $this->postJson('/api/signup', $json);
        $signupResponse->assertStatus(CREATED);

        $credentials = [
            'login' => $json['login'],
            'password' => 'validpassword',
        ];

        $user = User::where('login', $json['login'])->first();
        $tokenCountBefore = $user->tokens()->count();

        //act
        $response = $this->postJson('/api/signin', $credentials);

        //assert
        //source: https://laravel.com/docs/master/http-tests#assert-authenticated-as
        $this->assertAuthenticatedAs($user);

        $tokenCountAfter = User::where('login', $json['login'])->first()->tokens()->count();

        $this->assertEquals($tokenCountBefore + 1, $tokenCountAfter);

        $response->assertStatus(OK)
                 ->assertJsonStructure(['access_token', 'token_type']);
    }

    public function test_user_cannot_login_with_incorrect_credentials() : void
    {
        //prepare
        $this->seed();

        $user = User::factory()->create([
            'login' => 'testuser',
            'password' => bcrypt('validpassword'),
        ]);

        $json = [
            'login' => 'testuser',
            'password' => 'wrongpassword',
        ];

        //act
        $response = $this->postJson('/api/signin', $json);

        //assert
        $response->assertStatus(UNAUTHORIZED)
                 ->assertJson(['message' => 'Authentication failed']);
    }

    public function test_login_requires_valid_data() : void
    {
        //prepare
        $this->seed();
        $json = [
            'login' => '',
            'password' => ''
        ];

        //act
        $response = $this->postJson('/api/signin', $json);

        //assert
        $response->assertStatus(INVALID_DATA)
                 ->assertJsonValidationErrors(['login', 'password']);
    }

    public function test_user_cannot_login_when_already_authenticated() : void
    {
        //prepare
        $this->seed();

        $user = User::factory()->create([
            'login' => 'testuser',
            'password' => bcrypt('validpassword'),
        ]);

        $user->createToken('auth_token')->plainTextToken;

        $json = [
            'login' => 'testuser',
            'password' => 'validpassword',
        ];

        //act
        $response = $this->postJson('/api/signin', $json);

        //assert
        $response->assertStatus(FORBIDDEN)
                 ->assertJson(['message' => 'User already logged in']);
    }

    /////////////////////////////////////
    //Fin Tests pour la methode login
    ////////////////////////////////////

    /////////////////////////////////////
    //Début Tests pour la methode logout
    ////////////////////////////////////

    public function test_authenticated_user_can_logout() : void
    {
        //prepare
        $this->seed();

        $json = User::factory()->make()->toArray();
        $json['password'] = 'validpassword';

        $this->postJson('/api/signup', $json)
                          ->assertStatus(CREATED);

        $user = User::where('email', $json['email'])->first();
        $token = $user->createToken('auth_token')->plainTextToken;

        //act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/signout');

        //assert
        $response->assertStatus(NO_CONTENT);

        $this->assertDatabaseHas('users', [
            'email' => $json['email'],
        ]);

        //Source: https://laravel.com/docs/master/eloquent#refreshing-models
        $user->refresh();
        $this->assertEquals(0, $user->tokens()->count());
    }

    public function test_unauthenticated_user_cannot_logout() : void
    {
        //prepare
        $this->seed();

        //act
        $response = $this->postJson('/api/signout');

        //assert
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
        //prepare
        $this->seed();

        $json = [
            'login' => 'nonexistentuser',
            'password' => 'wrongpassword'
        ];

        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/api/signin', $json);
            $response->assertStatus(UNAUTHORIZED);
        }

        //act
        $response = $this->postJson('/api/signin', $json);

        //assert
        $response->assertStatus(TOO_MANY_REQUESTS);
    }

    public function test_throttling_on_auth_register_route() : void
    {
        //prepare
        $this->seed();

        for ($i = 0; $i < 5; $i++) {

            $json = User::factory()->make([
                'login' => 'user' . $i,
                'email' => 'test' . $i . '@test.com'
            ])->toArray();

            $json['password'] = 'validpassword';

            $response = $this->postJson('/api/signup', $json);
        }

        $json = User::factory()->make([
            'login' => 'user' .($i + 1),
            'email' => 'test' .($i + 1). '@test.com'
        ])->toArray();

        $json['password'] = 'validpassword';

        //act
        $response = $this->postJson('/api/signup', $json);

        //assert
        $response->assertStatus(TOO_MANY_REQUESTS);
    }

    /////////////////////////////////////
    //Fin Tests de throttling
    ////////////////////////////////////
}
