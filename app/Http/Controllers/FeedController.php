<?php


namespace App\Http\Controllers;

use App\Models\Feed;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Redis;


class FeedController extends Controller
{
    public function __construct()
    {
    }

    public function search(Request $request)
    {
        $message = '';
        $keyCachePattern = 'feed_search_' .($request->pageNo) .'_'.$request->pageSize .'_' .str_replace(' ', '', $request->search);
        $totalRows =  0;
        
        $cachedFeeds = $this->setorgetredis(strtolower($keyCachePattern), null, null, 'get');
        if(isset($cachedFeeds)){
            $feeds = json_decode($cachedFeeds, FALSE);
            $message = 'data get from Redis';
        }else{
            $message = 'data get from Database';
            $feeds = DB::select('call GetFeeds(?,?,?)', array($request->search, $request->pageSize, ($request->pageNo - 1)));          
            //set data key to redis
            $expired_time = 60 * 60; //in second
            $this->setorgetredis($keyCachePattern, json_encode($feeds), $expired_time, 'set');  
        }        
        
        if (count($feeds))
            $totalRows = $feeds[0]->total_rows;

        return response()->json([
            'status' => 'success',
            'totalRows' => $totalRows,
            'message' => $message,
            'data' => $feeds
        ], 200);
    }

    public function show($id)
    {
        $feeds = DB::select('call GetFeedById(?)', array($id));
        if (count($feeds))
            $data = $feeds[0];
        else
            $data = null;


        return response()->json([
            'status' => 'success',
            'message' => '',
            'data' => $data
        ], 200);
    }

    public function create(Request $request)
    {
        $utcNow = Carbon::now('UTC')->format('Y-m-d h:i:s.v'); //yyyy-mm-dd etc
        $id = Uuid::uuid4()->toString();
        $feed = new Feed();
        $feed->id = $id;
        $feed->client_id = $request->client_id;
        $feed->media_id = $request->media_id;
        $feed->taken_date = $request->taken_date;
        $feed->posted_date = $request->posted_date;
        $feed->origin_id = $request->origin_id;
        $feed->Keyword = $request->Keyword;
        $feed->title = $request->title;
        $feed->caption = $request->caption;
        $feed->content = $request->content;
        $feed->permalink = $request->permalink;
        $feed->thumblink = $request->thumblink;
        $feed->replies = $request->replies;
        $feed->views = $request->views;
        $feed->favs = $request->favs;
        $feed->likes = $request->likes;
        $feed->comment = $request->comment;
        $feed->age = $request->age;
        $feed->edu = $request->edu;
        $feed->spam = $request->spam;
        $feed->is_active = $request->is_active;
        $feed->created_at = $utcNow;
        // $sufeedbject->created_by = 'System'; 
        $feed->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Subject Successfully Created!',
            'data' => $feed
        ], 200);
    }

    private function setorgetredis($keyName, $keyValue = null, $expiredTime = null, $type){
        $redisClient = new \Predis\Client();
        if($type === 'get'){
            $res =  $redisClient->get($keyName);
            return $res;
        }else{
            $redisClient->setex($keyName, $expiredTime, $keyValue);
            return $redisClient->get($keyName);
        }
    }
}
