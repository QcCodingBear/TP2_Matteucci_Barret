<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Auth\AuthenticationException;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/signup",
     *     tags={"Authentication"},
     *     summary="Register a new user",
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
     *             @OA\Property(property="access_token", type="string"),
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
     *     )
     * )
     */
    public function register(Request $request)
    {
        try
        {
            // Meme si pas demandé dans l'enoncé, il est préférable d'avoir un seul email par utilisateur selon moi.
            $request->validate([
                'login' => 'required|string|unique:users,login',
                'password' => 'required|string|min:8',
                'email' => 'required|string|email|unique:users,email',
                'first_name' => 'required|string',
                'last_name' => 'required|string',
            ]);

            $user = User::create([
                'login' => $request->login,
                'password' => bcrypt($request->password),
                'email' => $request->email,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
            ]);

            // Je préfére l'approche consistant à connecter directement l'utilisateur après son inscription.
            $token = $this->generateToken($user);

            return response()->json([
                'message' => 'User created successfully',
                'access_token' => $token,
                'token_type' => 'Bearer',

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
     *     )
     * )
    */
    public function login(Request $request)
    {
        try
        {
            $request->validate([
                'login' => 'required|string',
                'password' => 'required|string',
            ]);

            $credentials = $request->only('login', 'password');

            if (!auth()->attempt($credentials)) throw new AuthenticationException();

            $token = $this->generateToken(auth()->user());

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer'], OK);
        }
        catch (AuthenticationException $e)
        {
            return response()->json(['message' => 'Authentication failed'], UNAUTHORIZED);
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
     *     )
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
