<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\News;

class FetchNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch-news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for fetching news daily';

    /**
     * Execute the console command.
     */
    public function handle()
    {
    //    $this->fetchFromNeworg();
        $this->fetchFromGuardian();
        $this->fetchFromNewyorkTimes();

    }

    public function fetchFromNeworg()
    {
        try{
            $api_key=config('services.news_apis.newsorg');
            $category='bitcoin';
             $response=Http::get("https://newsapi.org/v2/everything?q=$category&apiKey=$api_key");
           
             if($response->successful())
             {
                 $news=$response->json();
                
                 foreach($news['articles'] as $data)
                 {
                     
                     $news=new News();
                     $news->source=$data['source']['name'];
                     $news->category=$category;
                     $news->title=$data['title'];
                     $news->slug=str_replace(" ","-",$data['title']);
                     $news->author=$data['author'];
                     $news->description=$data['description'];
                     $news->content=$data['content'];
                     $news->save();
                 }
 
                 \Log::info('successfully fetched data using newsapi.org');
                
             }
         }
         catch(\Exception $e)
         {
            
             \Log::info('error while fetching data using newsapi.org');
             \Log::info($e);
         }
    }

    public function fetchFromGuardian()
    {
      try{
        $api_key=config('services.news_apis.guardian');
        $response = Http::get("https://content.guardianapis.com/search?api-key=$api_key");

        if ($response->successful()) {
            foreach ($response->json()['response']['results'] as $data) {
                $news=new News();
                $news->title=$data['webTitle'];
                $news->slug=str_replace(" ","-",$data['webTitle']);
                $news->source='The Guardian';
                $news->description=$data['webUrl'];
                $news->content=$data['webUrl'];
                $news->category=$data['sectionName'];
                $news->save();
            }
            \Log::info('successfully fetched data using guardian');
        }
        
        
      }
      catch(\Exception $e)
      {
         
          \Log::info('error while fetching data using guardian');
          \Log::info($e);
      }

    }

    public function fetchFromNewyorkTimes()
    {
      try{
        $api_key=config('services.news_apis.newyorktimes');
        $response = Http::get("https://api.nytimes.com/svc/topstories/v2/home.json?api-key=$api_key");

        if ($response->successful()) {
            foreach ($response->json()['results'] as $data) {
                $news=new News();
                $news->title=$data['title'];
                $news->slug=str_replace(" ","-",$data['title']);
                $news->source='NY Times';
                $news->description=$data['abstract'];
                $news->content=$data['url'];
                $news->category=$data['section'];
                $news->save();
            }
         \Log::info('successfully fetched data using newyork times');

        }

      }
      catch(\Exception $e)
      {
         
          \Log::info('error while fetching data using newyork times');
          \Log::info($e);
      }

    }
}
