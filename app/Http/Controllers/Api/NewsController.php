<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\{News,UserPreference};
use Cache;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        try{
            // $news=News::select('*');
            // $preferences=auth()->user()->preferences;
            
            // if($preferences)
            // {
               
            //     if (!empty($preferences->sources)) {
            //         $news->whereIn('source', $preferences->sources);
            //     }
            //     if (!empty($preferences->categories)) {
                   
            //         $news->whereIn('category', $preferences->categories);
            //     }
            //     if (!empty($preferences->authors)) {
            //         $news->whereIn('author', $preferences->authors);
                    
            //     }
            // }
            $userId=auth()->user()->id;
            $userPref=auth()->user()->preferences->sources;
         
            $news=Cache::remember("user_feed_{$userId}",null, function () use ($userPref) {
                return News::whereIn('source', json_decode($userPref))
                ->paginate(10);
                   
                });
            return response()->json(['status'=>true,'data'=>$news],200);
        }
        catch(\Exception $e)
        {
          
           \Log::info($e);
            return response()->json(['status'=>false,'message'=>'error while fetching data'],500);

        }
       
    }

    public function filter(Request $request)
    {
        try{
          
            $news=News::select('*');
            if(isset($request->date) && $request->date !=null)
            {
                
                $news=$news->whereDate('created_at',$request->date);
            }
            if(isset($request->category) && $request->category !=null)
            {
                $news=$news->where('catergory',$request->category);

            }
            if(isset($request->keyword) && $request->keyword !=null)
            {
                $news=$news->where('catergory','LIKE','%'.$request->keyword.'%');

            }
            if(isset($request->source) && $request->source !=null)
            {
                $news=$news->where('source',$request->source);
            }
           
            $news=$news->get();
            return response()->json(['status'=>true,'data'=>$news],200);
        }
        catch(\Exception $e)
        {
            
             \Log::info($e);
            return response()->json(['status'=>false,'message'=>'error while filtering data'],500);
        }
        
    }

    public function news($slug)
    {
        try{
          
            if(News::where('slug',$slug)->exists())
            {
                $news=News::where('slug',$slug)->first();
                return response()->json(['status'=>true,'data'=>$news],200);
            }
            else{
                return response()->json(['status'=>false,'message'=>'not found'],404);

            }
        }
        catch(\Exception $e)
        {
           
           \Log::info($e);
            return response()->json(['status'=>false,'message'=>'error while fetching data'],500);

        }
    }
}
