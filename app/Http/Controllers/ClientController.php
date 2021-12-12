<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class ClientController extends Controller
{

    public function index()
    {
        $client = Client::all();
        return response()->json($client);
    }

    public function show($id)
    {
        $client = Client::find($id);
        return response()->json($client);
    }

    public function create(Request $request)
    {
        $utcNow = Carbon::now('UTC')->format('Y-m-d h:i:s.v'); //yyyy-mm-dd etc

        $client = new Client();
        //TODO: need helper to generate UUID automatically for all model
        $client->Id = Uuid::uuid4()->toString();;
        $client->Name = $request->name;
        $client->ShortName = $request->shortName;
        $client->Website = $request->website;
        $client->PageTitle = $request->pageTitle;
        $client->Description = $request->description;
        $client->LogoUrl = $request->logoUrl;
        $client->CreatedUtc = $utcNow;
        $client->CreatedBy = 'System';
        $client->IsPublished = $request->isPublished;
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
                'Name' => $request->name,
                'ShortName' => $request->shortname,
                'Website' => $request->website,
                'PageTitle' => $request->pagetitle,
                'Description' => $request->description,
                'LogoUrl' => $request->logourl,
                'UpdatedUtc' => $utcNow,
                'UpdatedBy' => $request->updatedby,
                'IsPublished' => $request->ispublished,
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
            'message' => $message,
        ], 200);
    }

    public function destroy($id)
    {
        $message = 'Client delete successfully';
        $status = "success";
        try {
            $client = DB::table('trendata_clients')->where('id', $id);
            $client->delete();
            if (!$client) {
                $isContinue = false;
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
