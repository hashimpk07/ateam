<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Hash; 

class ApiAuthController extends Controller
{
   
    public function register(Request $request)
    {
        try{
            $fields = $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|unique:users,email',
                'password' => 'required|string',
            ]);

            $user = User::create([
                'name' => $fields['name'],
                'email' => $fields['email'],
                'password' => bcrypt($fields['password']),
            ]);

            $token = $user->createToken('myapptoken')->plainTextToken;

            $response = [
                'user' => $user,
                'token' => $token
            ];

            return response($response, 201);
        }catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status'=>'error']);
        } 
       
    }

    public function login(Request $request)
    {
        try{
            $fields = $request->validate([
                'email' => 'required|string',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $fields['email'])->first();

            if(!$user || !Hash::check($fields['password'], $user->password)){
                return response([
                    'message' => 'Bad Credentials',
                ], 401);
            }

            $token = $user->createToken('myapptoken')->plainTextToken;

            $response = [
                'user' => $user,
                'token' => $token
            ];

            return response($response, 201);
        }catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status'=>'error']);
        } 
    }
    public function logout(Request $request)
    {
        try{
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Logged out successfully']);
        }catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status'=>'error']);
        } 
    }
}
