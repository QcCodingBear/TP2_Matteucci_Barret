<?php

namespace App\Repository\Eloquent;

use App\Models\ActorFilm;
use App\Repository\ActorFilmRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ActorFilmRepository extends BaseRepository implements ActorFilmRepositoryInterface
{
    public function __construct(ActorFilm $model)
    {
        parent::__construct($model);
    }

    public function getByActorId($actorId)
    {
        $actorFilms = $this->model->where('actor_id', $actorId)->get();

        if ($actorFilms->isEmpty()) {
            throw new ModelNotFoundException("No films found for actor ID {$actorId}.");
        }

        return $actorFilms;
    }

    public function getByFilmId($filmId)
    {
        $actorFilms = $this->model->where('film_id', $filmId)->get();

        if ($actorFilms->isEmpty()) {
            throw new ModelNotFoundException("No actors found for film ID {$filmId}.");
        }

        return $actorFilms;
    }
}
