<?php

namespace App\Http\Controllers;

use App\Models\ConversationType;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;


class TalkAboutController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        $talkAbouts = DB::table('talk_abouts')->get();

        $totalRows =  $talkAbouts->count();
        return response()->json([
            'status' => 'success',
            'totalRows' => $totalRows,
            'message' => '',
            'data' => $talkAbouts
        ], 200);
    }    
}
