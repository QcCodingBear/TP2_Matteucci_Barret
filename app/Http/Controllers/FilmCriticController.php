<?php

namespace App\Http\Controllers;
use App\Http\Resources\CriticResource;
use App\Models\Film;
use App\Repository\FilmCriticRepositoryInterface;

use Illuminate\Http\Request;

class FilmCriticController extends Controller
{
    private FilmCriticRepositoryInterface $filmCriticRepository;

    public function __construct(FilmCriticRepositoryInterface $filmCriticRepository)
    {
        $this->filmCriticRepository = $filmCriticRepository;
    }

    public function getByFilmId($filmId)
    {
        $filmCritics = $this->filmCriticRepository->getByFilmId($filmId);
        return CriticResource::collection($filmCritics);
    }
}
