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
            $news=News::all();
            return response()->json(['status'=>true,'data'=>$news],200);
        }
        catch(\Exception $e)
        {
           
           \Log::info($e);
            return response()->json(['status'=>false,'message'=>'error while fetching data'],500);

        }
       
    }
}
