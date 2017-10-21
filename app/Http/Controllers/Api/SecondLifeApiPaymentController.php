<?php

namespace App\Http\Controllers\Api;
use Carbon\Carbon;
use App\Plan;
use App\Subscription;
use App\User;
use Auth;
use App\UserMessage;
use App\FeatureSetting;
use App\Tokens;
use App\VerifyUser;
use App\Donation;
use App\Notification;
use App\Advertisement;
use App\SecondLifeUsersNotification;
use App\AdsSubscriptions;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use App\Events\UserAllNotification;
use DB;
use App\Wallet;
use App\Models\Coupon;

class SecondLifeApiPaymentController extends SecondLifeApiBaseController
{


    public function __construct()
    {
        parent::__construct();
    }


    public function addBalanceToUser(Request $request)
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

        $amount=0;
        if($request->has('amount') && $request->get('amount')!=null)
        {
            $amount=intval($request->get('amount'));
        }

        if(!$amount)
        {
            return $this->sendErrorResponse('Please provide amount.');
        }


        $user->deposit($amount, 'deposit', ['description' => 'You deposited '.$amount]);

        //Notifications start
            $wallet = DB::table('wallets')->where('user_id',$user->id)->first();        
            $wallet_amount = $wallet->balance;

            $admin = User::where('role_id', '=', 1)->first();
            $admin_id = $admin->id;
            
            $message = $user->displayname." your deposit of ".$amount." Tokens was successful. Your wallet balance is: ".$wallet_amount." Tokens.";

            $emaildata = array(
                'email' => $user->email,
                'displayname' => $user->displayname,
                'email_message' => $message
            );
            
            $messagedata = array(
                'user_id' => $admin_id,
                'reciever_id' => $user->id,
                'message' => $message,
                'type' => 'message_notification'
            );
            
            $notficationdata = array(
                'user_id' => $user->id,
                'message' => $message,
                'type' => 'payment_deposite',
                'created_by' => $admin_id
            );
            
            $sl_notificationdata = array(
                'uuid' => $uuid,
                'message' => $message,
                'type' => 'Payment Notification'
            );
            
            $allnoticationdata = array(
                'emailtype' =>$emaildata,
                'messagetype' =>$messagedata,
                'notificationtype' =>$notficationdata,
                'sl_notificationtype' =>$sl_notificationdata
            );
            \Event::fire(new UserAllNotification($allnoticationdata)); 
        //Notifications end 

        $response_json = $this->sendSuccessResponse(array(),'Balance added successfully.');

        return $response_json;

    }

    public function getUserBalance(Request $request)
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

        $response_json = $this->sendSuccessResponse(array('amount'=>$user->balance),'Success.');

        return $response_json;
    }

    public function purchasePlan(Request $request)
    {
        $uuid="";
        $newplanbuy = $request->input('newplanbuy');

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

        $plan_id="";
        if($request->has('plan_id') && $request->get('plan_id')!=null)
        {
            $plan_id=$request->get('plan_id');
        }        

        if(!$plan_id)
        {
            return $this->sendErrorResponse('Please provide plan id.');
        }

        $plan = Plan::where('plan_id', '=', $plan_id)
            ->first();

        if ($plan === null) {
            return $this->sendErrorResponse('Plan not found.');
        }

        $amount=0;
        if($request->has('amount') && $request->get('amount')!=null)
        {
            $amount=intval($request->get('amount'));
        }


        if(!$amount)
        {
            return $this->sendErrorResponse('Please provide amount.');
        }

        if($plan){
            $expiry_date = getExpiryDate($request->plan_id);
            if(!$user->subscribed('main')){
                $subscription = new Subscription;
                $subscription->user_id = $user->id;
                $subscription->name = 'main';
                $subscription->stripe_plan = $plan_id;
                $subscription->quantity = '1';
                $subscription->ends_at = $expiry_date;
                $subscription->save();
            }

            if ($user->subscribed('main')) {
                Subscription::where('user_id', $user->id)->where('name', 'main')
                        ->update(['stripe_plan' => $plan_id, 'ends_at' => $expiry_date]);
            }
        }
        $user->deposit($amount, 'deposit', ['description' => 'You deposited '.$amount.' amount for Plan : '.$plan->name]);
        //$user->withdraw($amount, 'withdraw', ['description' => 'You purchased plan '.$plan->name]);
        
        //Notifications start
            $wallet_obj = $user->walletData;
            $wallet_obj->balance = $plan->tokens;
            $wallet_obj->save();
            if($request->appliedcouponcode_flag){
                $coupon_obj = Coupon::whereRaw('BINARY `coupon_code` = ?', $request->appliedcouponcode)->first();
                if($coupon_obj->discountType->slug == 'token_off' || $coupon_obj->discountType->slug == 'both'){
                    $wallet_obj->balance = $wallet_obj->balance + $coupon_obj->token_amt;
                    $wallet_obj->save();
                }
                if($coupon_obj->discountType->slug == 'discount_percentage' || $coupon_obj->discountType->slug == 'both'){
                    $amount = number_format($request->amount - (($request->amount * $coupon_obj->discount) / 100), 2);
                }
                $coupon_obj->count = $coupon_obj->count - 1;
                $coupon_obj->save();
            }
            $wallet = DB::table('wallets')->where('user_id',$user->id)->first();        
            $wallet_amount = $wallet->balance;

            $admin = User::where('role_id', '=', 1)->first();
            $admin_id = $admin->id;

            if($newplanbuy == 1){
                $message = $user->displayname.", welcome to a world of possibilities with your ".$plan->name." premium plan";
            }else{
                $message = "Congratulation ".$user->displayname."! You've successfully upgraded to ".$plan->name." premium plan and your payment of ".$amount." Tokens was successfully received."; 
            } 
            
            $emaildata = array(
                'email' => $user->email,
                'displayname' => $user->displayname,
                'email_message' => $message
            );
            
            $messagedata = array(
                'user_id' => $admin_id,
                'reciever_id' => $user->id,
                'message' => $message,
                'type' => 'message_notification'
            );
            
            $notficationdata = array(
                'user_id' => $user->id,
                'message' => $message,
                'type' => 'payment_deposite',
                'created_by' => $admin_id
            );
            
            $sl_notificationdata = array(
                'uuid' => $uuid,
                'message' => $message,
                'type' => 'Payment Notification'
            );
            
            $allnoticationdata = array(
                'emailtype' =>$emaildata,
                'messagetype' =>$messagedata,
                'notificationtype' =>$notficationdata,
                'sl_notificationtype' =>$sl_notificationdata
            );
            \Event::fire(new UserAllNotification($allnoticationdata)); 
        //Notifications end 

        $response_json = $this->sendSuccessResponse(array(),'Plan purchased successfully.');
        return $response_json;
    } 

    public function purchasePlanFeature(Request $request)
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

        $plan_id="";
        if($request->has('plan_id') && $request->get('plan_id')!=null)
        {
            $plan_id=$request->get('plan_id');
        }        

        if(!$plan_id)
        {
            return $this->sendErrorResponse('Please provide plan id.');
        }        
        
        $plan = FeatureSetting::where('plan_id', '=', $plan_id)
            ->first();

        if ($plan === null) {
            return $this->sendErrorResponse('Plan not found.');
        }


        $amount=0;
        if($request->has('amount') && $request->get('amount')!=null)
        {
            $amount=intval($request->get('amount'));
        }

        if(!$amount)
        {
            return $this->sendErrorResponse('Please provide amount.');
        }

        $userid = $user->id;

        if($plan){
            $expiry_date = getExpiryDate($plan_id);
            $sub_featured = Subscription::where('user_id', $userid)->where('name', 'feature')->first();
            if($sub_featured == ''){
                $subscription = new Subscription;
                $subscription->user_id = $user->id;
                $subscription->name = 'feature';
                $subscription->stripe_plan = $plan_id;
                $subscription->quantity = '1';
                $subscription->ends_at = $expiry_date;
                $subscription->save();
            }else{
                Subscription::where('user_id', $user->id)->where('name', 'feature')
                    ->update(['stripe_plan' => $plan_id, 'ends_at' => $expiry_date]);
            }              
        }

        $user->deposit($amount, 'deposit', ['description' => 'You deposited '.$amount.' amount for Plan : '.$plan->name]);
        
        //NOTIFICATIONS START
            $wallet = DB::table('wallets')->where('user_id',$user->id)->first();        
            $wallet_amount = $wallet->balance;

            $admin = User::where('role_id', '=', 1)->first();
            $admin_id = $admin->id;

            $message = "Congratulation ".$user->displayname."! You've successfully upgraded to ".$plan->name." feature plan and your payment of ".$amount." Tokens was successfully received.";              
            
            $emaildata = array(
                'email' => $user->email,
                'displayname' => $user->displayname,
                'email_message' => $message
            );
            
            $messagedata = array(
                'user_id' => $admin_id,
                'reciever_id' => $user->id,
                'message' => $message,
                'type' => 'message_notification'
            );
            
            $notficationdata = array(
                'user_id' => $user->id,
                'message' => $message,
                'type' => 'payment_deposite',
                'created_by' => $admin_id
            );
            
            $sl_notificationdata = array(
                'uuid' => $uuid,
                'message' => $message,
                'type' => 'Payment Notification'
            );
            
            $allnoticationdata = array(
                'emailtype' =>$emaildata,
                'messagetype' =>$messagedata,
                'notificationtype' =>$notficationdata,
                'sl_notificationtype' =>$sl_notificationdata
            );
            \Event::fire(new UserAllNotification($allnoticationdata)); 
        //NOTIFICATIONS END

        $response_json = $this->sendSuccessResponse(array(),'Feature Plan purchased successfully.');

        return $response_json;
    }

    public function purchaseToken(Request $request)
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

        $plan_id="";
        if($request->has('plan_id') && $request->get('plan_id')!=null)
        {
            $plan_id=$request->get('plan_id');
        }        

        if(!$plan_id)
        {
            return $this->sendErrorResponse('Please provide plan id.');
        }        
        
        $plan = Tokens::where('id', '=', $plan_id)
            ->first();

        if ($plan === null) {
            return $this->sendErrorResponse('Token not found.');
        }

        $amount=0;
        if($request->has('amount') && $request->get('amount')!=null)
        {
            $amount=intval($request->get('amount'));
        }

        if(!$amount)
        {
            return $this->sendErrorResponse('Please provide amount.');
        }
        
        $user->deposit($amount, 'deposit', ['description' => 'You deposited '.$amount.' amount for Token : '.$plan->title]);
        
        //Notifications start
            $wallet = DB::table('wallets')->where('user_id',$user->id)->first();        
            $wallet_amount = $wallet->balance;

            $admin = User::where('role_id', '=', 1)->first();
            $admin_id = $admin->id;

            $message = $user->displayname." your deposit of ".$amount." Tokens was successful. Your wallet balance is: ".$wallet_amount." Tokens.";  
            
            $emaildata = array(
                'email' => $user->email,
                'displayname' => $user->displayname,
                'email_message' => $message
            );
            
            $messagedata = array(
                'user_id' => $admin_id,
                'reciever_id' => $user->id,
                'message' => $message,
                'type' => 'message_notification'
            );
            
            $notficationdata = array(
                'user_id' => $user->id,
                'message' => $message,
                'type' => 'payment_deposite',
                'created_by' => $admin_id
            );
            
            $sl_notificationdata = array(
                'uuid' => $uuid,
                'message' => $message,
                'type' => 'Payment Notification'
            );
            
            $allnoticationdata = array(
                'emailtype' =>$emaildata,
                'messagetype' =>$messagedata,
                'notificationtype' =>$notficationdata,
                'sl_notificationtype' =>$sl_notificationdata
            );
            \Event::fire(new UserAllNotification($allnoticationdata)); 
        //Notifications end 

        $response_json = $this->sendSuccessResponse(array(),'Token plan purchased successfully.');

        return $response_json;
    }

    public function addDonation(Request $request)
    {
        $is_supporter = $request->input('show_supporter');
        $show_amount = $request->input('show_amount');

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

        $amount=0;
        if($request->has('amount') && $request->get('amount')!=null)
        {
            $amount=intval($request->get('amount'));
        }

        if(!$amount)
        {
            return $this->sendErrorResponse('Please provide amount.');
        }
        
        $userid = $user->id;
        if ($amount)
        {        
            $donationdata = Donation::where('user_id', $userid)->first();
            if(!$donationdata)
            {             
              $donation = new Donation;
              $donation->user_id = $userid;
              $donation->amount = $amount;
              $donation->is_supporter = $is_supporter;
              $donation->show_amount = $show_amount;
              $donation->save();
            }else{              
              $newamount = $donationdata->amount + $amount;
              Donation::where('user_id', $userid)->update(['amount' => $newamount, 'is_supporter' =>$request->input('show_supporter'), 'show_amount' =>$request->input('show_amount')]);
            }
        }        
        $user->deposit($amount, 'deposit', ['description' => 'You donated '.$amount.' amount']);

        //Notifications start
            $wallet = DB::table('wallets')->where('user_id',$user->id)->first();        
            $wallet_amount = $wallet->balance;

            $admin = User::where('role_id', '=', 1)->first();
            $admin_id = $admin->id;

            $message = $user->displayname." your deposit of ".$amount." Tokens was successful. Your wallet balance is: ".$wallet_amount." Tokens.";  
            
            $emaildata = array(
                'email' => $user->email,
                'displayname' => $user->displayname,
                'email_message' => $message
            );
            
            $messagedata = array(
                'user_id' => $admin_id,
                'reciever_id' => $user->id,
                'message' => $message,
                'type' => 'message_notification'
            );
            
            $notficationdata = array(
                'user_id' => $user->id,
                'message' => $message,
                'type' => 'payment_deposite',
                'created_by' => $admin_id
            );
            
            $sl_notificationdata = array(
                'uuid' => $uuid,
                'message' => $message,
                'type' => 'Payment Notification'
            );
            
            $allnoticationdata = array(
                'emailtype' =>$emaildata,
                'messagetype' =>$messagedata,
                'notificationtype' =>$notficationdata,
                'sl_notificationtype' =>$sl_notificationdata
            );
            \Event::fire(new UserAllNotification($allnoticationdata));
        //Notifications end 

        $response_json = $this->sendSuccessResponse(array(),'Deposite added successfully.');
        return $response_json;
    }   

    public function addAdvertisement(Request $request)
    {
        $uuid="";
        $newplanbuy = $request->input('newplanbuy');

        if($request->has('uuid') && $request->get('uuid')!=null)
        {
            $uuid=$request->get('uuid');
        }        
      
        if(!$uuid)
        {
            return $this->sendErrorResponse('Please provide UUID.');
        }

        $user = User::where('uuid', '=', $uuid)->first();

        if ($user === null) {
            return $this->sendErrorResponse('UUID not found.');
        }

        $ads_id="";
        if($request->has('ads_id') && $request->get('ads_id')!=null)
        {
            $ads_id=$request->get('ads_id');
        }        

        if(!$ads_id)
        {
            return $this->sendErrorResponse('Please provide advertisement id.');
        }

        $advertisement = Advertisement::where('id', '=', $ads_id)->first();

        if ($advertisement === null) {
            return $this->sendErrorResponse('Advertisement not found.');
        }

        $amount=0;
        if($request->has('amount') && $request->get('amount')!=null)
        {
            $amount=intval($request->get('amount'));
        }

        if(!$amount)
        {
            return $this->sendErrorResponse('Please provide amount.');
        }

        $userid = $user->id;
        if ($amount)
        {    
            $is_ads_subscribe = AdsSubscriptions::where('ads_id', $ads_id)->where('user_id', $userid)->get();
            if($is_ads_subscribe->count() == 0){
                $adssubscriptions = new AdsSubscriptions();
                $adssubscriptions->ads_id = $ads_id;
                $adssubscriptions->user_id = $userid;
                $adssubscriptions->status = 'Inactive';
                $adssubscriptions->paid = 1;
                $adssubscriptions->approve = 0;
                $adssubscriptions->save();

                $user->deposit($amount, 'deposit', ['description' => 'You deposited '.$amount.' amount for Advertisement']);
            }else{
                return $this->sendErrorResponse('Advertisement purchased already');
            }
        }

        //Notifications start
            $wallet = DB::table('wallets')->where('user_id',$user->id)->first();        
            $wallet_amount = $wallet->balance;

            $admin = User::where('role_id', '=', 1)->first();
            $admin_id = $admin->id;

            $message = $user->displayname." your deposit of ".$amount." Tokens was successful for advertisement. Your wallet balance is: ".$wallet_amount." Tokens.";  
                
            $emaildata = array(
                'email' => $user->email,
                'displayname' => $user->displayname,
                'email_message' => $message
            );
                
            $messagedata = array(
                'user_id' => $admin_id,
                'reciever_id' => $user->id,
                'message' => $message,
                'type' => 'message_notification'
            );
                
            $notficationdata = array(
                'user_id' => $user->id,
                'message' => $message,
                'type' => 'payment_deposite',
                'created_by' => $admin_id
            );
                
            $sl_notificationdata = array(
                'uuid' => $uuid,
                'message' => $message,
                'type' => 'Payment Notification'
            );
                
            $allnoticationdata = array(
                'emailtype' =>$emaildata,
                'messagetype' =>$messagedata,
                'notificationtype' =>$notficationdata,
                'sl_notificationtype' =>$sl_notificationdata
            );
            //\Event::fire(new UserAllNotification($allnoticationdata));
        //Notifications end 

        $response_json = $this->sendSuccessResponse(array(),'Advertisement purchased successfully.');
        return $response_json; 
    } 

    public function __destruct() {
        // clearing the object reference
        parent::__destruct();
    }

}
