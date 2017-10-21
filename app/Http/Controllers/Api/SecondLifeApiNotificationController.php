<?php

namespace App\Http\Controllers\Api;

use App\Plan;
use App\SecondLifeUsersNotification;
use App\Subscription;
use App\User;
use App\VerifyUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;

class SecondLifeApiNotificationController extends SecondLifeApiBaseController
{


    public function __construct()
    {
        parent::__construct();
    }


    public function getNotifications(Request $request)
    {

        $second_life_api_user_notifications = SecondLifeUsersNotification::with('user')->orderBy('created_at', 'desc')->get();
        //->where('is_read','=',0)

        $response=array();

        foreach($second_life_api_user_notifications  as $second_life_api_user_notification )
        {
            if($second_life_api_user_notification->user) {
                $response[] = array(
                    'uuid'=>$second_life_api_user_notification->user->uuid,
                    'message'=>$second_life_api_user_notification->message,
                    'url'=>$second_life_api_user_notification->url?$second_life_api_user_notification->url:''
                );
            }

            // update into db
            $second_life_api_user_notification->fill(array('is_read'=>1))->save();

        }


        $response_json = $this->sendSuccessResponse($response,'Success');

        return $response_json;
    }

    //Create SL notification
    public function addSLnotifications($data)
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

        $message = "";
        if($request->has('message') && $request->get('message')!=null)
        {
            $message=$request->get('message');
        }
        if(!$message)
        {
            return $this->sendErrorResponse('Please provide message.');
        }

        $type = "";
        if($request->has('type') && $request->get('type')!=null)
        {
            $type=$request->get('type');
        }
        if(!$type)
        {
            return $this->sendErrorResponse('Please provide type.');
        }

        $second_life_api_user_notification = SecondLifeUsersNotification::create([
            'uuid' => $uuid,
            'type' => $type,
            'message' => $message,
            'read' => 0,
            'created_time' => Carbon::now()->timestamp
        ]);
        $response_json = $this->sendSuccessResponse(array(),'SL Notiifcation created successfully.');
        return $response_json;        
    }


    public function __destruct() {
        // clearing the object reference
        parent::__destruct();
    }


}
