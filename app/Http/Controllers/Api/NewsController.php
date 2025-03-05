<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\News;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        try{
            $news=News::paginate(3);
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
