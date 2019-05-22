<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //

    public function user()
    {
        return response()->json(['user' => auth()->user()], 200);
    }

    public function login(Request $request)
    {
        $credentials = [
            'username' => $request->username,
            'password' => $request->password
        ];
 
        if (auth()->attempt($credentials)) {
            $token = auth()->user()->createToken('TodosToken')->accessToken;
            return response()->json([
                'token' => $token,
                'user' => auth()->user()
            ], 200);
        } else {
            return response()->json(['error' => 'UnAuthorised'], 401);
        }
    }

    public function logout()
    {
        auth()->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function register( Request $request ) {
        $validator = Validator::make( $request->toArray(), [
            'username' => 'unique:users,username'
        ]);
        if( $validator->fails() ) {
            return response()->json(["response" => "exists"], 422);
        }
        $request["password"] = Hash::make( $request["password"] );
        $user = User::create( $request->toArray() );
        $token = $user->createToken('TodosToken')->accessToken;
        unset( $request["password"] );
        return response()->json(["response" => "success", "user" => $request->all(), "token" => $token]);
    }
}
