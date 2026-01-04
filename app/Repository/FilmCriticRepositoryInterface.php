<?php

namespace App\Repository;

interface FilmCriticRepositoryInterface extends RepositoryInterface
{
    public function getByFilmId($filmId);
}
