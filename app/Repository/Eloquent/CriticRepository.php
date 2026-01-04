<?php

namespace App\Repository\Eloquent;

use App\Models\Critic;
use App\Repository\CriticRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CriticRepository extends BaseRepository implements CriticRepositoryInterface
{
    public function __construct(Critic $model)
    {
        parent::__construct($model);
    }

    public function getByUserId($userId)
    {
        $critic = $this->model->where('user_id', $userId)->get();

        if (!$critic) {
            throw new ModelNotFoundException("Critic with user ID {$userId} not found.");
        }

        return $critic;
    }

    public function getByFilmId($filmId)
    {
        $critic = $this->model->where('film_id', $filmId)->get();

        if (!$critic) {
            throw new ModelNotFoundException("Critic with film ID {$filmId} not found.");
        }

        return $critic;
    }

    public function userHasCriticForFilm($userId, $filmId)
    {
        return $this->model->where('user_id', $userId)
                           ->where('film_id', $filmId)
                           ->exists();
    }
}
