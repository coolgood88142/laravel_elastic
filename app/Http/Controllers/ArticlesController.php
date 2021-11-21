<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\LazyCollection;
use App\Models\Articles;
use App\Imports\ArticlesImport;
use App\Services\ElasticService;
use App\Console\Commands\ElasearchCommand;
use App\User;
use App\Models\Author;
use App\Models\Comment;
use Carbon\Carbon;
use Pusher\Pusher;
use Auth;
use Excel;

class ArticlesController extends Controller
{
    public $title;
    public $id;
    protected $elasticService;

    public function __construct(ElasticService $elasticService)
    {
        $this->elasticService = $elasticService;
    }

    public function showAritcles(Request $request)
    {   
        $articles = Articles::all();
        return view('articles', ['articles' => $articles, 'keyword' => '']);
    } 

    public function searchArticles(Request $request)
    {
        $keyword = $request->keyword;
        $articles = $this->elasticService->fuzzySearch($keyword);
        return view('articles', ['articles' => $articles, 'keyword' => $keyword]);
    }


    //新增一篇文章
    public function addArticles(Request $request)
    {
        $articles = new Articles();
        $id = 5;
        $data = $articles->where('id', '=', $id)->first();

        // $articles->title = '測試111';
        // $articles->author = '張三';
        // $articles->create_date = '2021-11-01';
        // $articles->content = '測試ElasticSearch測試ElasticSearch測試ElasticSearch測試ElasticSearch';
        // $articles->save();
        // $id = $articles->id;

        $this->elasticService->addElastic($id, $data->title, $data->author, $data->create_date, $data->content);
        // $client = $this->elasticService->addElastic(2, 'Ming');
		print_r('已建立成功!');

        // return view('add');
    }

    public function updateArticles()
    {
        $params =[
            'index' => 'elastic20211029191024',
            'type' => 'data',
            'body' => [
                'title' => [

                ]
            ]
        ];
    }

    //刪除文章
    public function deleteArticles()
    {
        $client = $this->elasticService->connElastic();
        $params = [
            'index' => 'elastic',
            'id' => 24
        ];

        $this->elasticService->deleteElastic($client, $params);
    }

    public function addIndex()
    {
        //取得config/scout.php的elasticsearch hosts的資料
        $host = config('scout.elasticsearch.hosts');

        //取得config/scout.php的elasticsearch index的資料
        $index = config('scout.elasticsearch.index');

        //建立elasticsearch物件
        $client = $this->elasticService->connElastic();

        //判斷elasticsearch物件中有沒有index，有的話重新建立
        if ($client->indices()->exists(['index' => $index])) {
            $client->indices()->delete(['index' => $index]);
        }

        //建立elasticsearch索引值
        return $client->indices()->create([
            //設定索引值
            'index' => $index,

            //設定索引值存放內容
            'body' => [
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0
                ],
                'mappings' => [
                    '_source' => [
                        'enabled' => true
                    ],
                    'properties' => [
                        'title' => [
                            'type' => 'text',
                            'analyzer' => 'ik_max_word',
                            'search_analyzer' => 'ik_smart'
                        ],
                        'subtitle' => [
                            'type' => 'text',
                            'analyzer' => 'ik_max_word',
                            'search_analyzer' => 'ik_smart'
                        ],
                        'content' => [
                            'type' => 'text',
                            'analyzer' => 'ik_max_word',
                            'search_analyzer' => 'ik_smart'
                        ]
                    ],
                ]
            ]
        ]);
    }
    
    //顯示新增文章畫面
    public function showAdd(Request $request)
    {
        return view('add');
    }

    public function importArticles()
    {
        Excel::import(new ArticlesImport, storage_path('articles.xls'));


        return redirect('/showArticles');
    }
}
