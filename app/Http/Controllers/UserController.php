<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Resources\LanguageResource;
use App\Models\User;
use App\Models\Language;
use Illuminate\Support\Facades\Validator;
use App\Repository\UserRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use App\Http\Requests\UpdatePasswordRequest;

class UserController extends Controller
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getByEmail($email)
    {
        $user = $this->userRepository->getByEmail($email);
        return new UserResource($user);
    }

    public function getById($id)
    {
        try
        {
            $user = $this->userRepository->getById($id);

            if(auth()->user()->id != $id)
            {
                return response()->json(['message' => 'You can only view your own informations.'], FORBIDDEN);
            }

            return response()->json(['data' => new UserResource($user)], OK);
        }
        catch (ModelNotFoundException $e)
        {
            return response()->json([
                'message' => 'User not found.'], NOT_FOUND);
        }
        catch (Exception $e)
        {
            return response()->json([
                'message' => 'An error occurred while retrieving the user.',
                'error' => $e->getMessage()], SERVER_ERROR);
        }
    }

    public function updatePassword(UpdatePasswordRequest $request, $id)
    {
        try
        {
            $this->userRepository->getById($id);

            if (auth()->user()->id != $id) {
            return response()->json([
                'message' => 'You can only update your own password.'], FORBIDDEN);
            }

            $validatedData = $request->validated();

            $newPassword = $validatedData['password'];

            $this->userRepository->updatePassword($id, $newPassword);

            return response()->json(['message' => 'Password updated successfully.'], OK);
        }
        catch (ModelNotFoundException $e)
        {
            return response()->json([
                'message' => 'User not found.'], NOT_FOUND);
        }
        catch (Exception $e)
        {
            return response()->json([
                'message' => 'An error occurred while updating the password.',
                'error' => $e->getMessage()], SERVER_ERROR);
        }
    }
}
