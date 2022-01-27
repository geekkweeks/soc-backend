<?php


namespace App\Http\Controllers;

use App\Models\Feed;
use App\Models\FeedAnalysis;
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
        $keyCachePattern = 'feed_search_' . ($request->pageNo) . '_' . $request->pageSize . '_' . str_replace(' ', '', $request->search);
        $totalRows =  0;

        $cachedFeeds = $this->setorgetredis(strtolower($keyCachePattern), null, null, 'get');
        if (isset($cachedFeeds)) {
            $feeds = json_decode($cachedFeeds, FALSE);
            $message = 'data get from Redis';
        }

        if(!isset($feeds) || empty($feeds)) {
            $message = 'data get from Database';
            $feeds = DB::select('call GetFeeds(?,?,?)', array($request->search, $request->pageSize, ($request->pageNo - 1)));
            //set data key to redis
            $expired_time = 10 * 60; //in second
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
        if (!$request->feed) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Invalid data request',
                'data' => null
            ], 500);
        }

        DB::beginTransaction();

        try {
            $feed = (object)$request->feed;
            $utcNow = Carbon::now('UTC')->format('Y-m-d h:i:s.v'); //yyyy-mm-dd etc
            $id = Uuid::uuid4()->toString();
            DB::table('feeds')->insert([
                'id' => $id,
                'client_id' => $feed->client_id,
                'media_id' => $feed->media_id,
                'taken_date' => $feed->taken_date,
                'posted_date' => $feed->posted_date,
                'origin_id' => $feed->origin_id ?? Uuid::uuid4()->toString(), //auto generated if null
                'keyword' => $feed->keyword,
                'title' => $feed->title,
                'caption' => $feed->caption,
                'content' => $feed->content,
                'permalink' => $feed->permalink,
                'thumblink' => $feed->thumblink,
                'replies' => $feed->replies,
                'views' => $feed->views,
                'favs' => $feed->favs,
                'likes' => $feed->likes,
                'comment' => $feed->comment,
                'spam' => $feed->spam,
                'is_active' => $feed->is_active,
                'created_at' => $utcNow
            ]);

            #region analysis
            $analysis = $request->analysis;
            if ($analysis) {
                $analysis = (object)$request->analysis;
                $idAnalysis = Uuid::uuid4()->toString();
                DB::table('feed_analysis')->insert([
                    'id' => $idAnalysis,
                    'feed_id' => $id,
                    'subject_id' => $analysis->subject_id,
                    'talk_about' => $analysis->talk_about,
                    'conversation_type' => $analysis->talk_about,
                    'tags' =>  $analysis->tags,
                    'corporate' => $analysis->corporate,
                    'education' => $analysis->education,
                    'gender' => $analysis->gender,
                    'age' => $analysis->age,
                    'location' => $analysis->location,
                    'created_at' => $utcNow
                ]);
            }
            #endregion
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Feed Successfully Created!',
                'data' => null
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    private function setorgetredis($keyName, $keyValue = null, $expiredTime = null, $type)
    {
        $redisClient = new \Predis\Client();
        if ($type === 'get') {
            $res =  $redisClient->get($keyName);
            return $res;
        } else {
            $redisClient->setex($keyName, $expiredTime, $keyValue);
            return $redisClient->get($keyName);
        }
    }
}
