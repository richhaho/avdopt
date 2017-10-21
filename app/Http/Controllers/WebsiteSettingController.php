<?php

namespace App\Http\Controllers;

use App\WebsiteSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\SubscriptionController;

class WebsiteSettingController extends Controller
{
    public function __construct()
	{
		$this->middleware('auth');
		/*\Stripe\Stripe::setApiKey(env("STRIPE_SECRET"));*/
	}

    //Display Token Price
    public function websitesettingtoken()
    {
        $metaData = self::setMetas();
        return view('admin.websitesetting.settingstoken', compact('metaData'));
    }

    public function getMembershipSettings($id)
    {
        $metaData = self::setMetas();
        $planlist = SubscriptionController::getPlanlist();
        return view('admin.websitesetting.membership-limitation', compact('metaData', 'planlist', 'id'));
    }

    public function saveWebsiteSetting(Request $request, $id=0){

      
        $planData=\App\Plan::find($id);
        if($planData){
            $planData->description=$request->description;
            $planData->trial_period=$request->trial_period_days;
            $planData->price = $request->price;
            $planData->tokens = $request->tokens;
            $planData->save();
        }


        $websiteSettings = $request->input('websiteSettings');
        if( isset( $websiteSettings['sub_private_messages_'.$id] ) ){
            $websiteSettings['sub_my_hearts_'.$id] = isset( $websiteSettings['sub_my_hearts_'.$id] )? 1 : 0;
            $websiteSettings['sub_advance_search_'.$id] = isset( $websiteSettings['sub_advance_search_'.$id] )? 1 : 0;
        }
        if( count( $websiteSettings ) ){
            foreach( $websiteSettings as $fieldId => $fieldVal ){
                self::addMetaValue( $fieldId, $fieldVal, $id );
            }
            return redirect()->back()->with('success', 'Settings saved successfully');
        }
    }

    public function setMetas(){
        $metaDatas = WebsiteSetting::all();
        $newmetaInfo = array();
        if( $metaDatas ){
            foreach( $metaDatas as $metaData ){
                $newmetaInfo[$metaData->meta_key] = $metaData->meta_value;
            }
        }
        return $newmetaInfo;
    }

    public function addMetaValue( $metaKey, $metaValue, $metaID ){
        $data = WebsiteSetting::where('meta_key', $metaKey)->first();
        if( $data ){
            $data->meta_value = $metaValue;
        }else{
            $data = new WebsiteSetting;
            $data->meta_id = $metaID;
            $data->meta_key = $metaKey;
            $data->meta_value = $metaValue;
        }
        $data->save();
    }
    public function featurePricing( $id ){
        $metaData = self::setMetas();
        return view('admin.websitesetting.featurepricing', compact('metaData', 'id'));
    }

    //Display Scrren Name
    public function websitesettingScreenName()
    {
        $metaData = self::setMetas();
        return view('admin.websitesetting.screennameSetting', compact('metaData'));
    }

    public function freeUsersFeatureSetting()
    {
        $metaData = self::setMetas();
        return view('admin.websitesetting.freemembersetting', compact('metaData'));
    }

    public function registerLabels()
    {
        $metaData = self::setMetas();
        return view('admin.websitesetting.registerlabels', compact('metaData'));
    }

}
