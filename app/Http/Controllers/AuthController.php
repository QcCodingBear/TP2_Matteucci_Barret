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
                'token_type' => 'Bearer'
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
