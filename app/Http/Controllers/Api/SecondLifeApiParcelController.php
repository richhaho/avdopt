<?php

namespace App\Http\Controllers\Api;

use App\Plan;
use App\Subscription;
use App\TerminalApiParcel;
use App\User;
use App\VerifyUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;

class SecondLifeApiParcelController extends SecondLifeApiBaseController
{


    public function __construct()
    {
        parent::__construct();
    }


    public function addParcelInfo(Request $request)
    {

        $uuid="";
        if($request->has('uuid') && $request->get('uuid')!=null)
        {
            $uuid=$request->get('uuid');
        }

        if(!$uuid)
        {
            return $this->sendErrorResponse('Please provide UUID.');
        }

        $user = User::where('uuid', '=', $uuid)
            ->first();

        if ($user === null) {
            return $this->sendErrorResponse('UUID not found.');
        }

        $parcel_name="";
        if($request->has('parcel_name') && $request->get('parcel_name')!=null)
        {
            $parcel_name=$request->get('parcel_name');
        }

        if(!$parcel_name)
        {
            return $this->sendErrorResponse('Please provide parcel name.');
        }

        $parcel = TerminalApiParcel::where('parcel_name', '=', $parcel_name)
            ->first();

        if ($parcel !== null) {
            return $this->sendErrorResponse('Parcel name already exists.');
        }

        $sl_url="";
        if($request->has('sl_url') && $request->get('sl_url')!=null)
        {
            $sl_url=$request->get('sl_url');
        }

        if(!$sl_url)
        {
            return $this->sendErrorResponse('Please provide sl url.');
        }

        $parcel = TerminalApiParcel::create([
            'uuid' => $uuid,
            'parcel_name' => $parcel_name,
            'sl_url' => $sl_url
        ]);

        $response_json = $this->sendSuccessResponse(array(),'Parcel info added successfully.');

        return $response_json;

    }



    public function __destruct() {
        // clearing the object reference
        parent::__destruct();
    }


}
