<?php


namespace App\Http\Controllers;

use App\Models\Keyword;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class KeywordController extends Controller
{
    public function __construct()
    {
    }

    public function index($pageNo = null, $pageSize = null)
    {
        if (!is_null($pageNo) && !is_null($pageSize))
            $keywords =  DB::table('keywords')->skip($pageNo - 1)->take($pageSize)->get();
        else
            $keywords = DB::table('keywords')->take(100)->get();

        $totalRows =  Keyword::count();
        return response()->json([
            'status' => 'success',
            'totalRows' => $totalRows,
            'message' => '',
            'data' => $keywords
        ], 200);
    }

    public function search(Request $request)
    {
        $totalRows =  0;
        $keywords = DB::select('call GetKeywords(?,?,?)', array($request->search, $request->pageSize, ($request->pageNo - 1)));

        if (count($keywords))
            $totalRows = $keywords[0]->total_rows;

        return response()->json([
            'status' => 'success',
            'totalRows' => $totalRows,
            'message' => '',
            'data' => $keywords
        ], 200);
    }

    public function show($id)
    {
        $subject = DB::table('keywords')->where('id', $id)->first();
        return response()->json([
            'status' => 'success',
            'message' => '',
            'data' => $subject
        ], 200);
    }

    public function create(Request $request)
    {
        $utcNow = Carbon::now('UTC')->format('Y-m-d h:i:s.v'); //yyyy-mm-dd etc
        $id = Uuid::uuid4()->toString();
        $keyword = new Keyword();
        $keyword->id = $id;
        $keyword->client_id = $request->client_id;
        $keyword->media_id = $request->media_id;
        $keyword->keyword = $request->keyword;
        $keyword->sequence = $request->sequence;
        $keyword->created_at = $utcNow;
        // $subject->created_by = 'System'; 
        $keyword->is_active = $request->is_active;
        $keyword->save();

        return response()->json([
            'status' => 'success',
            'message' => 'keyword Successfully Created!',
            'data' => $keyword
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $utcNow = Carbon::now('UTC')->format('Y-m-d h:i:s.v'); //yyyy-mm-dd etc
        $message = 'Keyword updated successfully';
        $status = "success";
        try {
            $update_data = Keyword::where('id', $id)->update([
                'keyword' => $request->keyword,
                'sequence' => $request->sequence,
                'is_active' => $request->is_active,
                'updated_at' => $utcNow
            ]);
            if (!$update_data) {
                $message = 'Keyword failed to update';
                $status = "error";
            }
        } catch (\Throwable  $th) {
            $status = "error";
            $message = $th->getMessage();
        }

        return response()->json([
            'status' => $status,
            'message' => $message
        ], 200);
    }

    public function destroy($id)
    {
        $message = 'Keyword delete successfully';
        $status = "success";
        try {
            $subject = Keyword::where('id', $id)->delete();
            if (!$subject) {
                $message = "Data not found with id:" . $id;
                $status = "error";
            }
        } catch (\Throwable  $th) {
            $status = "error";
            $message = $th->getMessage();
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
        ], 200);
    }
}
