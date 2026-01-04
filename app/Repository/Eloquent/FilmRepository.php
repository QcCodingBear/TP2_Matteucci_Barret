<?php

namespace App\Repository\Eloquent;

use App\Models\Film;
use App\Repository\FilmRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;

class FilmRepository extends BaseRepository implements FilmRepositoryInterface
{
    public function __construct(Film $model)
    {
        parent::__construct($model);
    }

    public function getByTitle($title)
    {
        $film = $this->model->where('title', $title)->first();

        if (!$film) {
            throw new ModelNotFoundException("Film with title {$title} not found.");
        }

        return $film;
    }

    public function getByReleaseYear($releaseYear)
    {
        $film = $this->model->where('release_year', $releaseYear)->first();

        if (!$film) {
            throw new ModelNotFoundException("Film with release year {$releaseYear} not found.");
        }

        return $film;
    }

    public function getByLanguageId($languageId)
    {
        $film = $this->model->where('language_id', $languageId)->first();

        if (!$film) {
            throw new ModelNotFoundException("Film with language ID {$languageId} not found.");
        }

        return $film;
    }

    public function getByDuration($duration)
    {
        $film = $this->model->where('duration', $duration)->first();

        if (!$film) {
            throw new ModelNotFoundException("Film with duration {$duration} not found.");
        }

        return $film;
    }

    public function getByRating($rating)
    {
        $film = $this->model->where('rating', $rating)->first();

        if (!$film) {
            throw new ModelNotFoundException("Film with rating {$rating} not found.");
        }

        return $film;
    }

    public function getByDescription($description)
    {
        $film = $this->model->where('description', $description)->first();

        if (!$film) {
            throw new ModelNotFoundException("Film with description {$description} not found.");
        }

        return $film;
    }

    public function getBySpecialFeatures($specialFeatures)
    {
        $film = $this->model->where('special_features', $specialFeatures)->first();

        if (!$film) {
            throw new ModelNotFoundException("Film with special features {$specialFeatures} not found.");
        }

        return $film;
    }
    
}
