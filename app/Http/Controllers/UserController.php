<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;

use  Validator;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function register(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required'
        ]);

        if ($validator->fails()) {
          return response()->json($validator->errors(), 400);
        }

         $user= User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=> Hash::make($request->password)
        ]);

        return response()->json([
            "message"=>'Create user Sussfull',
            'user'=>$user
        ] ,200);
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',

        ]);

        if ($validator->fails()) {
          return response()->json($validator->errors(), 400);
        }

        if(!$token = auth()->attempt($validator->validated())){
            return response()->json([
                'error'=>'Unauthoriseion'
            ], 401);
        }

        return $this->respondWithToken($token);

    }


    protected function respondWithToken($token){
        return response()->json([
            'access_token'=>$token,
            'token_type'=>'bearer',
            'expires_in'=>auth()->factory()->getTTL()*60

        ]);
    }

    public function profile(){
        return response()->json( auth()->user(), 200);
    }

    public function refresh(){
        return $this->respondWithToken(auth()->refresh());
    }
    public function logout(){
         auth()->logout();
        return response()->json(['message'=>'User Logout']);
    }


}
