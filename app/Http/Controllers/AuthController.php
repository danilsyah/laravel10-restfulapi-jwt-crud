<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User; // memanggil model
use Illuminate\Support\Facades\Auth; // memanggil class Auth
use Illuminate\Support\Facades\Hash; // memanggil class Hash untuk enkrip
use Illuminate\Support\Facades\Validator; // memanggil class validator

class AuthController extends Controller
{
    public function register(Request $request){
        // validasi request
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        // jika ada validasi yang error kita akan berikan pesan error
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        // mengambil array data request yang sudah validated
        $validatedData = $validator->validated();

        // enkrip password dengan hash
        $validatedData['password'] = Hash::make($request['password']);

        // Insert user ke table users
        $user = User::create($validatedData);

        // create token
        $token = Auth::attempt($request->only(['email', 'password']));

        return $this->createNewToken($token);
    }

    public function login(Request $request){
        $credentials = $request->only(['email','password']);

        if(!$token = Auth::attempt($credentials)){
            return response()->json(['message' => 'Wrong credentials'], 401);
        }

        return $this->createNewToken($token);
    }

    public function refresh(){
        $token = auth()->refresh();
        return $this->createNewToken($token);
    }

    public function me(){
        return response()->json(auth()->user());
    }

    protected function createNewToken($token){
        return response()->json([
            'access_token'=>$token,
            'token_type'=>'Bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);

    }
}
