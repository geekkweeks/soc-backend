<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Redis;
use App\Libraries\Helpers;
use Illuminate\Database\MySqlConnection;


class ClientController extends Controller
{
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index($pageNo = null, $pageSize = null)
    {
        if (!is_null($pageNo) && !is_null($pageSize))
            $clients = DB::table('clients')->skip($pageNo - 1)->take($pageSize)->get();
        else {
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

    public function getall()
    {
        $clients = DB::table('clients')->get();

        $totalRows =  Client::count();

        return response()->json([
            'status' => 'success',
            'totalRows' => $totalRows,
            'message' => '',
            'data' => $clients
        ], 200);
    }

    public function search(Request $request)
    {
        if (isset($request->search) && trim($request->search !== '')) {
            $clients = DB::table('clients')
                ->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('short_name', 'like', '%' . $request->search . '%')
                ->orWhere('website', 'like', '%' . $request->search . '%')
                ->orWhere('pagetitle', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%')
                ->skip($request->pageNo - 1)
                ->take($request->pageSize)
                ->get();
        } else
            $clients =  DB::table('clients')->skip($request->pageNo)->take($request->pageSize)->get();

        $totalRows =  $clients->count();
        return response()->json([
            'status' => 'success',
            'totalRows' => $totalRows,
            'message' => '',
            'data' => $clients
        ], 200);
    }

    public function checkdbexist(Request $request)
    {
        $dbName = 'db_' . $request->shortName;
        $client = DB::table('clients')->where('db_name', $dbName)->first();
        return response()->json([
            'status' => 'success',
            'message' => '',
            'data' => $client ? true : false,
        ], 200);
    }

    public function show($id)
    {
        $client = DB::table('clients')->where('id', $id)->first();
        return response()->json([
            'status' => 'success',
            'message' => '',
            'data' => $client
        ], 200);
    }

    public function showredis($id, $total)
    {
        $cachedClient = $this->setorgetredis('client_' . $id, null, null, 'get');

        if (isset($cachedClient)) {
            $client = json_decode($cachedClient, FALSE);

            return response()->json([
                'status_code' => 200,
                'message' => 'Fetched from redis',
                'data' => $client,
            ]);
        } else {
            $client = DB::table('clients')->where('id', $id)->first();
            $expired_time = 60 * 1;
            $this->setorgetredis('client_' . $id, json_encode($client), $expired_time, 'set');

            return response()->json([
                'status_code' => 201,
                'message' => 'Fetched from database',
                'data' => $client,
            ]);
        }
    }

    public function create(Request $request)
    {

        $dbName = 'db_' . $request->short_name;
        $sqlQuery = 'CREATE DATABASE ' . $dbName;
        $resSchema = DB::statement($sqlQuery);
        if ($resSchema > 0) {
            $utcNow = Carbon::now('UTC')->format('Y-m-d h:i:s.v'); //yyyy-mm-dd etc
            $id = Uuid::uuid4()->toString();
            $client = new Client();
            //TODO: need helper to generate UUID automatically for all model
            $client->id = $id;
            $client->name = $request->name;
            $client->short_name = $request->short_name;
            $client->website = $request->website;
            $client->pagetitle = $request->pagetitle;
            $client->description = $request->description;
            $client->logo_url = $request->logo_url;
            $client->created_at = $utcNow;
            $client->created_by = 'System';
            $client->is_active = $request->is_active;
            $client->db_name = $dbName;
            $client->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Client Successfully Created!',
                'data' => $client
            ], 200);
        }

        return response()->json([
            'status' => 'failed',
            'message' => 'Something went wrong',
            'data' => null
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
            $client = Client::where('id', $id)->delete();
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
