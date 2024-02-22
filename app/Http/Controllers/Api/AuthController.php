<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        //validate the request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        //login the user
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        if(!Hash::check($request->password, $user->password)){
            return response()->json([
                'status' => 'error',
                'message' => 'The credentials no match'
            ], 401);
        }

        //generate token
        $token = $user->createToken('token-name')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'user' => $user,
        ], 200);

    }
}
