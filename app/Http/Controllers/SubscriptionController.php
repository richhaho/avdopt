<?php

namespace App\Http\Controllers;

use App\Events\UserAllNotification;
use App\Features;
use Auth;
use App\User;
use App\Plan;
use App\Usergroup;
use App\WebsiteSetting;
use App\FeatureSetting;
use App\Subscription;
use Illuminate\Http\Request;
use DB;
use App\Models\Coupon;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        /*\Stripe\Stripe::setApiKey(env("STRIPE_SECRET"));*/
    }

    public function index()
    {
        $plans = Plan::orderBy('price', 'asc')->get();
        return view('membershipPlans.index', compact('plans'));
    }

    public function create()
    {
        return view('membershipPlans.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'tokens' => 'required|numeric',
            'billing_interval' => 'required',
        ]);
        $paymentarray = array(
            "nickname" => $request->input('name'),
            "amount" => $request->input('price') * 100,
            "tokens" => $request->input('tokens'),
            "interval" => $request->input('billing_interval'),
            "product" => array(
                "name" => $request->input('name')
            ),
            "currency" => "usd",
            "trial_period_days" => $request->input('trial_period_days'),
        );
        $tokenamount = getWebsiteSettingsByKey('token_amount');

        if ($tokenamount) {
            $paymentarray["amount"] = ($request->input('price') * $tokenamount) * 100;
        }


        if ($request->input('billing_interval') == 'quarter') {
            $paymentarray["interval"] = 'month';
            $paymentarray["interval_count"] = 3;
        }

        if ($request->input('billing_interval') == 'semiannual') {
            $paymentarray["interval"] = 'month';
            $paymentarray["interval_count"] = 6;
        }

        try {
            $response = \Stripe\Plan::create($paymentarray);
            $plan = new Plan;
            $plan->plan_id = $response->id;
            $plan->name = $request->name;
            $plan->description = $request->description;
            $plan->price = $request->price;
            $plan->billing_interval = $request->billing_interval;
            $plan->trial_period = $request->trial_period_days;
            $plan->save();
            dd($plan);
            $id = $plan->id;
            self::saveWebsiteSetting($request, $id);
        } catch (Exception $e) {
            return redirect()->back()->with('failed', $e->getMessage());
        }
        return redirect()->to('admin/subscriptionplans')->with('success', 'Plan created successfully');
    }

    public function saveWebsiteSetting($request, $id)
    {
        $websiteSettings = $request->input('websiteSettings');
        if (count($websiteSettings)) {
            foreach ($websiteSettings as $fieldId => $fieldVal) {
                self::addMetaValue($fieldId, $fieldVal, $id);
            }
        }
    }

    public function addMetaValue($metaKey, $metaValue, $metaID)
    {
        $data = WebsiteSetting::where('meta_key', $metaKey . '_' . $metaID)->first();
        if ($data) {
            $data->meta_value = $metaValue;
        } else {
            $data = new WebsiteSetting;
            $data->meta_id = $metaID;
            $data->meta_key = $metaKey . '_' . $metaID;
            $data->meta_value = $metaValue;
        }
        $data->save();
    }

    public function destroy($id)
    {
        if (!$id) {
            return redirect()->back()->with('failed', 'Invalid Request');
        }
        try {
            $data = Plan::find($id);
            $data->delete();
            $plan = \Stripe\Plan::retrieve($data->plan_id);
            $plan->delete();
            WebsiteSetting::where('meta_id', $id)->delete();
        } catch (Exception $e) {
            return redirect()->back()->with('failed', $e->getMessage());
        }
        return redirect()->to('admin/subscriptionplans')->with('success', 'Plan deleted successfully');
    }

    public static function getPlanlist()
    {
        if (Auth::user()) {
            if (Auth::user()->group) {
                $user = User::find(Auth::user()->id);
                $membership_plans = isset($user->usergroup->membership_plans) ? $user->usergroup->membership_plans : '';
                if ($membership_plans) {
                    $membership_planIds = json_decode($membership_plans, true);
                    return Plan::whereIn('id', $membership_planIds)->orderBy('price', 'asc')->get();
                }
            }
            return redirect()->back()->with('danger', "You haven't set any user group. Complete your profile");
        }
        return redirect()->to('/login');
    }

    public function getpricing()
    {
        $plans = self::getPlanlist();
        $features = Features::all();
        $starters = WebsiteSetting::where('meta_id', 10)->get();
        $professionals = WebsiteSetting::where('meta_id', 9)->get();
        $enterprices = WebsiteSetting::where('meta_id', 8)->get();
        $subscription = Subscription::where('user_id', Auth::user()->id)->where('name', 'main')->first();
        $title_by_page = "Subscription";
        if (Auth::user()->role_id == 1) {
            return view('user.pricing', compact('plans', 'subscription', 'features', 'starters', 'professionals', 'enterprices', 'title_by_page'));
        } else {
            return view('user.price', compact('plans', 'subscription', 'features', 'starters', 'professionals', 'enterprices', 'title_by_page'));
        }
    }

    public function upgrade(Request $request)
    {
        $chargeId = $request->input('chargeId');

        if ($chargeId) {
            $user = User::find(Auth::user()->id);
            if ($user->subscription('main')->onGracePeriod()) {
                $user->subscription('main')->resume();
            }
            if ($user->subscribed('main')) {
                //$user->subscription('main')->swap($chargeId);
                Subscription::where('user_id', Auth::user()->id)->where('name', 'main')
                    ->update(['stripe_plan' => $chargeId]);
            }
        }
        if ($user->subscribed('main')) {
            return redirect()->to('subscription/success');
        }
        return redirect()->to('subscription/failed');
    }

    public function checkout(Request $request)
    {
        $chargeId = $request->input('chargeId');
        $newplanbuy = 0;
        $newplanbuy = $request->input('newplanbuy');//user first time buy a plan

        $user = User::find(Auth::user()->id);
        $userid = $user->id;

        $walletsdata = DB::table('wallets')->where('user_id',$userid)->first();
        $wallet_amount = 0;
        if($walletsdata != ''){
            $wallet_amount = $walletsdata->balance;
        }

        $plandata = Plan::where('plan_id', $chargeId)->first();

        $featuredata = FeatureSetting::where('plan_id', $chargeId)->first();

        $token = array();
        $donation = array();
        $creditamount = 0;
        $advertisement = array();
        return view('user.checkout', compact('user', 'plandata', 'newplanbuy', 'featuredata', 'wallet_amount', 'token', 'creditamount', 'donation', 'advertisement'));
    }

    public function paywithwallet(Request $request)
    {
        $chargeId = $request->input('plan_id');
        $amount = $request->input('amount');
        if($request->appliedcouponcode_flag){
            $coupon_obj = Coupon::whereRaw('BINARY `coupon_code` = ?', $request->appliedcouponcode)->first();
            if($coupon_obj->discountType->slug == 'token_off' || $coupon_obj->discountType->slug == 'both'){
                $wallet_obj = Auth::user()->walletData;
                $wallet_obj->balance = $wallet_obj->balance + $coupon_obj->token_amt;
                $wallet_obj->save();
            }
            if($coupon_obj->discountType->slug == 'discount_percentage' || $coupon_obj->discountType->slug == 'both'){
                $amount = number_format($amount - (($amount * $coupon_obj->discount) / 100), 2);
            }
            $coupon_obj->count = $coupon_obj->count - 1;
            $coupon_obj->save();
        }
        $planname = $request->input('planname');
        $newplanbuy = $request->input('newplanbuy'); //user first time buy a plan

        $user = User::find(Auth::user()->id);
        $userid = $user->id;

        $description = array( "description" => 'You have Purchased plan : ' .$planname. ' of amount '.$amount);

        if ($chargeId) {
            $user = User::find(Auth::user()->id);
            $expiry_date = getExpiryDate($chargeId);
            if($newplanbuy == 1){
                $subscription = new Subscription;
                $subscription->user_id = $user->id;
                $subscription->name = 'main';
                $subscription->stripe_plan = $chargeId;
                $subscription->quantity = '1';
                $subscription->ends_at = $expiry_date;
                $subscription->save();
            }else{

                if($user->subscribed('main')) {
                    Subscription::where('user_id', Auth::user()->id)->where('name', 'main')
                        ->update(['stripe_plan' => $chargeId, 'ends_at' => $expiry_date]);
                }
            }


            $user->withdraw($amount, 'withdraw', $description);

            //NOTIFICATIONS START
            $admin = User::where('role_id', '=', 1)->first();
            $admin_id = $admin->id;

            if($newplanbuy == 1){
                $message = $user->displayname.", welcome to a world of possibilities with your ".$planname." premium plan from wallet.";
            }else{
                $message = "Congratulation ".$user->displayname."! You've successfully upgraded to ".$planname." premium plan from wallet. and your payment of ".$amount." Tokens was successfully received.";
            }

            $user_email = $user->user_email;
            if(!$user_email){
                $emaildata = array();
            }else{
                $emaildata = array(
                    'email' => $user->user_email,
                    'displayname' => $user->displayname,
                    'email_message' => $message
                );
            }            

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

            $sldata = array(
                'uuid' => $user->uuid,
                'message' => $message,
                'type' => 'Payment Notification'
            );

            $allnoticationdata = array(
                'emailtype' =>$emaildata,
                'messagetype' =>$messagedata,
                'notificationtype' =>$notficationdata,
                'sl_notificationtype' =>$sldata
            );

            \Event::fire(new UserAllNotification($allnoticationdata));
            //NOTIFICATIONS END
            return redirect()->to('subscription/success');
        }

        return redirect()->to('subscription/failed');
    }

    public function paywithwalletfeature(Request $request)
    {
        $chargeId = $request->input('plan_id');
        $amount = $request->input('amount');
        $planname = $request->input('planname');

        $user = User::find(Auth::user()->id);
        $userid = $user->id;

        $description = array( "description" => 'You have Purchased Featured plan : ' .$planname. ' for token '.$amount);

        if ($chargeId) {

            $user = User::find(Auth::user()->id);

            if ($user->subscribed('feature')) {
                Subscription::where('user_id', Auth::user()->id)->where('name', 'feature')
                    ->update(['stripe_plan' => $chargeId]);
                $user->withdraw($amount, 'withdraw', $description);
            }else{
                $subscription = new Subscription;
                $subscription->user_id = $user->id;
                $subscription->name = 'feature';
                $subscription->stripe_plan = $chargeId;
                $subscription->quantity = '1';
                $subscription->save();
            }

            //NOTIFICATIONS START
            $wallet = DB::table('wallets')->where('user_id',$user->id)->first();
            $wallet_amount = $wallet->balance;

            $admin = User::where('role_id', '=', 1)->first();
            $admin_id = $admin->id;

            $message = "Congratulation ".$user->displayname."! You've successfully upgraded to ".$planname." feature plan from wallet. and your payment of ".$amount." Tokens was successfully received.";

            $user_email = $user->user_email;
            if(!$user_email){
                $emaildata = array();
            }else{
                $emaildata = array(
                    'email' => $user->user_email,
                    'displayname' => $user->displayname,
                    'email_message' => $message
                );
            }

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

            $sldata = array(
                'uuid' => $user->uuid,
                'message' => $message,
                'type' => 'Payment Notification'
            );

            $allnoticationdata = array(
                'emailtype' =>$emaildata,
                'messagetype' =>$messagedata,
                'notificationtype' =>$notficationdata,
                'sl_notificationtype' =>$sldata
            );

            \Event::fire(new UserAllNotification($allnoticationdata));
            //NOTIFICATIONS END

            return redirect()->to('subscription/success');
        }else{
            return redirect()->to('subscription/failed');
        }
    }

    public function paywithinworld(Request $request)
    {
        return redirect()->to('parcel');
    }

    public function cancel(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $user->subscription('main')->cancel();
        if ($user->subscribed('main')) {
            return redirect()->to('subscription/success');
        }
        return redirect()->to('subscription/failed');
    }


    public function charge(Request $request)
    {
        $name = $request->input('chargeId');
        $stripeToken = $request->input('stripeToken');
        $user = User::find(Auth::user()->id);
        if (!$user->subscribed('main')) {
            $reponse = $user->newSubscription('main', $name)->create($stripeToken);
            if ($reponse->stripe_id) {
                return redirect()->to('subscription/success')->with('stripeToken', $stripeToken);
            }
        }
        return redirect()->to('subscription/failed')->with('stripeToken', $stripeToken);
    }

    public function success()
    {
        return view('user.success');
    }

    public function failed()
    {
        return view('user.failed');
    }

    public function edit($id)
    {
        $websiteSetting = self::setMetas($id);
        $subscription = Plan::find($id);

        $tokenamount = getWebsiteSettingsByKey('token_amount');

        if ($tokenamount) {
            $subscription->token = ($subscription->price * $tokenamount) * 100;
        }

        return view('membershipPlans.edit', compact('websiteSetting', 'id', 'subscription'));
    }

    public function setMetas($id)
    {
        $metaDatas = WebsiteSetting::where('meta_id', $id)->get();
        $newmetaInfo = array();
        if ($metaDatas) {
            foreach ($metaDatas as $metaData) {
                $newmetaInfo[$metaData->meta_key] = $metaData->meta_value;
            }
        }
        return $newmetaInfo;
    }

    public function update(Request $request, $id)
    {
        dd($request);
    }

    public function feturecharge(Request $request)
    {
        $name = $request->input('chargeId');
        $stripeToken = $request->input('stripeToken');
        $user = User::find(Auth::user()->id);
        if (!$user->subscribed('feature')) {
            $reponse = $user->newSubscription('feature', $name)->create($stripeToken);
            if ($reponse->stripe_id) {
                return redirect()->to('subscription/success')->with('stripeToken', $stripeToken);
            }
        }
        return redirect()->to('subscription/failed')->with('stripeToken', $stripeToken);
    }

    public function featureupgrade(Request $request)
    {
        $chargeId = $request->input('chargeId');
        if ($chargeId) {
            $user = User::find(Auth::user()->id);
            if ($user->subscription('feature')->onGracePeriod()) {
                $user->subscription('feature')->resume();
            }
            if ($user->subscribed('feature')) {
                $user->subscription('feature')->swap($chargeId);
            }
        }
        if ($user->subscribed('feature')) {
            return redirect()->to('subscription/success');
        }
        return redirect()->to('subscription/failed');
    }

    public function featurecancel(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $user->subscription('feature')->cancel();
        if ($user->subscribed('feature')) {
            return redirect()->to('subscription/success');
        }
        return redirect()->to('subscription/failed');
    }
    public function applyCoupon(Request $request){
        $plan_obj = Plan::where('plan_id', $request->plan_id)->first();
        $return_json['status'] = false;
        $return_json['message'] = 'Dodfgsdfgsdfgsdfgsdfgsdfgsdfgne';
        $coupon = Coupon::whereRaw('BINARY `coupon_code` = ?', $request->couponcode)->first();
        if(!$coupon){
            $return_json['status'] = false;
            $return_json['message'] = 'Coupon code is incorrect';
            return json_encode($return_json);
        }else{
            if(!$coupon->count){
                $return_json['status'] = false;
                $return_json['message'] = 'Coupon code is used';
                return json_encode($return_json);
            }
            if($coupon->expiry_date < Carbon::now()){
                $return_json['status'] = false;
                $return_json['message'] = 'Coupon code is expired';
                return json_encode($return_json);
            }
            $final_item_price = number_format($plan_obj->price - (($plan_obj->price * $coupon->discount) / 100), 2);
            $return_json['status'] = true;
            $return_json['message'] = 'Coupon code applied successfully ! <br> You have recieved '.$coupon->token_amt.' tokens and '.$coupon->discount.'% discount. <br> Your Item value will be <b>$'.$final_item_price.'</b>';
            $return_json['tokens'] = $coupon->token_amt;
            $return_json['discount'] = $coupon->discount;
            return json_encode($return_json);
        }
    }
}
