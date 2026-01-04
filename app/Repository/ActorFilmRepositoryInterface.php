<?php

namespace App\Repository;

interface ActorFilmRepositoryInterface extends RepositoryInterface
{
    public function getByActorId($actorId);
    public function getByFilmId($filmId);
}
