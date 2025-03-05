<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data=$request->all();
        $validater=Validator::make($data,['email'=>'required|email|unique:users,email',
                                    'name'=>'required',
                                    'password'=>'required|min:6|confirmed'],
                                    ['email.required'=>'please enter email',
                                    'name.required'=>'please enter name',
                                    'password.required'=>'please enter password']);
        
        if($validater->fails())
        {
            return response()->json(['status'=>false,'errors'=>$validater->getMessageBag()],400);
        }
        $input=$request->except('password_confirmation');
        $input['password']=bcrypt($input['password']);
        $user=User::create($input);
        $token=$user->createToken('news-auth-token')->plainTextToken;
        return response()->json(['status'=>true,'token'=>$token],200);

    }

    public function login(Request $request)
    {
        $data=$request->all();
        $validater=Validator::make($data,['email'=>'required|email',
                                    'password'=>'required|min:6'],
                                    ['email.required'=>'please enter email',
                                    'password.required'=>'please enter password']);
        
        if($validater->fails())
        {
            return response()->json(['status'=>false,'errors'=>$validater->getMessageBag()],400);
        }

        $user = User::where('email',$data['email'])->first();
        if(!$user || !Hash::check($data['password'],$user->password)){
            return response()->json([
                'message' => 'Invalid Credentials'
            ],401);
        }
        $token = $user->createToken($user->name.'-AuthToken')->plainTextToken;
        return response()->json(['status'=>true,'token' => $token],200);
    }

    public function logout(Request $request)
    {
       $request->user()->tokens()->delete();

        return response()->json(["status"=>true,"message"=>"logged out"  ]);
    }
}
