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

            $criticData = [
            'user_id' => auth()->user()->id,
            'film_id' => $validatedData['film_id'],
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
