<?php
namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserPreference;
use Illuminate\Support\Facades\Auth;
use Validator;

class UserPreferenceController extends Controller
{
    public function setPreferences(Request $request)
    {
        $data=$request->all();
        $validator=Validator::make($data,[
            'sources' => 'nullable|array',
            'categories' => 'nullable|array',
            'authors' => 'nullable|array',
        ],[]);
        if($validator->fails())
        {
            return response()->json(['status'=>false,'errors'=>$validator->getMessageBag()],400);
        }

        $user = Auth::user();
        
        $preferences = UserPreference::updateOrCreate(
            ['user_id' => $user->id], //finds the desired user
            [
            'sources' => json_encode($request->sources),
            'categories' => json_encode($request->categories),
            'authors' => json_encode($request->authors),
            ]
        );

        return response()->json(['status'=>true,'message'=>'successfully added'],200);
    }

    public function getPreferences()
    {
        $user = Auth::user();
        $preferences = $user->preferences;

        return response()->json(['status'=>true,'data'=>$preferences],200);
    }
}
