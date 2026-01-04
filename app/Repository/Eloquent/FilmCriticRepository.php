<?php

namespace App\Repository\Eloquent;

use App\Models\FilmCritic;
use App\Repository\FilmCriticRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FilmCriticRepository extends BaseRepository implements FilmCriticRepositoryInterface
{
    public function __construct(FilmCritic $model)
    {
        parent::__construct($model);
    }

    public function getByFilmId($filmId)
    {
        $filmCritics = $this->model->where('film_id', $filmId)->get();

        if ($filmCritics->isEmpty()) {
            throw new ModelNotFoundException("No film critics found for film ID {$filmId}.");
        }

        return $filmCritics;
    }
}
