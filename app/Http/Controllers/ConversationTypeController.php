<?php

namespace App\Http\Controllers;

use App\Models\ConversationType;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;


class ConversationTypeController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        $conversationTypes = DB::table('conversation_types')->get();

        $totalRows =  ConversationType::count();
        return response()->json([
            'status' => 'success',
            'totalRows' => $totalRows,
            'message' => '',
            'data' => $conversationTypes
        ], 200);
    }    
}
