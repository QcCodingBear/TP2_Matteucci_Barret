<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Film;
use App\Models\User;
use App\Http\Controllers\Controller;
use Laravel\Sanctum\Sanctum;

class FilmTest extends TestCase
{
    use RefreshDatabase;

    /////////////////////////////////////
    //Début Tests pour la methode store
    ////////////////////////////////////

    public function test_film_creation_with_valid_data() : void
    {
        //prepare
        $this->seed();

        Sanctum::actingAs(
            User::factory()->create(['role_id' => ADMIN]),
            ['*']
        );

        $json = Film::factory()->make()->toArray();

        //act
        $response = $this->postJson('/api/films', $json);

        //assert
        $response->assertStatus(CREATED)
                 ->assertJsonStructure(['message']);

        $this->assertDatabaseHas('films', [
            'title' => $json['title'],
            'release_year' => $json['release_year']
        ]);
    }

    public function test_film_creation_requires_valid_data() : void
    {
        //prepare
        $this->seed();

        Sanctum::actingAs(
            User::factory()->create(['role_id' => ADMIN]),
            ['*']
        );

        $json = Film::factory()->make([
            'title' => "",
            'release_year' => 'not-an-integer',
            'length' => 'not-an-integer',
            'language_id' => 'not-an-integer'
        ])->toArray();

        //act
        $response = $this->postJson('/api/films', $json);

        //assert
        $response->assertStatus(INVALID_DATA)
                 ->assertJsonValidationErrors(['title', 'release_year', 'length', 'language_id']);
    }

    public function test_film_creation_requires_all_required_fields() : void
    {
        //prepare
        $this->seed();

        Sanctum::actingAs(
            User::factory()->create(['role_id' => ADMIN]),
            ['*']
        );

        $json = [];

        //act
        $response = $this->postJson('/api/films', $json);

        //assert
        $response->assertStatus(INVALID_DATA)
                 ->assertJsonValidationErrors(['title', 'release_year', 'length', 'language_id', 'special_features']);
    }

    public function test_film_without_authentication_cannot_be_created() : void
    {
        //prepare
        $this->seed();

        $json = Film::factory()->make()->toArray();

        //act
        $response = $this->postJson('/api/films', $json);

        //assert
        $response->assertStatus(UNAUTHORIZED);
    }

    public function test_film_creation_forbidden_for_non_admin_users() : void
    {
        //prepare
        $this->seed();

        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $json = Film::factory()->make()->toArray();

        //act
        $response = $this->postJson('/api/films', $json);

        //assert
        $response->assertStatus(FORBIDDEN);
    }

    /////////////////////////////////////
    //Fin Tests pour la methode store
    ////////////////////////////////////


    /////////////////////////////////////
    //Début Tests pour la methode update
    ////////////////////////////////////

    public function test_film_update_with_valid_data() : void
    {
        //prepare
        $this->seed();

        Sanctum::actingAs(
            User::factory()->create(['role_id' => ADMIN]),
            ['*']
        );

        $film = Film::factory()->create();

        $json = [
            'title' => 'Updated Title',
            'release_year' => 2022,
            'length' => 150
        ];

        //act
        $response = $this->putJson("/api/films/{$film->id}", $json);

        //assert
        $response->assertStatus(OK)
                 ->assertJsonStructure(['message']);

        $this->assertDatabaseHas('films', [
            'id' => $film->id,
            'title' => 'Updated Title',
            'release_year' => 2022,
            'length' => 150
        ]);
    }

    public function test_film_update_requires_valid_data() : void
    {
        //prepare
        $this->seed();

        Sanctum::actingAs(
            User::factory()->create(['role_id' => ADMIN]),
            ['*']
        );

        $film = Film::factory()->create();

        $json = [
            'release_year' => 'not-an-integer',
            'length' => 'not-an-integer',
            'language_id' => 'not-an-integer'
        ];

        //act
        $response = $this->putJson("/api/films/{$film->id}", $json);

        //assert
        $response->assertStatus(INVALID_DATA)
                 ->assertJsonValidationErrors(['release_year', 'length', 'language_id']);
    }

    public function test_film_update_forbidden_for_non_admin_users() : void
    {
        //prepare
        $this->seed();

        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $film = Film::factory()->create();

        $json = [
            'title' => 'Updated Title'
        ];

        //act
        $response = $this->putJson("/api/films/{$film->id}", $json);

        //assert
        $response->assertStatus(FORBIDDEN);
    }

    public function test_film_without_authentication_cannot_be_updated() : void
    {
        //prepare
        $this->seed();

        $film = Film::factory()->create();

        $json = [
            'title' => 'Updated Title'
        ];

        //act
        $response = $this->putJson("/api/films/{$film->id}", $json);

        //assert
        $response->assertStatus(UNAUTHORIZED);
    }

    public function test_film_update_returns_not_found_for_non_existent_film() : void
    {
        //prepare
        $this->seed();

        Sanctum::actingAs(
            User::factory()->create(['role_id' => ADMIN]),
            ['*']
        );

        $nonExistentFilmId = 'not-an-id';

        $json = [
            'title' => 'Updated Title'
        ];

        //act
        $response = $this->putJson("/api/films/{$nonExistentFilmId}", $json);

        //assert
        $response->assertStatus(NOT_FOUND);
    }

    /////////////////////////////////////
    //Fin Tests pour la methode update
    ////////////////////////////////////


    /////////////////////////////////////
    //Début Tests pour la methode destroy
    ////////////////////////////////////

    public function test_film_deletion_successful() : void
    {
        //prepare
        $this->seed();

        Sanctum::actingAs(
            User::factory()->create(['role_id' => ADMIN]),
            ['*']
        );

        $film = Film::factory()->create();

        //act
        $response = $this->deleteJson("/api/films/{$film->id}");

        //assert
        $response->assertStatus(OK)
                 ->assertJsonStructure(['message']);

        $this->assertDatabaseMissing('films', [
            'id' => $film->id
        ]);
    }

    public function test_film_deletion_forbidden_for_non_admin_users() : void
    {
        //prepare
        $this->seed();

        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $film = Film::factory()->create();

        //act
        $response = $this->deleteJson("/api/films/{$film->id}");

        //assert
        $response->assertStatus(FORBIDDEN);
    }

    public function test_film_without_authentication_cannot_be_deleted() : void
    {
        //prepare
        $this->seed();

        $film = Film::factory()->create();

        //act
        $response = $this->deleteJson("/api/films/{$film->id}");

        //assert
        $response->assertStatus(UNAUTHORIZED);
    }

    public function test_film_deletion_returns_not_found_for_non_existent_film() : void
    {
        //prepare
        $this->seed();

        Sanctum::actingAs(
            User::factory()->create(['role_id' => ADMIN]),
            ['*']
        );

        $nonExistentFilmId = 'not-an-id';

        //act
        $response = $this->deleteJson("/api/films/{$nonExistentFilmId}");

        //assert
        $response->assertStatus(NOT_FOUND);
    }

    /////////////////////////////////////
    //Fin Tests pour la methode destroy
    ////////////////////////////////////

    /////////////////////////////////////
    //Début Tests de throttling
    ////////////////////////////////////

    public function test_throttling_on_film_creation_route() : void
    {
        //prepare
        $this->seed();
        $maxAttempts = 60;

        Sanctum::actingAs(
            User::factory()->create(['role_id' => ADMIN]),
            ['*']
        );

        $json = Film::factory()->make()->toArray();

        for ($i = 0; $i < $maxAttempts; $i++) {
            $response = $this->postJson('/api/films', $json);
            $response->assertStatus(CREATED);
        }

        //act
        $response = $this->postJson('/api/films', $json);

        //assert
        $response->assertStatus(TOO_MANY_REQUESTS);
    }

    public function test_throttling_on_film_update_route() : void
    {
        //prepare
        $this->seed();
        $maxAttempts = 60;

        Sanctum::actingAs(
            User::factory()->create(['role_id' => ADMIN]),
            ['*']
        );

        $film = Film::factory()->create();

        $json = [
            'title' => 'Updated Title'
        ];

        for ($i = 0; $i < $maxAttempts; $i++) {
            $response = $this->putJson("/api/films/{$film->id}", $json);
            $response->assertStatus(OK);
        }

        //act
        $response = $this->putJson("/api/films/{$film->id}", $json);

        //assert
        $response->assertStatus(TOO_MANY_REQUESTS);
    }

    public function test_throttling_on_film_deletion_route() : void
    {
        //prepare
        $this->seed();
        $maxAttempts = 60;

        Sanctum::actingAs(
            User::factory()->create(['role_id' => ADMIN]),
            ['*']
        );

        $film = Film::factory()->create();

        for ($i = 0; $i < $maxAttempts; $i++) {
            $response = $this->deleteJson("/api/films/{$film->id}");
            $response->assertStatus(OK);
            $film = Film::factory()->create();
        }

        //act
        $response = $this->deleteJson("/api/films/{$film->id}");
        //assert
        $response->assertStatus(TOO_MANY_REQUESTS);
    }

    public function test_throttling_on_film_creation_route_without_exceeding_limit() : void
    {
        //prepare
        $this->seed();
        $maxAttempts = 60;

        Sanctum::actingAs(
            User::factory()->create(['role_id' => ADMIN]),
            ['*']
        );
        $json = Film::factory()->make()->toArray();

        for ($i = 0; $i < $maxAttempts - 1; $i++) {
            $response = $this->postJson('/api/films', $json);
            $response->assertStatus(CREATED);
        }

        //act
        $response = $this->postJson('/api/films', $json);

        //assert
        $response->assertStatus(CREATED);
    }
}
