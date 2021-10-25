<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\LazyCollection;
use App\Events\AddArticles;
use App\Events\DeleteArticles;
use App\Events\RedisMessage;
use App\Models\Articles;
use App\Models\Channels;
use App\Models\Notifications;
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

    //文章列表-顯示所有文章、頻道
    public function showAritcles(Request $request)
    {   
        $datetime = Carbon::now()->setTimezone('Asia/Taipei')->toDateTimeString();
        $articles = Articles::orderBy('id')->get();
    
        $data = [
            'articles' => $articles,
            'datetime' => $datetime,
        ];

        // dd($data);

        return view('articles', $data);
    } 


    //新增一篇文章
    public function addArticles(Request $request)
    {
        $articles = new Articles();
        $articles->title = $request->InputTitle;
        $articles->content = $request->InputContent;
        $articles->author = $request->author;
        $articles->create_date = $request->createDate;
        $articles->save();

        $this->elasticService->addInfo($articles->title, $articles->content, $articles->author, $articles->create_date);

        return redirect()->route('showAritcles');
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

    //刪除文章
    public function deleteArticles(Request $request)
    {
        $id = $request->id;
        $isEven = $request->isEven;
        $status = 'success';
        try{
            $articles = Articles::where('id', '=', $id)->first();
            $title = '您有一篇新訊息【' . $articles->title . '已刪除】';
            $articles->delete();
            $even = $isEven ? '0' : '1';

            $pusher = new Pusher(
                '408cd422417d5833d90d',
                '2cb040ab9efbb676ed8b',
                '1243356', 
                array(
                    'cluster' => 'ap3',
                    'encrypted' => true
                )
            );

            // $user =  User::where('id % 2', '=', $even);
            
            set_time_limit(0);
            \App\User::chunk(10000, function($users)
            {   
                $title = $this->title;
                $id = $this->id;
                event(new DeleteArticles($users, $title, $id));
                
                $data['message'] =  $title;
                $data['userData'] =  [
                    'id' => $id,
                    'type' => 'deleteArticle'
                ];

                // $data['users'] = $users;

                foreach($users as $user){
                    $notification = $user->notifications()->first();
                    $data['broadcast'] = $notification;
                    $pusher->trigger('article-channel' . $user->id, 'App\\Events\\SendMessage', $data);
                }

                event(new RedisMessage($data));
            });
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
