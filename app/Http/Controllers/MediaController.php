<?php


namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;


class MediaController extends Controller
{
    public function __construct()
    {
    }

    public function index($pageNo = null, $pageSize = null)
    {
        if (!is_null($pageNo) && !is_null($pageSize))
            $medias =  DB::table('medias')->skip($pageNo - 1)->take($pageSize)->get();
        else
            $medias = DB::table('medias')->take(100)->get();

        $totalRows =  Media::count();
        return response()->json([
            'status' => 'success',
            'totalRows' => $totalRows,
            'message' => '',
            'data' => $medias
        ], 200);
    }

    public function search(Request $request)
    {
        if (isset($request->search) && trim($request->search !== '')) {
            $medias = DB::table('medias')
                ->where('name', 'like', '%' . $request->search . '%')
                ->skip($request->pageNo - 1)
                ->take($request->pageSize)
                ->get();
        } else
            $medias =  DB::table('medias')->skip($request->pageNo)->take($request->pageSize)->get();

        $totalRows =  $medias->count();
        return response()->json([
            'status' => 'success',
            'totalRows' => $totalRows,
            'message' => '',
            'data' => $medias
        ], 200);
    }

    public function show($id)
    {
        $media = DB::table('medias')->where('id', $id)->first();
        return response()->json([
            'status' => 'success',
            'message' => '',
            'data' => $media
        ], 200);
    }

    public function create(Request $request)
    {
        $utcNow = Carbon::now('UTC')->format('Y-m-d h:i:s.v'); //yyyy-mm-dd etc
        $id = Uuid::uuid4()->toString();
        $media = new Media();
        $media->id = $id;
        $media->name = $request->name;
        $media->created_at = $utcNow;
        // $media->created_by = 'System'; 
        $media->is_active = $request->is_active;
        $media->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Media Successfully Created!',
            'data' => $media
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $utcNow = Carbon::now('UTC')->format('Y-m-d h:i:s.v'); //yyyy-mm-dd etc
        $message = 'Media updated successfully';
        $status = "success";
        try {
            $update_data = Media::where('id', $id)->update([
                'name' => $request->name,
                'updated_at' => $utcNow,
                'is_active' => $request->is_active,
            ]);
            if (!$update_data) {
                $message = 'Media failed to update';
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
        $message = 'Media delete successfully';
        $status = "success";
        try {
            $client = Media::where('id', $id)->delete();
            if (!$client) {
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
