<?php


namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;


class SubjectController extends Controller
{
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

    public function getbyclient($clientid)
    {
        $subjects = DB::table('subjects')
            ->join('clients', 'subjects.client_id', '=', 'clients.id')
            ->where('client_id', $clientid)
            ->select('subjects.id', 'subjects.title', 'subjects.color', 'subjects.order_no', 'subjects.is_active', 'clients.name')
            ->get();
        $totalRows =  $subjects->count();
        return response()->json([
            'status' => 'success',
            'message' => '',
            'totalRows' => $totalRows,
            'data' => $subjects
        ], 200);
    }

    public function search(Request $request)
    {
        $totalRows =  0;
        $subjects = DB::select('call GetSubjects(?,?,?)', array($request->search, $request->pageSize, ($request->pageNo - 1)));

        if (count($subjects))
            $totalRows = $subjects[0]->total_rows;

        return response()->json([
            'status' => 'success',
            'totalRows' => $totalRows,
            'message' => '',
            'data' => $subjects
        ], 200);
    }

    public function show($id)
    {
        $subject = DB::select('call GetSubjectById(?)', array($id));
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
