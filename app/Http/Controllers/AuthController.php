<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    

    public function register(Request $request){

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:5'
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password'])
        ]);

        return $user;
    }

    public function login(Request $request){

        $validatedData = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string|min:5'
        ]);

        if(!Auth::attempt(['email'=>$validatedData['email'], 'password'=>$validatedData['password']])){
            return response()->json(['message'=>'invalid email or password'],401);
        }

        $user = User::where('email',$request['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['access_token'=>$token,'token_type'=>'Bearer'],201);
    }

    public function logout(Request $request){
        $user = User::where('email',$request['email'])->firstOrFail();

        $user->tokens()->delete();

        return response()->json(['message'=>'Token was revoked']);
    }
}
