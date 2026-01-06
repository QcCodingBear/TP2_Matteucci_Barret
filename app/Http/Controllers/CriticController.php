<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Critic;
use App\Http\Resources\CriticResource;
use App\Repository\CriticRepositoryInterface;
use App\Http\Requests\StoreCriticRequest;
use Exception;

class CriticController extends Controller
{
   private CriticRepositoryInterface $criticRepository;

    public function __construct(CriticRepositoryInterface $criticRepository)
    {
         $this->criticRepository = $criticRepository;
    }

    public function store (StoreCriticRequest $request)
    {
        try
        {
            $validatedData = $request->validated();

            $userId = auth()->user()->id;
            $filmId = $validatedData['film_id'];

            if ($this->criticRepository->userHasCriticForFilm($userId, $filmId)) {
                return response()->json(['message' => 'User has already submitted a critic for this film.'], FORBIDDEN);
            }

            $criticData = [
            'user_id' => $userId,
            'film_id' => $filmId,
            'score' => $validatedData['score'],
            'comment' => $validatedData['comment'],
        ];

        $critic = $this ->criticRepository->create($criticData);

        return response()->json([
            'data' => new CriticResource($critic),
            'message' => 'Critic added successfully'],
            CREATED);
        }

        catch (Exception $e)
        {
            return response()->json([
                'message' => 'An error occurred while creating the critic.',
                'error' => $e->getMessage()], SERVER_ERROR);
        }
    }
}
