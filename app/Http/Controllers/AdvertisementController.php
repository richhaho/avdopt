<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Carbon\Carbon;
use App\User;
use App\Banner;
use App\Usergroup;
use App\TargetAudience;
use Session;
use Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Advertisement;
use App\UsersBanner; 
use Intervention\Image\Facades\Image;
use App\Helpers\ImageHelper;
use App\AdsSubscriptions;

class AdvertisementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $advertisement  = Advertisement::where('is_deleted',0)->get();
        return view('admin.advertisement.showalladvertise',compact('advertisement'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $banner = Banner::all();
        $targetaudience= TargetAudience::all();
        return view('admin.advertisement.createadvertise',compact('banner','targetaudience'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'title' => 'required',
            'description'  => 'required',
            'banner' => 'required',
            'targetaudience' => 'required',
            'banner_plan'  => 'required',
            'plan_period' => 'required'
        ]);
        
        if($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors( $validation->errors() );
        }
        $banner_price =[];
        $target_price =[];
        $price ='';
        $adsplan = $request->input('banner_plan'); 
        foreach($request->banner as $key => $value)
        {
            $banners = Banner::find($value);
            if($adsplan == 'Weekly')
            {
                $price = $banners->weekly_price;
            }else if($adsplan == 'Monthly')
            {
                $price = $banners->monthly_price;
            }
            array_push($banner_price, $price);
        }
        foreach ($request->input('targetaudience') as $key => $value1) {
            $targetaudience = TargetAudience::find($value1);
                array_push($target_price, $targetaudience->price);
        }

        $finalprice = array_sum($target_price) + array_sum($banner_price); 
        $advertisement =new Advertisement();
        $advertisement->user_id = Auth::user()->id;
        $advertisement->title = $request->input('title');
        $advertisement->description = $request->input('description');
        $advertisement->banner_ids = implode(',',$request->input('banner'));
        $advertisement->target_audience_ids = implode(',',$request->input('targetaudience'));
        $advertisement->total_amt = $finalprice;
        $advertisement->banner_plan = $request->input('banner_plan');
        $advertisement->plan_period = $request->input('plan_period');
        $advertisement->save();

        return redirect('admin/advertisement')->with('success', 'Advertisement Created Successfully');        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $advertisement  = Advertisement::where('id', $id)->first();
        return view('admin.advertisement.showads', compact('advertisement'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $advertisement = Advertisement::find($id);
        $banner = Banner::all();
        $targetaudience= TargetAudience::all();
        return view('admin.advertisement.editadvertise', compact('advertisement','banner','targetaudience'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validation = Validator::make($request->all(), [
            'title' => 'required',
            'description'  => 'required',
            'banner' => 'required',
            'targetaudience' => 'required',
            'banner_plan'  => 'required',
            'plan_period' => 'required'
        ]);
        
        if($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors( $validation->errors() );
        }
        $banner_price =[];
        $target_price =[];
        $price ='';
        $adsplan = $request->input('banner_plan'); 
        foreach($request->banner as $key => $value)
        {
            $banners = Banner::find($value);
            if($adsplan == 'Weekly')
            {
                $price = $banners->weekly_price;
            }else if($adsplan == 'Monthly')
            {
                $price = $banners->monthly_price;
            }
            array_push($banner_price, $price);
        }
        foreach ($request->input('targetaudience') as $key => $value1) {
            $targetaudience = TargetAudience::find($value1);
                array_push($target_price, $targetaudience->price);
        }

        $finalprice = array_sum($target_price) + array_sum($banner_price); 
        $advertisement =Advertisement::find($id);
        $advertisement->user_id = Auth::user()->id;
        $advertisement->title = $request->input('title');
        $advertisement->description = $request->input('description');
        $advertisement->banner_ids = implode(',',$request->input('banner'));
        $advertisement->target_audience_ids = implode(',',$request->input('targetaudience'));
        $advertisement->total_amt = $finalprice;
        $advertisement->banner_plan = $request->input('banner_plan');
        $advertisement->plan_period = $request->input('plan_period');
        $advertisement->save();

        return redirect('admin/advertisement')->with('success', 'Advertisement Updated Successfully');  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function paidads()
    {
       $subscriptions  = AdsSubscriptions::with('user')->get();
       return view('admin.advertisement.paidadvertises',compact('subscriptions'));
    }

    public function endedAds()
    {
       $currentdate = Carbon::now()->toDateTimeString();
       $endedads  = AdsSubscriptions::with('advertisement','user')->where('ended_at',$currentdate)->get();
       return view('admin.advertisement.endedadvertises',compact('endedads'));
    }

    public function suspendAdvertisement(Request $request)
    {
        $advtid = $request->advtid;
        $adssubscriptions = AdsSubscriptions::find($advtid);
        $adssubscriptions->status = 'Suspend';
        $adssubscriptions->save();
        return redirect('admin/advertisement/paid');
    }

    public function deleteAdvertisement(Request $request)
    {
        $deleteid = $request->deleteid;
        $advertisement = Advertisement::find($deleteid);
        $advertisement->is_deleted = 1;
        $advertisement->save();
        return redirect('admin/advertisement');
    }

    public function deletePaidAdvertisement(Request $request)
    {
        $deleteid = $request->deleteid;
        $adssubscriptions = AdsSubscriptions::find($deleteid);
        $adssubscriptions->status = 'Deleted';
        $adssubscriptions->save();
        return redirect('admin/advertisement/paid');
    }
    

    public function showbanners()
    {
        $banners = Banner::orderBy('id', 'desc')->get();
        return view('admin.advertisement.banner', compact('banners'));
    }

    public function addbanner()
    {        
        return view('admin.advertisement.addbanner');
    }

    public function savebanner(Request $request)
    {        
        $bannerwidth = $request->input('bannerwidth');
        $bannerheight = $request->input('bannerheight');
        $bannerlocation = $request->input('bannerlocation');
        $weeklyprice = $request->input('weeklyprice');
        $monthlyprice = $request->input('monthlyprice');
        
        $validation = Validator::make($request->all(), [
            'bannerwidth' => 'required|integer',
            'bannerheight' => 'required|integer',
            'bannerlocation' => 'required',
            'weeklyprice' => 'required|integer',
            'monthlyprice' => 'required|integer',
        ]);

        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors( $validation->errors() );
        }else{
            $banner = new Banner();
            $banner->banner_width = $bannerwidth;
            $banner->banner_height = $bannerheight;
            $banner->page_location = $bannerlocation;
            $banner->weekly_price = $weeklyprice;
            $banner->monthly_price = $monthlyprice;
            $banner->save();

            return redirect('admin/advertisement/addbanner')->with('success', 'Banner Created Successfully!!');
        }        
    }

    public function editbanner($id, Request $request)
    {
        $banner = Banner::find($id);
        return view('admin/advertisement/editbanner', compact('banner'));   
    }

    public function updatebanner($id, Request $request)
    {
        $validation = Validator::make($request->all(), [
            'bannerwidth' => 'required|integer',
            'bannerheight' => 'required|integer',
            'bannerlocation' => 'required',
            'weeklyprice' => 'required|integer',
            'monthlyprice' => 'required|integer',
        ]);

        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors( $validation->errors() );
        }else{
            $banner = Banner::find($id);
            $banner->banner_width = request('bannerwidth');
            $banner->banner_height = request('bannerheight');
            $banner->page_location = request('bannerlocation');
            $banner->weekly_price = request('weeklyprice');
            $banner->monthly_price = request('monthlyprice');
            $banner->updated_at = Carbon::now();
            $banner->save();
            return redirect('/admin/advertisement/banners')->with('success', 'Banner Updated Successfully!!');            
        }   
    }

    public function deletebanner($id, Request $request)
    {
        $banner = Banner::findorfail($id);
        $banner->delete();
        return redirect()->back()->with('success', 'Banner Deleted Successfully!!');
    }

    public function showtargetaudiances()
    {        
        $targetaudience = TargetAudience::get();
        return view('admin.advertisement.targetaudiances', compact('targetaudience'));
    }

    public function addtargetaudiance()
    {
        $usergroups = Usergroup::get();
        return view('admin.advertisement.createtargetaudiance', compact('usergroups'));
    }

    public function savetargetaudiance(Request $request)
    {
        $multiple_usr_groups = $request->input('multiple_usr_groups');
        $usergroup_price = $request->input('usergroup_price');        

        $validation = Validator::make($request->all(), [
            'multiple_usr_groups' => 'required',
            'usergroup_price' => 'required|integer'
        ]);

        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors( $validation->errors() );
        }else{
            $usergroupname = '';
            $usergrpnames = array();
            $usergrouplist = implode(',', $multiple_usr_groups);
            foreach ($multiple_usr_groups as $value) {
                $usergroup = Usergroup::find($value);
                array_push($usergrpnames, $usergroup->title);
            }
            $usergroupname = implode(',', $usergrpnames);

            $targetaudience = new TargetAudience();
            $targetaudience->usergroup_ids = $usergrouplist;
            $targetaudience->usergroup_names = $usergroupname;
            $targetaudience->price = $usergroup_price;
            $targetaudience->save();

            return redirect('admin/advertisement/addtargetaudiance')->with('success', 'Target Audience Created Successfully!!');
        }
    }

    public function edittargetaudiance($id, Request $request)
    {
        $targetaudience = TargetAudience::find($id);
        $usergroups = Usergroup::get();
        return view('admin/advertisement/edittargetaudiance', compact('targetaudience', 'usergroups'));
    }

    public function updatetargetaudiance($id, Request $request)
    {
        $multiple_usr_groups = $request->input('multiple_usr_groups');
        $usergroup_price = $request->input('usergroup_price');        

        $validation = Validator::make($request->all(), [
            'multiple_usr_groups' => 'required',
            'usergroup_price' => 'required|integer'
        ]);

        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors( $validation->errors() );
        }else {
            $usergroupname = '';
            $usergrpnames = array();
            $usergrouplist = implode(',', $multiple_usr_groups);
            foreach ($multiple_usr_groups as $value) {
                $usergroup = Usergroup::find($value);
                array_push($usergrpnames, $usergroup->title);
            }
            $usergroupname = implode(',', $usergrpnames);

            $targetaudience = TargetAudience::find($id);
            $targetaudience->usergroup_ids = $usergrouplist;
            $targetaudience->usergroup_names = $usergroupname;
            $targetaudience->price = $usergroup_price;
            $targetaudience->updated_at = Carbon::now();
            $targetaudience->save();

            return redirect('admin/advertisement/targetaudiances')->with('success', 'Target Audience updated successfully!!');
        }
    }

    public function deletetargetaudiance($id, Request $reques)
    {
        $targetaudience = TargetAudience::findorfail($id);
        $targetaudience->delete();
        return redirect('admin/advertisement/targetaudiances')->with('success', 'Target Audience deleted successfully!!');
    }

    public function approveads(Request $request, $id)
    {
        $currentdate = Carbon::now()->toDateTimeString();
        $update_values = [
            'approve' => 1,
            'status' => 'Active',
            'start_at' => $currentdate
        ];
        AdsSubscriptions::where('id', $id)->update($update_values);

        return redirect('admin/advertisement/paid')->with('message', 'Advertisement approved successfully!!');
    }
}
