<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class ClientController extends Controller
{

    public function index($pageNo = null, $pageSize = null)
    {
        if (!is_null($pageNo) && !is_null($pageSize))
            $clients = DB::table('clients')->skip($pageNo)->take($pageSize)->get();
        else{
            $clients = DB::table('clients')->take(100)->get();
        }            

        $totalRows =  Client::count();
        return response()->json([
            'status' => 'success',
            'totalRows' => $totalRows,
            'message' => '',
            'data' => $clients
        ], 200);
    }

    public function show($id)
    {
        $client = Client::find($id);
        return response()->json([
            'status' => 'success',
            'message' => '',
            'data' => $client
        ], 200);
    }

    public function create(Request $request)
    {
        $utcNow = Carbon::now('UTC')->format('Y-m-d h:i:s.v'); //yyyy-mm-dd etc

        $client = new Client();
        //TODO: need helper to generate UUID automatically for all model
        $client->id = Uuid::uuid4()->toString();
        $client->name = $request->name;
        $client->short_name = $request->short_name;
        $client->website = $request->website;
        $client->pagetitle = $request->pagetitle;
        $client->description = $request->description;
        $client->logo_url = $request->logo_url;
        $client->created_at = $utcNow;
        $client->created_by = 'System';
        $client->is_active = $request->is_active;
        $client->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Client Successfully Created!',
            'data' => $client
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $utcNow = Carbon::now('UTC')->format('Y-m-d h:i:s.v'); //yyyy-mm-dd etc
        $message = 'Client updated successfully';
        $status = "success";
        try {
            $update_data = Client::where('id', $id)->update([
                'name' => $request->name,
                'short_name' => $request->short_name,
                'website' => $request->website,
                'pagetitle' => $request->pagetitle,
                'description' => $request->description,
                'logo_url' => $request->logo_url,
                'updated_at' => $utcNow,
                'updated_by' => 'System',
                'is_active' => $request->is_active,
            ]);
            if (!$update_data) {
                $message = 'Client failed to update';
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
        $message = 'Client delete successfully';
        $status = "success";
        try {
            // $client = DB::table('clients')->where('id', $id);
            $client= Client::where('id', $id)->delete();
            // $client->delete();
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
