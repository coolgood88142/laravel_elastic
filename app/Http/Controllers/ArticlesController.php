<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\LazyCollection;
use App\Models\Articles;
use App\Models\Channels;
use App\Models\Notifications;
use App\Services\ElasticService;
use App\Console\Commands\ElasearchCommand;
use App\User;
use App\Models\Author;
use App\Models\Comment;
use Carbon\Carbon;
use Pusher\Pusher;
use Auth;

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
        $articles->title = '測試111';
        $articles->author = '張三';
        $articles->create_date = '2021-11-01';
        $articles->content = '測試ElasticSearch測試ElasticSearch測試ElasticSearch測試ElasticSearch';
        $articles->save();
        $id = $articles->id;

        $this->elasticService->addElastic($id, $articles->title, $articles->author, $articles->create_date, $articles->content);
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
            'index' => '20211027151005',
        ];

        $this->elasticService->deleteElastic($client, $params);
    }

    public function addIndex()
    {
        $host = config('scout.elasticsearch.hosts');
        $index = config('scout.elasticsearch.index');
        $client = $this->elasticService->connElastic();

        if ($client->indices()->exists(['index' => $index])) {
            $client->indices()->delete(['index' => $index]);
        }

        return $client->indices()->create([
            'index' => $index,
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

    //已閱讀通知 or 已閱讀全部
    public function readArticles(Request $request)
    {
        $id = $request->id;
        $userId = $request->userId;
        $status = 'success';
        try{
            if($id != ''){
                //已閱讀通知
                $this->readNotifications($id, $userId);
            }else{
                //已閱讀全部
                $this->readNotificationsAll($userId);
            }
            
        }catch(Exception $e){
            $status = 'error';
        }
        return $status;
    }

    //已閱讀通知
    public function readNotifications($id, $userId){
        // $userId =  $request->userId;
        // $id =  $request->id;
        $user = User::where('id', '=', $userId)->first();
        $data = $user->unreadNotifications->where('id', '=', $id)->first()->markAsRead();
    }

    //已閱讀全部
    public function readNotificationsAll($userId){
        // $userId =  $request->userId;
        $user = User::where('id', '=', $userId)->first();
        $data = $user->unreadNotifications->markAsRead();
    }

    //顯示單篇文章內容
    public function showArticleContent(Request $request)
    {
        $id = $request->id;
        $userId = $request->userId;
        $notificationId = $request->notificationId;
        $isAdd = $request->isAdd;
        $articles = Articles::where('id', '=', $id)->first();
        $user = User::where('id', '=', $articles->author_id)->first();
        $comment =  DB::table('users')
            ->select('users.name', 'comment.text')
            ->join('comment', 'users.id', '=', 'comment.user_id')
            ->where([
                ['articles_id', '=', $id]
            ])
            ->get();

        // if($notificationId != null && $userId != null && $isRead == 'N'){
        //     //點選後直接做已閱讀
        //     $this->readNotifications($notificationId, $userId);
        // }
        

        return view('edit', [
            'articleId' => $id,
            'title' => $articles->title,
            'content' => $articles->content,
            'onlineDate' => $articles->online_date,
            'sendNotice' => $articles->send_notice,
            'userId' => $userId,
            'userName' => $user->name,
            'comment' => $comment,
            'isAdd' => $isAdd
        ]);
    }

    //儲存文章
    public function saveArticles(Request $request)
    {
        $id = $request->id;
        $status = 'success';
        try{
            $articles = Articles::where('id', '=', $id)->first();
            $articles->title = $request->title;
            $articles->content = $request->content;
            $articles->save();
        }catch(Exception $e){
            $status = 'error';
        }

        return $status;
    }
    
    //顯示新增文章畫面
    public function showAdd(Request $request)
    {
        return view('add');
    }


}
