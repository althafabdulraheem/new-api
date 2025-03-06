<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use Hash;
use Auth;

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
        $validator=Validator::make($data,['email'=>'required|email',
                                    'password'=>'required|min:6'],
                                    ['email.required'=>'please enter email',
                                    'password.required'=>'please enter password']);
        
        if($validator->fails())
        {
            return response()->json(['status'=>false,'errors'=>$validator->getMessageBag()],400);
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

    public function password_reset(Request $request)
    {
        $data=$request->all();
        $validator=Validator::make($data,[
        'old_password'=>'required|min:6',
        'password'=>'required|min:6|confirmed'],
        ['password.required'=>'please enter password']);

        if($validator->fails())
        {
            return response()->json(['status'=>false,'errors'=>$validator->getMessageBag()],400);
        }
        $user=Auth::user();
        if(Hash::check($request->old_password,$user->password))
        {
            $update=$user->update(['password'=>bcrypt($request->password)]);
            if($update)
            {
                $user->tokens()->delete();
                return response()->json(["status"=>true,"message"=>"successfully changed password"  ]);

            }
        }
        else{
            return response()->json(["status"=>false,"message"=>"Invalid old password"  ]);

        }

    }
}
