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
}
