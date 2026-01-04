<?php

namespace App\Repository;

interface FilmRepositoryInterface extends RepositoryInterface
{
    public function getByTitle($title);
}
