<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\FilmResource;
use App\Models\Film;
use App\Repository\FilmRepositoryInterface;
use App\Http\Controllers\Controller;

class FilmController extends Controller
{
    private FilmRepositoryInterface $filmRepository;

    public function __construct(FilmRepositoryInterface $filmRepository)
    {
        $this->filmRepository = $filmRepository;
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'release_year' => 'required|integer',
                'length' => 'required|integer',
                'description' => 'nullable|string',
                'rating' => 'nullable|string|max:10',
                'language_id' => 'required|integer|exists:languages,id',
                'special_features' => 'required|string',
                'image' => 'nullable|string|max:255',
            ]);

            $film = $this->filmRepository->create($validatedData);

            return (new FilmResource($film))->response()
                ->setStatusCode(CREATED);
        }
        catch (ValidationException $e) {
            return response()->json([
                'invalid data' => $e->errors(),
            ], INVALID_DATA);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Film creation failed',
            ], SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $film = $this->filmRepository->getById($id);
            if (!$film) {
                return response()->json([
                    'message' => 'Film not found',
                ], NOT_FOUND);
            }

            $validatedData = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'release_year' => 'sometimes|required|integer',
                'length' => 'sometimes|required|integer',
                'description' => 'sometimes|nullable|string',
                'rating' => 'sometimes|nullable|string|max:10',
                'language_id' => 'sometimes|required|integer|exists:languages,id',
                'special_features' => 'sometimes|nullable|string',
                'image' => 'sometimes|nullable|string|max:255',
            ]);

            $film = $this->filmRepository->update($id, $validatedData);

            return (new FilmResource($film))->response()
                ->setStatusCode(OK);
        }
        catch (ValidationException $e) {
            return response()->json([
                'invalid data' => $e->errors(),
            ], INVALID_DATA);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Film update failed',
            ], SERVER_ERROR);
        }
    }

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

