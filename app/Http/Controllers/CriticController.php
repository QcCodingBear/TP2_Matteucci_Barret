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


    /**
    * @OA\Post(
    *     path="/api/critics",
    *     tags={"Critics"},
    *     summary="Create a film critic",
    *     description="Allows an authenticated user to create a critic for a film. A user can only create one critic per film. Throttling: 60 requests per minute.",
    *     security={{"bearerAuth":{}}},
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(
    *             required={"film_id","score","comment"},
    *             @OA\Property(property="film_id", type="integer", example=1, description="ID of the film to review"),
    *             @OA\Property(property="score", type="number", format="float", example=8.5, description="Score between 0 and 10"),
    *             @OA\Property(property="comment", type="string", example="Great movie with exceptional direction!", description="Comment (max 1000 characters)")
    *         )
    *     ),
    *     @OA\Response(
    *         response=201,
    *         description="Critic created successfully",
    *         @OA\JsonContent(
    *             @OA\Property(property="data", type="object",
    *                 @OA\Property(property="score", type="number", example=8.5),
    *                 @OA\Property(property="comment", type="string", example="Great movie with exceptional direction!"),
    *                 @OA\Property(property="film_id", type="integer", example=1),
    *                 @OA\Property(property="user_id", type="integer", example=4)
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=401,
    *         description="Unauthenticated",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="Unauthenticated.")
    *         )
    *     ),
    *     @OA\Response(
    *         response=403,
    *         description="User has already submitted a critic for this film",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="User has already submitted a critic for this film.")
    *         )
    *     ),
    *     @OA\Response(
    *         response=422,
    *         description="Invalid input data",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="The given data was invalid."),
    *             @OA\Property(property="errors", type="object",
    *                 @OA\Property(property="film_id", type="array", @OA\Items(type="string", example="The selected film id is invalid.")),
    *                 @OA\Property(property="score", type="array", @OA\Items(type="string", example="The score field must not be greater than 10."))
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=429,
    *         description="Too many requests (throttling: 60/min)",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="Too Many Attempts.")
    *         )
    *     )
    * )
    */
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
