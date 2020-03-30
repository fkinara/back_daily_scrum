<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller 
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        return response()->json([compact('token')]);

    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Firstname'         => 'required|string|max:255',
            'Lastname'          => 'required|string|max:255',
			'email'             => 'required|string|email|max:100|unique:users',
            'password'          => 'required|string|min:6',
            'password_verify'   => 'required|string|min:6',
		]);
		if($validator->fails()){
			return response()->json($validator->errors()->toJson(), 400);
        }
        $user = User::create([
            'Firstname' => $request->get('Firstname'),
            'Lastname' => $request->get('Lastname'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'password_verify' => $request->get('password'),
        ]);
        $token = JWTAuth::fromUser($user);
        return response()->json(compact('user', 'token'), 201);
    }   

    
    public function getAuthenticatedUser()
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 400);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json([token_expired], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvaliddException $e) {
            return response()->json([token_invalid], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([token_absent], $e->getStatusCode());
        } 
        return response()->json(compact('user'));
    }
}