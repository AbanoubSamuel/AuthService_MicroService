<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AuthController extends Controller {

    public function register(Request $request): JsonResponse
    {

        $email = $request->input('email');

        $userExists = User::where('email', $email)->first();

        if ($userExists) {
            return response()->json([
                'status' => false,
                'message' => 'User already registered, please login'],
                409);
        }

        $user = new User([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'player_id' => uniqid('pl_'),
            'password' => Hash::make($request->input('password')),
        ]);

        // Save the user to the database
        $user->save();


        return response()->json([
            'status' => true,
            'message' => 'User created successfully'],
            201);
    }

    public function login(Request $request): JsonResponse
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Attempt to authenticate the user
        if (Auth::attempt($validatedData)) {
            // Authentication successful
            // Generate a new API token (if using API tokens)
            $user = User::where('email', $request->input('email'))->first();
            $user->authToken = $user->createToken('auth-token')->plainTextToken;
            // Return a response or perform any additional actions
            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'user' => $user],
                status: ResponseAlias::HTTP_OK);
        } else {
            // Authentication failed
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials'],
                ResponseAlias::HTTP_UNAUTHORIZED);
        }
    }

    public function getLoggedInUser(Request $request)
    {
        $user = auth()->user();
        if ($user) {
            return response()->json([
                'status' => true,
                'message' => 'User fetched successfully',
                'data' => $user
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }
    }
}
