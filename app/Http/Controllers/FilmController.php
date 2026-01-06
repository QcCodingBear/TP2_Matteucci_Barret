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

