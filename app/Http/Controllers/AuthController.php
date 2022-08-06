<?php

namespace App\Http\Controllers;

use App\Helpers\Functions;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function Register(Request $request)
    {
        $fields = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);

        $user = User::create([
            'first_name' => $fields['first_name'],
            'last_name' => $fields['last_name'],
            'email' => $fields['email'],
            'is_admin' => false,
            'password' => bcrypt($fields['password'])
        ]);

//        $token = $user->createToken('auth-token', ['is_admin'])->plainTextToken;

        $token = $user->createToken('auth-token')->plainTextToken;

        $response = [
            'user' => ['first_name' => $user->first_name, 'last_name' => $user->last_name, 'email' => $user->email],
            'token' => $token,
        ];

        return response()->json($response, 201);
    }

    public function Login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $fields['email'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password))
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);

        $token = $user->createToken('auth-token')->plainTextToken;

        $response = [
            'user' => ['first_name' => $user->first_name, 'last_name' => $user->last_name, 'email' => $user->email, 'is_admin' => $user->is_admin == true],
            'token' => $token
        ];

        return response()->json($response);
    }

    public function Logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
