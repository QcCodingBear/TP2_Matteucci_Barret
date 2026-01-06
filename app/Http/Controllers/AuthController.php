<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Auth\AuthenticationException;
use App\Repository\UserRepositoryInterface;
use App\Http\Requests\RegisterAuthRequest;
use App\Http\Requests\LoginAuthRequest;

class AuthController extends Controller
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @OA\Post(
     *     path="/api/signup",
     *     tags={"Authentication"},
     *     summary="Register a new user",
     *     description="Creates a new user account with the provided details. Throttling applied: max 5 attempts per minute.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"login","password","email","first_name","last_name"},
     *             @OA\Property(property="login", type="string", example="newuser"),
     *             @OA\Property(property="password", type="string", example="securepassword"),
     *             @OA\Property(property="email", type="string", format="email", example="test@test.com"),
     *             @OA\Property(property="first_name", type="string", example="John"),
     *             @OA\Property(property="last_name", type="string", example="Snow")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User created successfully"),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
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
     *         response=500,
     *         description="User registration failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User registration failed")
     *         )
     *     ),
     *    @OA\Response(
     *         response=429,
     *        description="Too many requests",
     *        @OA\JsonContent(
     *            @OA\Property(property="message", type="string", example="Too many requests")
     *        )
     *     )
     * )
     */
    public function register(RegisterAuthRequest $request)
    {
        try
        {
            $validatedData = $request->validated();
            $validatedData['password'] = bcrypt($validatedData['password']);
            $validatedData['role_id'] = USER; // Par défaut, le rôle 'User' a l'ID 2 (User)

            $user = $this->userRepository->create($validatedData);

            return response()->json([
                'message' => 'User created successfully'
            ], CREATED);
        }
        catch (ValidationException $e)
        {
            return response()->json(['errors' => $e->errors()], INVALID_DATA);
        }
        catch (Exception $e)
        {
            return response()->json(['message' => 'User registration failed'], SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/signin",
     *     tags={"Authentication"},
     *     summary="Authenticate a user and obtain an access token",
     *    description="Authenticates a user using their login and password, returning an access token upon successful authentication. Throttling applied: max 5 attempts per minute.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"login","password"},
     *             @OA\Property(property="login", type="string", example="existinguser"),
     *             @OA\Property(property="password", type="string", example="securepassword")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Authentication successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Authentication failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Authentication failed")
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
     *         response=500,
     *         description="Login failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Login failed")
     *         )
     *     ),
     *    @OA\Response(
     *         response=429,
     *        description="Too many requests",
     *       @OA\JsonContent(
     *           @OA\Property(property="message", type="string", example="Too many requests")
     *       )
     *     )
     * )
    */
    public function login(LoginAuthRequest $request)
    {
        try
        {
            $validatedData = $request->validated();

            if (!auth()->attempt($validatedData))
                return response()->json(['message' => 'Authentication failed'], UNAUTHORIZED);

            $token = $this->generateToken(auth()->user());

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer'], OK);
        }
        catch (ValidationException $e)
        {
            return response()->json(['errors' => $e->errors()], INVALID_DATA);
        }
        catch (Exception $e)
        {
            return response()->json(['message' => 'Login failed'], SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/signout",
     *     tags={"Authentication"},
     *     summary="Logout the authenticated user",
     *     security={{"bearerAuth": {}}},
     *    description="Logs out the currently authenticated user by revoking their access token. Requires a valid bearer token in the Authorization header.",
     *     @OA\Response(
     *         response=204,
     *         description="Logged out successfully"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Logout failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logout failed")
     *         )
     *     ),
     *    @OA\Response(
     *        response=429,
     *       description="Too many requests",
     *       @OA\JsonContent(
     *          @OA\Property(property="message", type="string", example="Too many requests")
     *      )
     *    )
     * )
    */
    public function logout(Request $request)
    {
        try
        {
            $request->user()->currentAccessToken()->delete();

            return response()->json(['message' => 'Logged out successfully'], NO_CONTENT);
        }
        catch (Exception $e)
        {
            return response()->json(['message' => 'Logout failed'], SERVER_ERROR);
        }
    }

    private function generateToken(User $user)
    {
        return $user->createToken('auth_token')->plainTextToken;
    }
}
