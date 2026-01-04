<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\FilmResource;
use App\Http\Resources\ActorResource;
use App\Models\ActorFilm;
use App\Models\Actor;
use App\Models\Film;
use App\Repository\ActorFilmRepositoryInterface;

class ActorFilmController extends Controller
{
   private ActorFilmRepositoryInterface $actorFilmRepository;

    public function __construct(ActorFilmRepositoryInterface $actorFilmRepository)
    {
         $this->actorFilmRepository = $actorFilmRepository;
    }

    public function getByActorId($actorId)
    {
        $actorFilms = $this->actorFilmRepository->getByActorId($actorId);
        return FilmResource::collection($actorFilms);
    }
    public function getByFilmId($filmId)
    {
        $actorFilms = $this->actorFilmRepository->getByFilmId($filmId);
        return ActorResource::collection($actorFilms);
    }
}
