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


    /**
 * @OA\Get(
 *     path="/api/users/{id}",
 *     tags={"Users"},
 *     summary="Get user information",
 *     description="Allows an authenticated user to view their own information only. Cannot view other users' information. Throttling: 60 requests per minute.",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID (must match the authenticated user's ID)",
 *         @OA\Schema(type="integer", example=4)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User information retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=4),
 *                 @OA\Property(property="login", type="string", example="johndoe"),
 *                 @OA\Property(property="email", type="string", example="john@example.com"),
 *                 @OA\Property(property="last_name", type="string", example="Doe"),
 *                 @OA\Property(property="first_name", type="string", example="John"),
 *                 @OA\Property(property="role_id", type="integer", example=1)
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
 *         description="Access denied - You can only view your own information",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="You can only view your own information.")
 *         )
 *     ),
    *     @OA\Response(
    *         response=404,
    *         description="User not found",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="User not found.")
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


    /**
    * @OA\Patch(
    *     path="/api/users/{id}/password",
    *     tags={"Users"},
    *     summary="Update user password",
    *     description="Allows an authenticated user to change their own password. Requires password confirmation. User cannot change another user's password. Throttling: 60 requests per minute.",
    *     security={{"bearerAuth":{}}},
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         required=true,
    *         description="User ID (must match the authenticated user's ID)",
    *         @OA\Schema(type="integer", example=4)
    *     ),
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(
    *             required={"password","password_confirmation"},
    *             @OA\Property(property="password", type="string", format="password", minLength=8, example="newpassword123", description="New password (minimum 8 characters)"),
    *             @OA\Property(property="password_confirmation", type="string", format="password", example="newpassword123", description="Password confirmation (must match password)")
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Password updated successfully",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="Password updated successfully.")
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
    *         description="Access denied - You can only update your own password",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="You can only update your own password.")
    *         )
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="User not found",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="User not found.")
    *         )
    *     ),
    *     @OA\Response(
    *         response=422,
    *         description="Invalid input data",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="The given data was invalid."),
    *             @OA\Property(property="errors", type="object",
    *                 @OA\Property(property="password", type="array", @OA\Items(type="string", example="The password field must be at least 8 characters.")),
    *                 @OA\Property(property="password_confirmation", type="array", @OA\Items(type="string", example="The password confirmation does not match."))
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
    //https://stackoverflow.com/questions/47936337/how-do-i-get-only-the-validated-data-from-a-laravel-formrequest => permet de retoruner uniquement les donnés validées par la request

}
