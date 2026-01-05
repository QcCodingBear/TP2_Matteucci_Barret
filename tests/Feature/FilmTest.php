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


}
