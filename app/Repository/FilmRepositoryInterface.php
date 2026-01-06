<?php

namespace App\Repository;

interface FilmRepositoryInterface extends RepositoryInterface
{
    public function getByTitle($title);
    public function getByReleaseYear($releaseYear);
    public function getByRating($rating);
    public function getByLanguageId($languageId);
    public function getByLength($length);
    public function getBySpecialFeatures($specialFeatures);
    public function getByDescription($description);
}
