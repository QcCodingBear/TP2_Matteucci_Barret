<?php

namespace App\Repository;

interface CriticRepositoryInterface extends RepositoryInterface
{
    public function getByUserId($userId);
    public function getByFilmId($filmId);
    public function userHasCriticForFilm($userId, $filmId);
}
