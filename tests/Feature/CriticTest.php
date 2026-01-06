<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Film;
use App\Models\Critic;
use App\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\Controller;
use Laravel\Sanctum\Sanctum;

class CriticTest extends TestCase
{
    use RefreshDatabase;

    /////////////////////////////////////
    //Début Tests pour la methode store
    ////////////////////////////////////

    public function test_user_can_create_critic_with_valid_data() : void
    {
        //prepare
        $this->seed();

        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $film = Film::factory()->create();

        $json = [
            'score' => 4,
            'comment' => 'Great movie!',
            'film_id' => $film->id
        ];

        //act
        $response = $this->postJson('/api/critics', $json);

        //assert
        $response->assertStatus(CREATED)
                 ->assertJsonStructure(['message', 'data' => ['score', 'comment', 'user_id', 'film_id']]);

        $this->assertDatabaseHas('critics', [
            'score' => $json['score'],
            'comment' => $json['comment'],
            'user_id' => $user->id,
            'film_id' => $film->id
        ]);
    }

    public function test_user_cannot_create_multiple_critics_for_same_film() : void
    {
        //prepare
        $this->seed();

        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $film = Film::factory()->create();

        Critic::factory()->create([
            'user_id' => $user->id,
            'film_id' => $film->id
        ]);

        $json = [
            'score' => 5,
            'comment' => 'Amazing movie!',
            'film_id' => $film->id
        ];

        //act
        $response = $this->postJson('/api/critics', $json);

        //assert
        $response->assertStatus(FORBIDDEN)
                 ->assertJson(['message' => 'User has already submitted a critic for this film.']);
    }

    public function test_user_cannot_create_critic_with_invalid_data() : void
    {
        //prepare
        $this->seed();

        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $film = Film::factory()->create();

        $json = [
            'score' => 11,
            'comment' => '',
            'film_id' => $film->id
        ];

        //act
        $response = $this->postJson('/api/critics', $json);

        //assert
        $response->assertStatus(INVALID_DATA)
                 ->assertJsonValidationErrors(['score', 'comment']);
    }

    public function test_unauthenticated_user_cannot_create_critic() : void
    {
        //prepare
        $this->seed();

        $film = Film::factory()->create();

        $json = [
            'score' => 4,
            'comment' => 'Great movie!',
            'film_id' => $film->id
        ];

        //act
        $response = $this->postJson('/api/critics', $json);

        //assert
        $response->assertStatus(UNAUTHORIZED);
    }

    public function test_user_cannot_create_critic_for_nonexistent_film() : void
    {
        //prepare
        $this->seed();

        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $json = [
            'score' => 4,
            'comment' => 'Great movie!',
            'film_id' => 9999
        ];

        //act
        $response = $this->postJson('/api/critics', $json);

        //assert
        $response->assertStatus(INVALID_DATA)
                 ->assertJsonValidationErrors(['film_id']);
    }

    public function test_user_cannot_create_critic_with_missing_data() : void
    {
        //prepare
        $this->seed();

        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $film = Film::factory()->create();

        $json = [
            'film_id' => $film->id
        ];

        //act
        $response = $this->postJson('/api/critics', $json);

        //assert
        $response->assertStatus(INVALID_DATA)
                 ->assertJsonValidationErrors(['comment', 'score']);
}

    /////////////////////////////////////
    //Fin Tests pour la methode store
    ////////////////////////////////////

    /////////////////////////////////////
    //Début Tests de throttling
    ////////////////////////////////////

    public function test_throttling_on_create_critic() : void
    {
        //prepare
        $this->seed();
        $maxAttempts = 60;

        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        for ($i = 0; $i < $maxAttempts; $i++) {

            $film = Film::factory()->create();

            $json = Critic::factory()->create([
                'user_id' => $user->id,
                'film_id' => $film->id
            ])->toArray();

            $response = $this->postJson('/api/critics', $json);
        }

        $json = Critic::factory()->create([
            'user_id' => $user->id,
            'film_id' => Film::factory()->create()->id
        ])->toArray();

        //act
        $response = $this->postJson('/api/critics', $json);

        //assert
        $response->assertStatus(TOO_MANY_REQUESTS);
    }

    /////////////////////////////////////
    //Fin Tests de throttling
    ////////////////////////////////////
}
