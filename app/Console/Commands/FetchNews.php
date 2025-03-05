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
       $this->fetch_neworg_api();
    }

    public function fetch_neworg_api()
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
}
