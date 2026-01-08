<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\FilmResource;
use App\Models\Film;
use App\Repository\FilmRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFilmRequest;
use App\Http\Requests\UpdateFilmRequest;

class FilmController extends Controller
{
    private FilmRepositoryInterface $filmRepository;

    public function __construct(FilmRepositoryInterface $filmRepository)
    {
        $this->filmRepository = $filmRepository;
    }


/**
    * @OA\Post(
    *     path="/api/films",
    *     tags={"Films"},
    *     summary="Create a new film",
    *     description="Creates a new film with the provided details. Admin access required. Throttling: 60 requests per minute.",
    *     security={{"bearerAuth":{}}},
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(
    *         required={"title","release_year","language_id","rating"},
    *         @OA\Property(property="title", type="string", example="The Matrix"),
    *         @OA\Property(property="release_year", type="integer", example=1999),
    *         @OA\Property(property="length", type="integer", example=136, description="Duration in minutes"),
    *         @OA\Property(property="description", type="string", example="A computer hacker learns from mysterious rebels about the true nature of his reality."),
    *         @OA\Property(property="rating", type="string", example="PG-13", description="Film rating"),
    *         @OA\Property(property="language_id", type="integer", example=1),
    *         @OA\Property(property="special_features", type="string", example="Trailers,Deleted Scenes"),
    *         @OA\Property(property="image", type="string", example="matrix.jpg")
    *         )
    *     ),
    *     @OA\Response(
    *         response=201,
    *         description="Film created successfully",
    *         @OA\JsonContent(
    *         @OA\Property(property="message", type="string", example="Film created successfully"),
    *             @OA\Property(property="data", type="object",
    *                 @OA\Property(property="id", type="integer", example=1),
    *                 @OA\Property(property="title", type="string", example="The Matrix"),
    *                 @OA\Property(property="release_year", type="integer", example=1999)
    *         )
    *       )
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
    *         description="Access denied - Admin access required",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="Access denied. Admins only.")
    *         )
    *     ),
    *     @OA\Response(
    *         response=422,
    *         description="Invalid input data",
    *         @OA\JsonContent(
    *             @OA\Property(property="errors", type="object")
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
    public function store(StoreFilmRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $film = $this->filmRepository->create($validatedData);

            return response()->json([
                'message' => 'Film created successfully',
                'film' => new FilmResource($film)
            ], CREATED);
        }
        catch (ValidationException $e)
        {
            return response()->json([
                'errors' => $e->errors(),
                'message' => 'Invalid data'],
                INVALID_DATA);
        }
        catch (Exception $e) {

            return response()->json(['message' => 'Film creation failed',], SERVER_ERROR);
        }
    }



    /**
    * @OA\Put(
    *     path="/api/films/{id}",
    *     tags={"Films"},
    *     summary="Update an existing film (full update)",
    *     description="Updates all information of an existing film. Admin access required. Throttling: 60 requests per minute.",
    *     security={{"bearerAuth":{}}},
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         required=true,
    *         description="ID of the film to update",
    *         @OA\Schema(type="integer", example=1)
    *     ),
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(
    *             required={"title","release_year","language_id","rating"},
    *             @OA\Property(property="title", type="string", example="The Matrix Reloaded"),
    *             @OA\Property(property="release_year", type="integer", example=2003),
    *             @OA\Property(property="length", type="integer", example=138),
    *             @OA\Property(property="description", type="string", example="Neo and his allies race against time before the machines discover the city of Zion."),
    *             @OA\Property(property="rating", type="string", example="R"),
    *             @OA\Property(property="language_id", type="integer", example=1),
    *             @OA\Property(property="special_features", type="string", example="Trailers,Commentaries"),
    *             @OA\Property(property="image", type="string", example="matrix2.jpg")
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Film updated successfully",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="Film updated successfully"),
    *             @OA\Property(property="data", type="object")
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
    *         description="Access denied - Admin access required",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="Access denied. Admins only.")
    *         )
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="Film not found",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="Film not found")
    *         )
    *     ),
    *     @OA\Response(
    *         response=422,
    *         description="Invalid input data",
    *         @OA\JsonContent(
    *             @OA\Property(property="errors", type="object")
    *         )
    *     )
    * )
    */
    public function update(UpdateFilmRequest $request, $id)
    {
        try {
            $film = $this->filmRepository->getById($id);
            if (!$film) {
                return response()->json([
                    'message' => 'Film not found',
                ], NOT_FOUND);
            }

            $validatedData = $request->validated();

            $film = $this->filmRepository->update($id, $validatedData);

            return response()->json([
                'message' => 'Film updated successfully',
                'film' => new FilmResource($film)
            ], OK);
        }
        catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
                'message' => 'Invalid data'],
                INVALID_DATA);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Film update failed',
            ], SERVER_ERROR);
        }
    }


    /**
    * @OA\Delete(
    *     path="/api/films/{id}",
    *     tags={"Films"},
    *     summary="Delete a film",
    *     description="Deletes a film and manages related data (actors, critics). Admin access required. Throttling: 60 requests per minute.",
    *     security={{"bearerAuth":{}}},
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         required=true,
    *         description="ID of the film to delete",
    *         @OA\Schema(type="integer", example=1)
    *     ),
    *     @OA\Response(
    *         response=204,
    *         description="Film deleted successfully (No Content)"
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Film deleted successfully",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="Film deleted successfully")
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
    *         description="Access denied - Admin access required",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="Access denied. Admins only.")
    *         )
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="Film not found",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="Film not found")
    *         )
    *     )
    * )
    */
    public function destroy($id)
    {
        try {
            $film = $this->filmRepository->getById($id);
            if (!$film) {
                return response()->json([
                    'message' => 'Film not found',
                ], NOT_FOUND);
            }
            $this->filmRepository->delete($id);

            return response()->json([
                'message' => 'Film deleted successfully',
            ], OK);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Film deletion failed',
            ], SERVER_ERROR);
        }
    }
}

