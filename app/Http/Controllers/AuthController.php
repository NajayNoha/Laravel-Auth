<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try{
            $validateData = Validator::make($request->all(), [
                "email"=> "required|email",
                "password" => "required"
            ]);

            if(!Auth::attempt($request->only(['email', 'password']))){
                return response()->json([
                    'status' => false,
                    'message' => __('auth.failed'),
                    'code' => 'INVALID_CREDENTIALS'
                ], 401);
            }

            $user = User::where('email', $request->email)->first();
            $success = response()->json([
                "status" => true,
                "message" => "User succefully Logged",
                "token"=> $user->createToken('API TOKEN')->plainTextToken,
                'data' => [
                    "user" => $user,
                ]
            ], 200);

            $faild = response()->json([
                'status' => false ,
                "message" => "user login failed",
                "errors" => "server error "
            ], 401);

            return $user->save() ? $success : $faild;

        }catch(\Throwable $th)
        {
            return response()->json([
                'status' => false ,
                "message" => "server error",
                "errors" => $th->getMessage()
            ], 401);
        }
    }
    public function register(Request $request)
    {
        try{
            $validateData = Validator::make($request->all(), [
                "name" => "required|string",
                "email"=> "required|email",
                "password" => "required"
            ]);

            if($validateData->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'data validation fails',
                    'errors' =>$validateData->errors()
                ], 401);
            }

            $user = User::create([
                "name"=> $request->name,
                "email"=> $request->email,
                "password"=>Hash::make($request->password),
            ]);
            $success = response()->json([
                "status" => true,
                "message" => "User created succefully",
                "token"=> $user->createToken('API TOKEN')->plainTextToken,
                'data' => [
                    "user" => $user,
                ]
            ], 200);

            $faild = response()->json([
                'status' => false ,
                "message" => "user register failed",
                "errors" => "server error "
            ], 401);

            return $user->save() ? $success : $faild;

        }catch(\Throwable $th)
        {
            return response()->json([
                'status' => false ,
                "message" => "server error",
                "errors" => $th->getMessage()
            ], 401);
        }
    }
}
