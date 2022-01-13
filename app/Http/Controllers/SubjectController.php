<?php


namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;


class SubjectController extends Controller{
    public function __construct()
    {
    }

    public function index($pageNo = null, $pageSize = null)
    {
        if (!is_null($pageNo) && !is_null($pageSize))
            $subjects =  DB::table('subjects')->skip($pageNo - 1)->take($pageSize)->get();
        else
            $subjects = DB::table('subjects')->take(100)->get();

        $totalRows =  Subject::count();
        return response()->json([
            'status' => 'success',
            'totalRows' => $totalRows,
            'message' => '',
            'data' => $subjects
        ], 200);
    }

    public function search(Request $request)
    {
        if (isset($request->search) && trim($request->search !== '')) {
            $subjects = DB::table('subjects')
                ->where('title', 'like', '%' . $request->search . '%')
                ->skip($request->pageNo - 1)
                ->take($request->pageSize)
                ->get();
        } else
            $subjects =  DB::table('subjects')->skip($request->pageNo)->take($request->pageSize)->get();

        $totalRows =  $subjects->count();
        return response()->json([
            'status' => 'success',
            'totalRows' => $totalRows,
            'message' => '',
            'data' => $subjects
        ], 200);
    }

    public function show($id)
    {
        $subject = DB::table('subjects')->where('id', $id)->first();
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
        $subject = new Subject();
        $subject->id = $id;
        $subject->client_id = $request->client_id;
        $subject->title = $request->title;
        $subject->color = $request->color;
        $subject->order_no = $request->order_no;
        $subject->created_at = $utcNow;
        // $subject->created_by = 'System'; 
        $subject->is_active = $request->is_active;
        $subject->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Subject Successfully Created!',
            'data' => $subject
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $utcNow = Carbon::now('UTC')->format('Y-m-d h:i:s.v'); //yyyy-mm-dd etc
        $message = 'Subject updated successfully';
        $status = "success";
        try {
            $update_data = Subject::where('id', $id)->update([
                'title' => $request->title,
                'color' => $request->color,
                'order_no' => $request->order_no,
                'is_active' => $request->is_active,
                'updated_at' => $utcNow
            ]);
            if (!$update_data) {
                $message = 'Subject failed to update';
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
        $message = 'Subject delete successfully';
        $status = "success";
        try {
            $subject = Subject::where('id', $id)->delete();
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