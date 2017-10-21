<?php

namespace App\Http\Controllers;

use App\Species;
use Auth;
use App\User;
use App\Usergroup;
use Newsletter;
use App\SearchResult;
use Illuminate\Http\Request;
use App\WebsiteSetting;
use App\Features;
use App\Subscription;
use App\Plan;
use App\SeekingRole;
use App\FamilyRole;
use App\Trials;
use Illuminate\Support\Arr;
use App\Reportblock;

class UserSearchController extends Controller
{

    public function __construct()
    {

    }


    public function index(Request $request){
        $seacrhresult = '';
        if(Auth::user()){
            $seacrhresult = SearchResult::where('user_id',Auth::user()->id)->first();
        }

        $blockIds = getblockeduser();
        if($request->usergroup){
            if(isthisSubscribed()){
                $query = User::where('group',$request->usergroup)
                       ->where('gender',$request->gender)
                       ->whereBetween('age', [intval($request->minage), intval($request->maxage)])
                       ->orderBy('last_activity', 'DESC')
                       ->where('role_id', '!=',  1 )
                       ->whereNotIn('id', $blockIds)
                       ->where('id', '!=',  Auth::user()->id );
                       //->paginate(12);

                if($request->has('species') && $request->get('species')!='') {
                    $query->where('species_id', '=',  $request->get('species') );
                }

                $users=$query->paginate(12);

            }else{
                if(Auth::user()){
                    $users = User::where('group',$request->usergroup)->orderBy('last_activity', 'DESC')->where('role_id', '!=',  1 )->where('id', '!=',  Auth::user()->id )->whereNotIn('id', $blockIds)->paginate(12);
                }
                else{
                    $users = User::where('group',$request->usergroup)->orderBy('last_activity', 'DESC')->where('role_id', '!=',  1 )->paginate(12);
                }
            }

        }
        else{

            if(Auth::user()){
                $users = User::orderBy('last_activity', 'DESC')->where('role_id', '!=',  1 )->where('id', '!=',  Auth::user()->id )->whereNotIn('id', $blockIds)->paginate(12);
            }
            else{
                $users = User::orderBy('last_activity', 'DESC')->where('role_id', '!=',  1 )->paginate(12);
            }
        }

        $advanceSearchEnableorNot = 0;
        if(Auth::user()){
            $advanceSearchEnableorNot = Features::isUserAccessSearch();
        }
        $group = Usergroup::get();

        //$genders = GenderRole::get();
        if(Auth::user()){

          $userdata = Usergroup::find(Auth::user()->group);
          if($userdata){
            $data = json_decode($userdata->genderrole);
            $searchs = json_decode($userdata->searchs);
            //$genders = GenderRole::find($data);
            $group = Usergroup::find($searchs);
          }
          else{
            //$genders = GenderRole::get();
            $group = Usergroup::get();
          }

        }

        if(Auth::user()){

        /** get User Group All subscription plans **/
        $usergroup = Usergroup::find(Auth::user()->group);
        $memberShip_plans = json_decode( $usergroup->membership_plans );
        $getAllplans = Plan::whereIn('id', $memberShip_plans)->orderBy("price", "DESC")->first();

        // get current user's subscription plans
        $user_plans = Subscription::where('user_id', Auth::user()->id)->get();
        $getUserPLans = array();
        $checkPlan = 0;
        $upgradeStatus = 0;


        //If users have any subscription plan

        if($user_plans){

             foreach($user_plans as $plan){
                // get each User plan information
                $singlePlanInfo = Plan::where('plan_id', $plan->stripe_plan)->first();

                if($singlePlanInfo){
                    // check if user plan is related to current Membership Plans
                    if(in_array($singlePlanInfo->id, $memberShip_plans)){
                            $checkPlan++;
                            array_push($getUserPLans, $singlePlanInfo->id);
                    }
                }

            }

            if($checkPlan > 0){
                $getUsersPlan = Plan::whereIn('id', $getUserPLans)->orderBy("price", "DESC")->first();

                // comapre the plan if higher amount plan exists
                if($getAllplans->price > $getUsersPlan->price){
                    $upgradeStatus = 1;
                }else{
                    $upgradeStatus = 0;
                }
            }
        }else{

            $upgradeStatus = 1;
        }
      }
      else{

        $upgradeStatus = 0;

      }

        $allgroup = Usergroup::get();
        $seekingroles  = SeekingRole::get();
        $familyroles  = FamilyRole::get();
        $species  = Species::orderBy('id', 'asc')->get();
        return view('browse', compact('users', 'group', 'seacrhresult', 'advanceSearchEnableorNot','allgroup','species','upgradeStatus','seekingroles','familyroles'));
    }

    public function featuredmembers(Request $request){
        $users = User::orderBy('last_activity', 'DESC')->where('role_id', '!=',  1 )->paginate(12);
        return view('featuredmembers', compact('users'));
    }

    public function newBrowse(Request $request){

      $ids = array();
      // check if user has success adoption
        if(Auth::user()){
            $checkAdoption = Trials::WhereRaw('((user_id = ' . Auth::user()->id.') OR (matcher_id = ' . Auth::user()->id.' ))')
                   ->where('adopt_is_accepted', 1)
                   ->where('adopt_is_dissolve', 0)
                   ->get();
            // get user ids
            if($checkAdoption){
                foreach($checkAdoption as $adoption){
                      if($adoption->user_id == Auth::user()->id){
                          array_push($ids, $adoption->matcher_id);
                      }else{
                          array_push($ids, $adoption->user_id);
                      }
                      array_push($ids, Auth::user()->id);
                }
            }

            // get blocked users
            $blockIds = Reportblock::WhereRaw('((user_id = ' . Auth::user()->id.') OR (block_id = ' . Auth::user()->id.' ))')->pluck('block_id')->toArray();

            if(count($blockIds) > 0){
                foreach($blockIds as $key => $value){
                    array_push($ids, $value);
                }
            }
            
            if(count($ids) > 0){
                $ids = array_unique($ids);
                $users = User::WhereHasRole(['Free User'])->whereNotIn('id', $ids)->orderBy('last_activity', 'DESC')->with('isFeatureUser')->paginate(16);
            }else{
                $users = User::WhereHasRole(['Free User'])->orderBy('last_activity', 'DESC')->with('isFeatureUser')->paginate(16);
            }
        }else{
            $users = User::orderBy('last_activity', 'DESC')->where('role_id', '!=',  1 )->with('isFeatureUser')->paginate(16);
        }
     
        $divs_per_row = 4;
        $allgroup = Usergroup::get();
        $seekingroles  = SeekingRole::get();
        $advanceSearchEnableorNot = 0;
        if(Auth::user()){
            $advanceSearchEnableorNot = Features::isUserAccessSearch();
        }
        $species  = Species::orderBy('id', 'asc')->get();
        if(Auth::user()){
            $usergroup = Usergroup::find(Auth::user()->group);
            $memberShip_plans = json_decode( $usergroup->membership_plans );
            $getAllplans = Plan::whereIn('id', $memberShip_plans)->orderBy("price", "DESC")->first();
            $user_plans = Subscription::where('user_id', Auth::user()->id)->get();
            $getUserPLans = array();
            $checkPlan = 0;
            $upgradeStatus = 0;

            if($user_plans){
                foreach($user_plans as $plan){
                    $singlePlanInfo = Plan::where('plan_id', $plan->stripe_plan)->first();
                    if($singlePlanInfo){
                        if(in_array($singlePlanInfo->id, $memberShip_plans)){
                        $checkPlan++;
                            array_push($getUserPLans, $singlePlanInfo->id);
                        }
                    }
                }
                if($checkPlan > 0){
                    $getUsersPlan = Plan::whereIn('id', $getUserPLans)->orderBy("price", "DESC")->first();
                    if($getAllplans->price > $getUsersPlan->price){
                        $upgradeStatus = 1;
                    }else{
                        $upgradeStatus = 0;
                    }
                }
            }else{
                $upgradeStatus = 1;
            }
        }else{
            $upgradeStatus = 0;
        }
        $randomUser = User::inRandomOrder()->take(3)->get();
        $familyroles  = FamilyRole::get();

        return view('featuredmembers-new', compact('users', 'divs_per_row','allgroup','seekingroles','advanceSearchEnableorNot','species','upgradeStatus','randomUser','familyroles'));
    }

    public function newsletter(Request $request){
        $response = array('status' => 0 );
        $email = $request->email;
        if($email){
            Newsletter::subscribe($email);
            $response = array('status' => 1 );
        }
        return json_encode($response);
    }

    public function filteruser( Request $request ){


        $response = array('status' => 0);
        $group = $request->input('group');
        $gender = $request->input('gender');
        $users = User::where('group', $group)->where('gender', $gender)->get();
        if( count( $users ) ){
            ob_start();
            foreach( $users as $user ){
                ?>
                <div class="col-sm-3 col-md-3 text-center usercolumn">
                    <?php
                        $profilepic = ( $user->profile_pic )? 'uploads/'.$user->profile_pic : 'images/default.png';
                    ?>
                    <a href="<?php echo route('viewprofile', base64_encode( $user->id )); ?>">
                        <img src="<?php echo url( $profilepic ); ?>" alt="<?php echo $user->name ?>">
                        <h3><?php echo @$user->name; ?> </h3>
                        <span><?php echo @$user->usergroup->title; ?></span>
                    </a>
                </div>
            <?php
            }
            $response['htmlinfo'] =  ob_get_clean();
        }else{
            $response['htmlinfo'] =  "<span class='no_result_found'>No result found</span>";
        }
        $response['status'] =  1;
        return json_encode( $response );
    }
    public function saveSearchResult( Request $request){


        $seacrhresult = SearchResult::where('user_id',Auth::user()->id)->first();
        if($seacrhresult){
            $search = SearchResult::find($seacrhresult->id);
        }
        else{
            $search = new SearchResult;
        }

        $search->user_id = Auth::user()->id;
        $search->usergroup = Auth::user()->group;
        $search->seeking_role = $request->input('seeking_role_search');
        $search->family_role = $request->input('family_role_search');
        $search->species_id = $request->input('species_search');
        $search->minage = $request->input('minage_search');
        $search->maxage = $request->input('maxage_search');
        $search->gender = $request->input('family_role_search');

        $search->save();
        $response['status'] =  1;
        return json_encode( $response );
    }

}
