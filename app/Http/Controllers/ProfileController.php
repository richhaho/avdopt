<?php

namespace App\Http\Controllers;

use App\Species;
use Auth;
use DB;
use App\User;
use App\MyFun;
use App\Like;
use App\Usergroup;
use App\Photoalbum;
use App\Questionnaires;
use App\Reportblock;
use App\Note;
use App\Heart;
use App\Match;
use App\Trials;
use App\TrialLocation;
use App\Announcements;
use App\Message;
use App\Features;
// use Alert;
use App\FeatureUses;
use App\Notification;
use App\Review;
use App\WordsSecurity;
use App\UsersFamilyRole;
use App\FamilyRole;
// use App\Reportblock;
use App\GenderRole;
use Session;
use App\Helpers\ImageHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use App\Bannedips;
use App\EthnicityGroup;
use App\Plan;
use App\Page;
use App\MatchQuestCategory;
use App\Answer;


class ProfileController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        $userid = Auth::user()->id;
        $user = User::with('species')->find($userid);
        $groupid = $user->group;
        $useranswer = DB::table('answers')->where('user_id', $userid)->where('group_id', $groupid)->first();
        $likecount = Like::where('isliked', 1)->where('user_id', $user->id)->count();
        $familyRoles = UsersFamilyRole::where('user_id', $user->id)->pluck('family_role_id')->toArray();
        if (count($familyRoles) > 0) {
            $getFamilyRoleInfo = FamilyRole::whereIn('id', $familyRoles)->get();
        } else {
            $getFamilyRoleInfo = null;
        }


        $title_by_page = "My Profile";
        if (Auth::user()->role_id == 1 || Auth::user()->role_id == 3) {
            return view('profile.index', compact('user', 'likecount', 'useranswer', 'title_by_page', 'getFamilyRoleInfo'));
        } else {
            return view('profile.indexuser', compact('user', 'likecount', 'useranswer', 'title_by_page', 'getFamilyRoleInfo'));
        }
    }

    public function setMetas()
    {
        $metaDatas = \App\WebsiteSetting::all();
        $newmetaInfo = array();
        if ($metaDatas) {
            foreach ($metaDatas as $metaData) {
                $newmetaInfo[$metaData->meta_key] = $metaData->meta_value;
            }
        }
        return $newmetaInfo;
    }

    public function dropzoneUploadFile(Request $request)
    {
        $isfeature = Features::isHeCanUploadMaxImages();

        $subs = getCurrentUserPlan();

        $current_sub_id = $subs->id;
        $metaData = self::setMetas();

        $subDate = subscriptionStartDate();
        $usedimages = FeatureUses::where('userid', Auth::user()->id)->where('featurid', $metaData['sub_max_images_upload_' . $current_sub_id])->get();

        $userusedimages = FeatureUses::where('userid', Auth::user()->id)->where('subscriptionid', '=', $current_sub_id)->where('featurid', 'sub_max_images_upload_' . $current_sub_id)->get();
        
        if ($metaData['sub_max_images_upload_' . $current_sub_id] == '-1') {
            $text = 'unlimited';
        } else {
            $text = $metaData['sub_max_images_upload_' . $current_sub_id];
        }
        
        if (!$isfeature) {
            //return json_encode('You can upload ' . $text . ' images in your albumb and you used ' . count(usedImagesInAlbum()) . ' images');
            if(count($userusedimages)>=3){
                $result['error_fupgrade'] = 'You have used up your photo limit! Upgrade to upload more photos';
                return json_encode($result);
            }            
        }
        $user = Auth::user();
        $imageName = time() . '.' . $request->file->getClientOriginalExtension();
        $request->file->move(public_path('uploads'), $imageName);
        $upload = new Photoalbum();
        $upload->image = $imageName;
        $upload->user_id = $user->id;
        $upload->save();
        $feature = 'sub_max_images_upload_';
        if ($isfeature == 2) {
            $feature = 'token_max_images_upload_value_';
        }
        FeatureUses::storeFeatureUsase($feature, $isfeature);
        // return response()->json(['success'=>$imageName]);
        $userphotoalbum_images = Photoalbum::where('user_id', Auth::user()->id)->get();
        $userphotoalbum_images_upd = 3 - count($userphotoalbum_images);

        if($userphotoalbum_images_upd <= 0){
            $result['error_upgrade'] = 'You have used up your photo limit! Upgrade to upload more photos';
        }else{
            $result['error_normal'] = 'You can upload ' . $userphotoalbum_images_upd . ' images in your albumb';
        }
        return json_encode($result);
    }

    public function edit()
    {
        $user = Auth::user();
        $myfuns = MyFun::all();
        $familyRoles = FamilyRole::all();
        $ethnicityGroups = EthnicityGroup::all();
        $UsersFamilyRole = UsersFamilyRole::where('user_id', $user->id)->pluck('family_role_id')->toArray();
        $grouprole = Usergroup::get();
        $groupid = $user->group;
        $groupInfo = Usergroup::find($groupid);
        $getUserGender = app('App\Http\Controllers\UsergroupController')->getGroupProfile($groupid);

        $useranswer = DB::table('answers')->where('user_id', $user->id)->where('group_id', $groupid)->first();
        $questionnary = Questionnaires::where('group_id', 'LIKE', "%{$groupid}%")->get();

        $species = Species::orderBy('id', 'asc')->get();
        //get all adoptions data
        $myadoptions = Trials::WhereRaw('((user_id = ' . $user->id.') OR (matcher_id = ' . $user->id .' ))')
                ->Where('adopt_is_accepted', 1)
                ->get();
        //get all dissolved adoptions data
        $mydissolvedadoptions = Trials::WhereRaw('((user_id = ' . $user->id.') OR (matcher_id = ' . $user->id .' ))')
                        ->Where('adopt_is_dissolve', 1)
                        ->get();

        $title_by_page = "Edit Profile";

        $metaData = self::setMetas();
        // get Username change amount
        $usernameChangeAmount = $metaData['token_username_change_' . $user->group];

        $usernameChangeCount = 1;
        if (isthisSubscribed()) {
            $usedname = FeatureUses::where('userid', Auth::user()->id)->where('featurid', $metaData['sub_username_change_' . getCurrentUserPlan()->id])->get();
            if ($metaData['sub_username_change_' . getCurrentUserPlan()->id] == '-1') {
                $usernameChangeCount = 1; //unlimited
            } else {

                $text = $metaData['sub_username_change_' . getCurrentUserPlan()->id];

                //get remaining change count
                if (isset($text) && is_numeric($text)) {
                    if (null != usedUserChangeName()) {

                        $remainingCount = $text - count(usedUserChangeName());
                        if ($remainingCount >= 0) {
                            $usernameChangeCount = $remainingCount;
                        } else {
                            $usernameChangeCount = 0;
                        }
                    }
                }
            }
        }else{
          $usernameChangeCount = 0;
        }

        $termContent = Page::findOrFail(config('params.pages.terms'));
        $policyContent = Page::findOrFail(config('params.pages.policy'));

        return view('profile.edit', compact('grouprole', 'user', 'questionnary', 'useranswer', 'myfuns', 'species', 'groupid', 'title_by_page', 'groupInfo', 'familyRoles', 'UsersFamilyRole', 'getUserGender', 'usernameChangeCount', 'usernameChangeAmount', 'ethnicityGroups','termContent','policyContent','myadoptions','mydissolvedadoptions'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();

        // save username change
        if ($request->get("action_submit") == 'action_submitUsername') {
            $this->validate($request, [
                'name' => 'required'
            ]);

            $metaData = self::setMetas();
            $text = '';
            if (isthisSubscribed()) {
                $usedname = FeatureUses::where('userid', Auth::user()->id)->where('featurid', $metaData['sub_username_change_' . getCurrentUserPlan()->id])->get();
                if ($metaData['sub_username_change_' . getCurrentUserPlan()->id] == '-1') {
                    $text = 'unlimited';
                } else {
                    $text = $metaData['sub_username_change_' . getCurrentUserPlan()->id];
                }
            }

            $successText = '';
            $profile = User::findorfail($id);
            $profile->ethnicity_group_id = request('ethnicity_group');
            $getWords = WordsSecurity::pluck('title')->toArray();
            $isfeature = Features::isUserCanChangeUserName();

            if ($isfeature) {
              $min = ($metaData['screen_name_minimum']) ? $metaData['screen_name_minimum'] : '4';
              $max = ($metaData['screen_name_maximum']) ? $metaData['screen_name_maximum'] : '6';
              $user_enter_name = strlen(trim(request('name')));
              if ($user_enter_name < $min ) {
                  return redirect('/profile/edit')->with('warning', 'Username required minimum '. $min .' characters');
              } elseif ($user_enter_name > $max ) {
                  return redirect('/profile/edit')->with('warning', 'In Username maximum '. $max .' characters are allowed');
              }

              $compareUsernameWordsStrcmp = '';

              $user_second_life_full_name = explode(',',preg_replace('/\s+/',' ',$user->second_life_full_name));
              $getWords = array_merge($getWords,$user_second_life_full_name);

                $compareUsernameWords = preg_replace('/\b(\w*' . strtolower(implode('|', $getWords)) . '\w*)\b/', '', strtolower(request('name')));
                if(!empty($compareUsernameWords)){

                  $compareUsernameWordsStrcmp = strcmp(strtolower(preg_replace('/\s+/','',request('name'))), strtolower(preg_replace('/\s+/','',$compareUsernameWords)));
                }else{
                  $compareUsernameWordsStrcmp = '-1';
                }

                if ($compareUsernameWordsStrcmp !=0 ) {
                    return redirect('/profile/edit')->with('danger', 'Sorry, that username is not allowed; please choose another username.');
                } else {

                    $profile->displayname = request('name');
                    $profile->save();
                    $successText = 'Username Changed successfully! ';
                }
            } else {
                $successText = "Sorry! Please subscribe to update your name";
            }

            if (!isthisSubscribed()) {
                return redirect('/profile/edit')->with('warning', 'Account Settings Updated, Please subscribe to update your name.');
            }
            $feature = 'sub_username_change_';
            if ($isfeature == 2) {
                $feature = 'token_username_change_value_';
            }
            FeatureUses::storeFeatureUsase($feature, $isfeature);
            $mess = '.';

            if ((null != usedUserChangeName()) && count(usedUserChangeName()) > 0) {

                $mess = ' and you tried to changed it in ' . count(usedUserChangeName()) . ' times.';

                //get remaining change count

                if (is_numeric($text)) {
                    $remainingCount = $text - count(usedUserChangeName());
                    if ($remainingCount > 0) {
                        $usernameChangeCount = $remainingCount;
                        $successText = "Username changed successfully!. You can change your name remaining " . $usernameChangeCount . " times more.";
                    } elseif ($remainingCount < 0) {
                        $usernameChangeCount = 0;
                        $successText = "Sorry! Please subscribe to update your name";
                    } else {
                        $successText = "Username changed successfully!. Please subscribe to update your name in future";
                        $usernameChangeCount = 0;
                    }
                    Session::flash('usernameChangeCount', $usernameChangeCount);
                }
            }

            return redirect('/profile/edit')->with('success', $successText);
        } else {
            // Else update other Profile SETTINGS
            $getWords = WordsSecurity::pluck('title')->toArray();
            $this->validate($request, [
                'age' => 'required|numeric|max:99',
                'about' => 'required',
            ]);
            $profile = User::findorfail($id);
            if ($profile->myfuns = request()) {
                $profile->myfuns = json_encode(request('myfuns'));
            }

            $userid = Auth::user()->id;
            $user = User::with('species')->find($userid);




            $compareAboutWordsStrcmp = '';



            $user_second_life_full_name = explode(',',preg_replace('/\s+/',' ',$user->second_life_full_name));
            if(request('name'))
            $user_second_life_full_name[] = request('name');

            $getWords = array_merge($getWords,$user_second_life_full_name);
        
            $compareAboutWords = preg_replace('/\b(\w*' . strtolower(implode('|', $getWords)) . '\w*)\b/', '******', strtolower(request('about')));
            $search = "******"  ;
            if (strpos($compareAboutWords, $search) !== false) {
                $compareAboutWordsStrcmp = 1;
            }



            // save family role
            if (request('family_role') != null) {

                if (count(request('family_role')) > 0) {
                    //delete all family existing roles
                    DB::table("users_family_roles")->where("user_id", Auth::user()->id)->delete();

                    for ($i = 0; $i < count(request('family_role')); $i++) {

                        DB::table("users_family_roles")
                            ->insert([
                                'user_id' => Auth::user()->id,
                                'family_role_id' => request('family_role')[$i]
                            ]);
                    }

                }

            }
            $profile->group = request('user_group');
            $profile->gender = request('gender');
            $profile->age = request('age');
            $profile->species_id = request('species_id');
            $profile->about_me = $compareAboutWords;
            $profile->ethnicity_group_id = request('ethnicity_group');
            $profile->save();
            if ($compareAboutWordsStrcmp != 0) {
                return redirect('/profile/edit')->with('warning', 'You have used a word that is not allowed; we have filtered it! Please remove the ****** from about for next time.');
            }else{
              return redirect('/profile/edit')->with('success', 'Account Settings Updated.');
            }

        }
    }

    public function profilePicupdate(Request $request)
    {
        $isfeature = Features::isHeCanChangeProfilePic();
        if (!$isfeature) {
            return redirect('/profile/edit')->with('success', 'Your monthly limit over to update profile picture. Please subscribe.');
        }
        $user = Auth::user();
        if (null != $request->file('avatar')) {
            $imageName = time() . '.' . $request->file('avatar')->getClientOriginalExtension();
            $request->file('avatar')->move(public_path('uploads'), $imageName);
            $user->profile_pic = $imageName;
            $user->photo_status = 0;
            $user->save();
            $feature = 'sub_user_image_change_';
            if ($isfeature == 2) {
                $feature = 'token_user_image_change_value_';
            }
            FeatureUses::storeFeatureUsase($feature, $isfeature);
            return redirect('profile/edit')->with('success', 'Profile changed successfully');
        } else {
            return redirect('profile/edit')->with('warning', 'Please select a profile picture.');
        }
    }

    public function UploadProfilePicture(Request $request)
    {
        $isfeature = Features::isHeCanChangeProfilePic();
        if (!$isfeature) {
            return Response::json(array(
                'success' => true,
                'isfeatured' => 0,
                'message' => "You've reached your profile picture limit. Please upgrade your premium plan to change your profile picture"
            ), 200);
        }

        if(!Auth::check()) {
            return Response::json(array(
                'success' => false,
                'error' =>'Please refresh page and login.'
            ), 200); //422
        }
        $user=Auth::user();
        $upload_path = public_path('/uploads');
        $user_profile_images_folder_path=$upload_path;

        if (!File::exists($user_profile_images_folder_path))
        {
            $result = File::makeDirectory($user_profile_images_folder_path, 0775, true, true);
        }

        //get all posted values
        $input_all = $request->all();

        $user = Auth::user();
        if($request->has('image') && $request->get('image')!="") {

            $image_helper = new ImageHelper();

            $original_name=$request->get('selected_file_name');

            $data =$request->get('image');

            $original_name_without_ext=$image_helper->getImageNameWithoutExtension($original_name);

            $original_ext = $image_helper->getImageExtension($original_name);

            $filename = $image_helper->sanitize($original_name_without_ext);

            $allowed_filename = $image_helper->createUniqueFilename($filename, 'png', $user_profile_images_folder_path);

            $filename_ext = $allowed_filename . '.png' ;

            $img = Image::make($data)->save($user_profile_images_folder_path . '/' . $filename_ext);

            $original_name=$original_name_without_ext.'.png';

            //$input['original_image'] = $original_name;
            $input['profile_pic'] = $filename_ext;

            // update into db
            $user->fill($input)->save();


            $feature = 'sub_user_image_change_';
            if ($isfeature == 2) {
                $feature = 'token_user_image_change_value_';
            }
            FeatureUses::storeFeatureUsase($feature, $isfeature);

            return Response::json(array(
                'success' => true,
                'message' => "Profile image updated successfully.",
                'image_path'=>$user->profile_pic
            ), 200);
        }
    }

    public function UploadSelectedProfilePicture(Request $request)
    {
        $profileimage = $request->input('profileimage');

        $isfeature = Features::isHeCanChangeProfilePic();
        if (!$isfeature) {
            return Response::json(array(
                'success' => true,
                'isfeatured' => 0,
                'message' => "You've reached your profile picture limit. Please upgrade your premium plan to change your profile picture"
            ), 200);
        }

        $user = Auth::user();
        if($profileimage && $profileimage!="") {
            $input['profile_pic'] = $profileimage;
            $user->fill($input)->save();

            $feature = 'sub_user_image_change_';
            if ($isfeature == 2) {
                $feature = 'token_user_image_change_value_';
            }
            FeatureUses::storeFeatureUsase($feature, $isfeature);

            return Response::json(array(
                'success' => true,
                'message' => "Profile image updated successfully.",
                'image_path'=>$user->profile_pic
            ), 200);
        }
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

    public function frontuserprofile($id)
    {

        $encrypted_other_user_id = $id;
        $plans = self::getPlanlist();
        $id = base64_decode($id);
        $other_userdata =  User::where('id', $id)->first();
        $dispname = $other_userdata->displayname;
        if (Auth::check()) {
            $blocked = Reportblock::where('user_id',$id)->where('block_id',Auth::user()->id)->orWhere(function ($query) use($id)  {
                        $query->where('user_id',Auth::user()->id)
                      ->Where('block_id',$id);

            })->first();

            if($blocked){
                if($blocked->user_id == Auth::user()->id){
                    $message = 'Unfortunately, you have blocked this user, Please unblock this user to see profile.';
                    // Alert::warning('Unfortunately, you have blocked this user, Please unblock this user to see profile.')->autoclose(4000);
                }else{
                    $message = 'Unfortunately, you are blocked from accessing '.$dispname.'\'s profile.';
                    // Alert::warning('Unfortunately, you are blocked from accessing '.$dispname.'\'s profile.')->autoclose(4000);
                }

                return redirect()->back()->with('error',$message);
            }
        }    

        

        $getFamilyRoleInfo = UsersFamilyRole::where('user_id', $id)->pluck('family_role_id')->toArray();
        if (count($getFamilyRoleInfo) > 0) {
           $familyroles = FamilyRole::whereIn('id', $getFamilyRoleInfo)->get();
        } else {
           $familyroles = FamilyRole::all();
        }
        $liked_the_user = 0;
        $own_profile = 0;
        if (Auth::check()) {
            $auth_user = Auth::user();
            $liked_the_user = $auth_user->likedUser($id);
            if ($id == $auth_user->id) {
                $own_profile = 1;
            }
        }
        $user = User::with('species')->find($id);

        //get family roles
        $familyRoles = UsersFamilyRole::where('user_id', $id)->pluck('family_role_id')->toArray();
        if (count($familyRoles) > 0) {
            $getFamilyRoleInfo = FamilyRole::whereIn('id', $familyRoles)->get();
        } else {
            $getFamilyRoleInfo = null;
        }

        //get ethnicity group
        if ($user->ethnicity_group_id != 0) {
            $getEthnicityGroupInfo = EthnicityGroup::where('id', $user->ethnicity_group_id)->pluck('title');
        }else{
          $getEthnicityGroupTitle = null;
          $getEthnicityGroupInfo = null;
        }

        // get current user's current  Trials
        $myCurrentTrials = Trials::WhereRaw('((user_id = ' . $user->id.') OR (matcher_id = ' . $user->id .' ))')
                      ->where('is_accepted',1)
                      // ->where('is_decline',0)
                      // ->where('is_success',0)
                      // ->where('is_ended',0)
                      // ->where('auto_ended',0)
                      ->get();


        $myEndTrials = Trials::WhereRaw('((user_id = ' . $user->id.') OR (matcher_id = ' . $user->id .' ))')
                             ->WhereRaw('((is_ended =1) OR (auto_ended =1 ))')
                              ->get();





        $myadoptions = Trials::WhereRaw('((user_id = ' . $user->id.') OR (matcher_id = ' . $user->id .' ))')
                ->Where('adopt_is_accepted', 1)
                ->get();
        //get all dissolved adoptions data
        $mydissolvedadoptions = Trials::WhereRaw('((user_id = ' . $user->id.') OR (matcher_id = ' . $user->id .' ))')
                        ->Where('adopt_is_dissolve', 1)
                        ->get();




        //users data start
        $blockIds = getblockeduser();
        $auth_user = 0;
        if (Auth::user()) {
            $users = User::orderBy('last_activity', 'DESC')->where('role_id', '!=', 1)->where('id', '!=', Auth::user()->id)->whereNotIn('id', $blockIds)->paginate(12);
        } else {
            $users = User::orderBy('last_activity', 'DESC')->where('role_id', '!=', 1)->paginate(12);
        }
        //users data end
        $auth_user = Auth::user();
        $auth_user_profile = '';
        if($auth_user){
            if($id == $auth_user->id){
                $auth_user_profile = $auth_user;
            }else{
                $auth_user_profile = '';
            }
        }

        // check if this user have some matches
        $trialInfo = array();
        $sentReqTrial = 0;
        $sentReqAdopt = 0;
        $trial_id = 0;
        $adoption_success = 0;
        $adopt_message = '';
        $logged_user_subscription = 1;
        if (Auth::check()) {

            $matches = Match::WhereRaw(' is_match = 1 AND ( (user_id = ' . Auth::user()->id . '   && matcher_id = ' . $id . ' ) OR (user_id = ' . $id . '   && matcher_id = ' . Auth::user()->id . ' ))')->get()->count();

            if ($matches > 0) {
                $sentReqTrial = 1;
                // check Request sent or not
                $checkReq = Trials::WhereRaw('( (user_id = ' . Auth::user()->id . ' && matcher_id = ' . $id . ' ) OR (user_id = ' . $id . ' && matcher_id = ' . Auth::user()->id . ' ))')->get()->last();

                if($checkReq){

                $adopter_family_role = FamilyRole::find($checkReq->trial_family_role)->title;
                $adopter_family_gender = (FamilyRole::find($checkReq->trial_family_role)->gender == 'female')  ? "she" : "he" ;
                $adoptee_family_role = FamilyRole::find($checkReq->adoptee_family_role)->title;
                $adoptee_family_gender = (FamilyRole::find($checkReq->adoptee_family_role)->gender == 'female')? "she" : "he";

                if(Auth::user()->id == $checkReq->user_id){


                    $reciverUrl = url("userprofile").'/'.base64_encode($checkReq->matcher_id);
                    $reciverName = $checkReq->matcherid->display_name_on_pages;
                    $reciverLink = '<a href="'.$reciverUrl.'">'.$reciverName.'</a>';

                    $adopt_message = "By signing this certificate, you promise to give ".$reciverLink." all of the love and care that ".$adoptee_family_gender."  require in return, your ".$adoptee_family_role." will give you all the love, comfort and attention you need. Failure to meet adoption requirements herein, may result in dissolution. ";
                }else{
                    $reciverUrl = url("userprofile").'/'.base64_encode($checkReq->user_id);
                    $reciverName = $checkReq->userid->display_name_on_pages;
                    $reciverLink = '<a href="'.$reciverUrl.'">'.$reciverName.'</a>';

                    $adopt_message = "By signing this certificate, you promise to give ".$reciverLink." all of the love and care that ".$adopter_family_gender." require in return, your ".$adopter_family_role." will give you all the love, comfort and attention you need. Failure to meet adoption requirements herein, may result in dissolution. ";
                }
            }




                // print_r($adoptee_family_role);exit;


                if ($checkReq) {


                  $trial_id = $checkReq->id;
                    if ($checkReq->is_decline == 1 && $checkReq->is_ended == 0) {
                        $sentReqTrial = 1;
                    }else if($checkReq->adopt_is_dissolve == 1) {
                        $sentReqTrial = 1;
                    }else{
                        $sentReqTrial = 0;

                    }

                    if ($checkReq->adoption_success == 1 ) {
                        $adoption_success = 1;
                    }else{
                         $adoption_success = 0;
                    }

                   if ($checkReq->is_success == 1 ) {
                        $sentReqAdopt = 1;
                    }else{
                         $sentReqAdopt = 0;
                    }
                }else{
                        $sentReqTrial = 1;
                        $sentReqAdopt = 0;
                }

            } else {
                $sentReqTrial = 0;
                $sentReqAdopt = 0;
            }

        } else {
            $sentReqTrial = 0;
            $sentReqAdopt = 0;
        }

        // print_r($sentReqTrial);exit;

        //photo_ulbum
        $photo_ulbum = Photoalbum::where('user_id', '=', $id)->get();

        $visited_user_group_id = $user->group;
        VisitorController::store($id);
        $useranswer = DB::table('answers')->where('user_id', $user->id)->where('group_id', $visited_user_group_id)->first();
        $answerarray = array();
        if($useranswer){
            $answerarray = json_decode($useranswer->answer_data,true);
        }
        $math_quest_categories = MatchQuestCategory::with(['visitedUserQuestions' => function ($query) use ($visited_user_group_id){
                $query->where('group_id', $visited_user_group_id);
            }])->get();
        $final_free_questions_array = array();
        foreach ($math_quest_categories as $math_quest_category) {
            $max_per_cat = 1;
            foreach ($math_quest_category->visitedUserQuestions as $question) {
                if(array_key_exists($question->id, $answerarray) && $max_per_cat <= 2){
                    $final_free_questions_array[$question->id] = $answerarray[$question->id];
                    $max_per_cat++;
                }
            }
        }

        $likecount = Like::where('isliked', 1)->where('user_id', $user->id)->count();
        $myfuns = MyFun::find($id);

        $termContent = Page::findOrFail(config('params.pages.terms'));
        $policyContent = Page::findOrFail(config('params.pages.policy'));
        $has_unlock_match_quest_feature = '';
        $reviews = '';
        $blocked = 0;
        if (Auth::check()) {
        
            $has_unlock_match_quest_feature = $this->checkMoreMacthQuestAccess();
            $userDataToSub = User::find(Auth::user()->id);
            $logged_user_subscription = $userDataToSub->subscribedPlan;
            if($logged_user_subscription && $logged_user_subscription->name != 'main'){
                $logged_user_subscription = 0;
            }
        }

        $reviews  = Review::with('reviewComment')->with('ReviewAbuse')->where('other_user_id',$id)->latest()->take(6)->get();


        $reviewsQuery = Review::where('other_user_id',$id);
        $reviewsSum = $reviewsQuery->sum('stars');
        $reviewsCount = $reviewsQuery->count('id');



        $canSendAdoptionReview = 0;
        $canSendDissolveReview = 0;
        $canSendTrialReview = 0;

        if (Auth::check()) {
            // check Request sent or not
            $trailReq = Trials::with(['getReviewsTrail','getReviewsAdoption','getDissolveAdoption'])->WhereRaw('( (user_id = ' . Auth::user()->id . ' && matcher_id = ' . $id . ' ) OR (user_id = ' . $id . ' && matcher_id = ' . Auth::user()->id . ' ))')->get()->last();
            
            if($trailReq){

                if($trailReq->adoption_success && !$trailReq->getReviewsAdoption){
                    $canSendAdoptionReview = 1;
                }
               
                if(!$trailReq->getReviewsTrail && $trailReq->is_ended){
                    $canSendTrialReview = 1;
                }
                if($trailReq->adopt_is_dissolve && !$trailReq->getDissolveAdoption ){
                    $canSendDissolveReview = 1;
                }

                if($trailReq->getReviewsTrail && $trailReq->getReviewsAdoption && $trailReq->getDissolveAdoption){
                    $trailReq = '';
                    $canSendAdoptionReview = 0;
                    $canSendDissolveReview = 0;
                    $canSendTrialReview = 0;
                }
            
            }    
        }    






        $reviewsAvg = 0;
        if($reviewsSum && $reviewsCount )
            $reviewsAvg = round($reviewsSum/$reviewsCount);
        
        return view('profile.front', compact('user','blocked', 'reviews','reviewsAvg', 'canSendTrialReview','canSendAdoptionReview','canSendDissolveReview','reviewsCount', 'trailReq' , 'users', 'likecount', 'useranswer', 'myfuns', 'liked_the_user', 'own_profile', 'getFamilyRoleInfo', 'getEthnicityGroupInfo', 'sentReqTrial','adopt_message','adoption_success' ,'sentReqAdopt', 'familyroles', 'trial_id', 'plans', 'termContent', 'policyContent', 'auth_user_profile', 'myadoptions' , 'mydissolvedadoptions' , 'photo_ulbum', 'dispname', 'has_unlock_match_quest_feature', 'encrypted_other_user_id', 'logged_user_subscription', 'myCurrentTrials','myEndTrials'));
    }

    public function checkMoreMacthQuestAccess(){
        $has_unlock_match_quest_feature = 0;
        $user_subscription = Auth::user()->subscribedPlan;
        if($user_subscription){
            $subscribed_plan = Plan::where('plan_id', $user_subscription->stripe_plan)->first();
            if($subscribed_plan){
                $feature_value = getWebsiteSettingsByKey( 'sub_match_quest_count_'.$subscribed_plan->id );
                if($feature_value != ''){
                    $has_unlock_match_quest_feature = 1;
                }
            }
        }
        return $has_unlock_match_quest_feature;
    }

    public function matchWithMe(Request $request, $profile_id)
    {        
        $auth_user = Auth::user();
        $profile_id = base64_decode($profile_id);       
        $msg = "";
        $profile_user_arr = array();
        $auth_user_arr = array();
        if($profile_id == Auth::user()->id)
        {
            $matches = Match::WhereRaw( ' is_match = 1 AND ( user_id = ' . $profile_id .' OR  matcher_id = ' . $profile_id .' )' )->get();
            foreach ($matches as $key => $value) {
                $userid = $value->user_id;
                                    if( $value->user_id == Auth::user()->id ){
                                        $userid = $value->matcher_id;
                                    }
                $finduser = User::find($userid);
                if($finduser)
                {
                    $profiles[] = $finduser;
                }
            }
            foreach ($profiles as $key1 => $profiles) { 

                    $profile_user_arr[] = array(
                    'id' => $profiles['id'],
                    'name' => $profiles['name'],
                    'displayname' => $profiles['displayname'],
                    'profile_url' => $profiles['profile_url'],
                    'profile_pic_url' => $profiles['profile_pic_url']
                    );

                    $auth_user_arr[] = array(
                        'name' => $auth_user->name,
                        'displayname' => $auth_user->displayname,
                        'profile_url' => $auth_user->profile_url,
                        'profile_pic_url' => $auth_user->profile_pic_url,
                    );                    
            }
            return Response::json(array(
                        'success' => true,
                        'message' => $msg,                     
                        'flag'=>1,
                        'info' => array(
                        'profile_user' => $profile_user_arr,
                        'match_user' => $auth_user_arr,
            )), 200);
        }else
        {
            $profile_user = User::find($profile_id);
        
        if (!$profile_user) {
            return Response::json(array(
                'success' => false,
                'error' => 'User not found.'
            ), 200); //422
        }
        if (!Auth::check()) {
            return Response::json(array(
                'success' => false,
                'error' => 'Please refresh page and login again.'
            ), 200); //422
        }
        $is_matched_with_user = 0;
        $is_matched_with_user1 = 1;
        $total_match = $profile_user->totalMatch();
        if ($total_match) {
            $is_matched_with_user = $auth_user->isMatchedWithUser($profile_id);
            //$is_matched_with_user1 = $profile_user->isMatchedWithUser($auth_user->id);
        }
        if ($total_match == 0) {
            $msg = "User has 0 matches. Be the first to match with user by liking " . $profile_user->his_her . " profile. If " . $profile_user->he_she . " likes you back, you'll be their first match.";
        } else if (!$is_matched_with_user) {
            $msg = "Match with user by liking " . $profile_user->his_her . " profile. If " . $profile_user->he_she . " likes you back, you'll be their match.";
        } else {
            // get auth him/her attributes
            if($profile_user->gender)
            {
                if($profile_user->usergender)
                {
                    if($profile_user->usergender->gender=='female')
                        $himher =  'her';
                    else {
                      $himher = 'him';
                    }
                }
            }else{
                $himher = 'him/her';
            }
            //  check Trial request
            // check Request sent or not
            $trialInfo = array();
            $checkReq = Trials::WhereRaw('( (user_id = ' . Auth::user()->id . ' && matcher_id = ' . $profile_id . ' ) OR (user_id = ' . $profile_id . ' && matcher_id = ' . Auth::user()->id . ' ))')->get()->last();

            $sentReqTrial = 1;
            $message = '';
            if ($checkReq) {
                if ($checkReq->is_decline == 1 && ($checkReq->is_ended == 0 || $checkReq->auto_ended == 0 )) {
                    $sentReqTrial = 1;

                }else if($checkReq->adopt_is_dissolve == 1){
                    $sentReqTrial = 1;

                }else {
                    $sentReqTrial = 0;


                    if($checkReq->is_success == 1 && ($checkReq->is_ended == 1 || $checkReq->auto_ended == 1 )){
                        if($checkReq->adoption_success == 1 && $checkReq->adopt_is_accepted == 1){
                              // see certificate message
                              $message = '<p><a href="'.url("adoptions").'">Click here</a> to see your Adoption Certificate.</p>';

                        }elseif($checkReq->adoption_success == 0 && $checkReq->adopt_is_accepted == 0){
                              // Going for Adoption Request (user Profile)
                              $message = '<a class="btn btn-success btn-lg matchesAdoption-btn" style="display:table;margin:0 auto" data-toggle="modal" data-target="#sendRequestBtn">Adopt Now</a>';


                        }else{
                          if($checkReq->adoption_success == 0){
                              // Going for Adoption Request
                                  $message = '<a class="btn btn-success btn-lg matchesAdoption-btn" style="display:table;margin:0 auto" data-toggle="modal" data-target="#sendRequestBtn">Adopt Now</a>';
                          }
                          if($checkReq->adopt_is_accepted == 0){

                              // check dissolved Adoption
                              if($checkReq->adopt_is_dissolve == 0){
                                // Going for Adoption Accepted (Adoption Requests page)
                                $message = '<p>Please accept the Adoption request. <a href="'.url("adoptions").'">Click here</a> to see your Adoption Requests Status.</p>';

                              }else{
                                $message = '';
                              }

                          }
                        }
                    }else{


                        if($checkReq->is_ended == 1 || $checkReq->auto_ended == 1 ){
                            $message = '<p>'.$checkReq->userid->display_name_on_pages. ' & '.$checkReq->matcherid->display_name_on_pages. ' your trial has ended. <a href="'.url("trials").'">Click here</a> to check your Trial status.</p>';
                        }else{
                            // Trial going on
                            $getLocation = TrialLocation::find($checkReq->trial_location_id);

                            $trialInfo = array(
                              'location' => $getLocation->address,
                              'date' => date("d F Y",strtotime($checkReq->trial_date)),
                              'time' => date("h:iA", strtotime($checkReq->trial_time))
                            );


                            $message .= '<p>Please remember your Trial Date is: '.date("d F Y",strtotime($checkReq->trial_date)).', '.date("h:iA", strtotime($checkReq->trial_time)).' (SLT) at  <span><a href="'.$getLocation->address.'" target="_blank"> '.$getLocation->name.'</a>.</span></p><p><a href="'.url("trials").'"> Click here</a> to check your Trial status.</p>';
                          }

                    }
                }
            }



            $profile_user_arr = array(
                'id' => base64_encode($profile_id),
                'name' => $profile_user->name,
                'displayname' => $profile_user->displayname,
                'profile_url' => $profile_user->profile_url,
                'profile_pic_url' => $profile_user->profile_pic_url,
                'himHerStatus'   => $himher,
                'sentReqTrial' => $sentReqTrial,
                'message'  => $message

            );

            $auth_user_arr = array(
                'name' => $auth_user->name,
                'displayname' => $auth_user->displayname,
                'profile_url' => $auth_user->profile_url,
                'profile_pic_url' => $auth_user->profile_pic_url,

            );

        }
        return Response::json(array(
            'success' => true,
            'message' => $msg,
            'flag' => 0,
            'info' => array(
                'profile_user' => $profile_user_arr,
                'match_user' => $auth_user_arr,
            )
        ), 200);
        }
    }

    public function usersWhoLiked($id)
    {
        $id = base64_decode($id);
        $user = User::find($id);
        if (!$user) {
            return Response::json(array(
                'success' => false,
                'error' => 'User not found.'
            ), 200); //422
        }
        $users_who_liked = $user->usersWhoLiked();
        $user_arr = array();
        foreach ($users_who_liked as $user_who_liked) {
            $user_arr[] = array(
                'displayname' => $user_who_liked->displayname,
                'profile_url' => $user_who_liked->profile_url,
                'profile_pic_url' => $user_who_liked->profile_pic_url,
            );
        }
        return Response::json(array(
            'success' => true,
            'message' => "",
            'info' => array(
                'users' => $user_arr
            )
        ), 200);
    }

    public function albumdestroy($id)
    {
        if (isthisSubscribed()) {
            $user_max_images_upload = FeatureUses::orderBy('id', 'DESC')->where('userid', Auth::user()->id)->where('featurid', 'sub_max_images_upload_' . getCurrentUserPlan()->id)->first();
            $user_max_images_upload_id = $user_max_images_upload->id;
            FeatureUses::destroy($user_max_images_upload_id);
        }
        
        Photoalbum::destroy($id);
        return response()->json(['status' => true, 'success' => 'Deleted']);
    }

    // Update
    public function updateAnswer(Request $request, $id)
    {
        return back()->withInput()->with('error', 'Already integrated in route: matchquests_submit');
        $user = Auth::user();
        $getWords = WordsSecurity::pluck('title')->toArray();
        $group_id = $request->get('group_id');
        $isexist = DB::table('answers')->where('user_id', $id)->where('group_id', $group_id)->first();
        $insertorupdate = $request->insertorupdate;
        $answers = json_encode($request->answers);
        $compareAnswersWords = preg_replace('/\b(' . strtolower(implode('|', $getWords)) . ')\b/', '', strtolower($answers));
        $compareAnswersWords = preg_replace('/\b('.strtolower($user->second_life_full_name).')\b/','******',strtolower($compareAnswersWords));

        $user_second_life_full_name = explode(',',preg_replace('/\s+/',' ',$user->second_life_full_name));
        $getWords = array_merge($getWords,$user_second_life_full_name);

        $compareAnswersWords = preg_replace('/\b(\w*' . strtolower(implode('|', $getWords)) . '\w*)\b/', '******', strtolower($answers));


        if ($isexist) {
            DB::table('answers')->where('user_id', $request->user_id)->where('group_id', $group_id)->update(['answer_data' => $answers, 'group_id' => $group_id]);
        } else {
            $arrData = array(
                'user_id' => $request->user_id,
                'answer_data' => $compareAnswersWords,
                'group_id' => $group_id
            );
            DB::table('answers')->insert($arrData);
        }
        return back()->withInput()->with('display_questionnaire_section', 1)->with('success', 'Questionnaire updated successfully.');
    }

    //Assign Warning to User
    public function warning(Request $request, $id)
    {
        $notification['user_id'] = $id;
        $notification['message'] = $request->warningmessage;
        $notification['type'] = 'warning';
        NotificationController::create($notification);
        return redirect()->back()->with('message', 'Notification Warning Added!');
    }

    //Suspend User
     public function suspend(Request $request)
     {
         $id= $request->userid;
         $days=  $request->select_days;
         $reason=  $request->reason;
         if($days == 'permanent')
         {
             $suspend = User::findorfail($id);
             $suspend->suspend = '2';
             $suspend->reason =$reason;
             $suspend->save();
             $bannedips = new Bannedips();
             $bannedips->ip_address = $suspend->ip_address;
             $bannedips->save();
             $table_name=['trials','matches','answers','event_buy','hearts','likes','messages','notifications','photo_album','rating','report_block','search_results','subscriptions','ticketit','ticketit_audits','ticketit_categories_users','ticketit_comments','token_debit','wallets','visitors','verify_users','users_family_roles','users_events_saved','user_messages'];

             foreach ($table_name as $t_name )
             {
                 DB::table($t_name)->where('user_id', $id)->delete();
             }
             DB::table('users')->where('id', $id)->delete();
         }else
         {
             $current = Carbon::now();
             $suspendExpires = $current->addDays($days);
             $suspend = User::findorfail($id);
             $suspend->suspend = '1';
             $suspend->suspend_exp_time =$suspendExpires;
             $suspend->reason =$reason;
             $suspend->save();
         }
         return redirect('admin/users')->with('message', $suspend->name.' is Suspended');
     }

    //actuve suspended User
    public function active(Request $request, $id)
    {
        $active = User::findorfail($id);
        $active->suspend = '0';
        $active->suspend_exp_time = Carbon::now();
        $active->reason ="";
        $active->save();
        return redirect()->back()->with('message', 'User is Active Now');
    }

    // Display user account info
    public function accountSetting()
    {
        $userid = Auth::user()->id;
        $user = User::find($userid);
        if (Auth::user()->role_id == 1 || Auth::user()->role_id == 3) {
            return view('profile.accountsetting', compact('user'));
        } else {
            return view('profile.accountsettinguser', compact('user'));
        }
    }


     function clean($string) {
       // $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

       $replced =  preg_replace('/[^A-Za-z0-9\-]/', ' ', $string); // Removes special chars.
        // $final = explode(' ',$replced);
        return $replced;
    }

    //Update user account
    public function updateaccount(Request $request)
    {




        $userid = Auth::user()->id;
        $user = Auth::user();
        $updateaccount = User::findorfail($userid);

        // save username change
        if ($request->get("action_submit") == 'action_submitUsername') {
            $this->validate($request, [
                'name' => 'required',
                 'user_email' => 'email|unique:users,email,'.$userid,
            ]);

            $metaData = self::setMetas();
            $text = '';
            if (isthisSubscribed()) {
                $usedname = FeatureUses::where('userid', Auth::user()->id)->where('featurid', $metaData['sub_username_change_' . getCurrentUserPlan()->id])->get();
                if ($metaData['sub_username_change_' . getCurrentUserPlan()->id] == '-1') {
                    $text = 'unlimited';
                } else {
                    $text = $metaData['sub_username_change_' . getCurrentUserPlan()->id];
                }
            }

            $successText = '';
            $getWords = WordsSecurity::pluck('title')->toArray();
            $isfeature = Features::isUserCanChangeUserName();

            if ($isfeature) {
              $min = ($metaData['screen_name_minimum']) ? $metaData['screen_name_minimum'] : '4';
              $max = ($metaData['screen_name_maximum']) ? $metaData['screen_name_maximum'] : '6';
              $user_enter_name = strlen(trim(request('name')));
              if ($user_enter_name < $min ) {
                  return redirect()->back()->with('warning', 'Username required minimum '. $min .' characters');
              } elseif ($user_enter_name > $max ) {
                  return redirect()->back()->with('warning', 'In Username maximum '. $max .' characters are allowed');
              }


            // STARTS HERE

            $compareUsernameDisplaynameStrcmp = '';

            # $user_second_life_full_name = explode(',',preg_replace('/\s+/',' ',$user->second_life_full_name));
            $user_explode  =  $this->clean($updateaccount->second_life_full_name);
            $user_explode = explode(' ', $user_explode);

            // print_r($user_explode);exit;

                $getWords = array_merge($getWords,$user_explode);
                $getWords = strtolower(implode('|', $getWords));


                if(preg_match('('.$getWords.')', strtolower(request('name'))) === 1) {

                    $display_name_arr=array(
                        'displayname'=>request('name')
                      );

                      $display_messages = [
                          'displayname'  => 'Sorry, that username is not allowed; please choose another username.'
                  ];

                  $validator = Validator::make($display_name_arr, [
                      'displayname' => 'bail|required|min:'.$min.'|max:'.$max.'|unique:users,displayname,'.$userid,
                  ],$display_messages);


                        return redirect()->back()->with('danger', 'Sorry, that username is not allowed; please choose another username.');

                      return redirect()->back()->withErrors($validator)->withInput();
                }else{
                    $updateaccount->displayname = request('name');
                    $updateaccount->save();
                    $successText = 'Username Changed successfully! ';
                }



              // ENDS HERE





              // $compareUsernameWordsStrcmp = '';

              // $user_second_life_full_name = explode(',',preg_replace('/\s+/',' ',$user->second_life_full_name));
              // $getWords = array_merge($getWords,$user_second_life_full_name);

              //   $compareUsernameWords = preg_replace('/\b(\w*' . strtolower(implode('|', $getWords)) . '\w*)\b/', '', strtolower(request('name')));
              //   if(!empty($compareUsernameWords)){

              //     $compareUsernameWordsStrcmp = strcmp(strtolower(preg_replace('/\s+/','',request('name'))), strtolower(preg_replace('/\s+/','',$compareUsernameWords)));
              //   }else{
              //     $compareUsernameWordsStrcmp = '-1';
              //   }

              //   if ($compareUsernameWordsStrcmp !=0 ) {
              //       return redirect()->back()->with('danger', 'Sorry, that username is not allowed; please choose another username.');
              //   } else {

              //       $updateaccount->displayname = request('name');
              //       $updateaccount->save();
              //       $successText = 'Username Changed successfully! ';
              //   }
            } else {
                $successText = "Sorry! Please subscribe to update your name";
            }



            if (!isthisSubscribed()) {
                return redirect()->back()->with('warning', 'Please subscribe to update your name.');
            }
            $feature = 'sub_username_change_';
            if ($isfeature == 2) {
                $feature = 'token_username_change_value_';
            }
            FeatureUses::storeFeatureUsase($feature, $isfeature);
            $mess = '.';

            if ((null != usedUserChangeName()) && count(usedUserChangeName()) > 0) {

                $mess = ' and you tried to changed it in ' . count(usedUserChangeName()) . ' times.';

                //get remaining change count

                if (is_numeric($text)) {
                    $remainingCount = $text - count(usedUserChangeName());
                    if ($remainingCount > 0) {
                        $usernameChangeCount = $remainingCount;
                        $successText = "Username changed successfully!. You can change your name remaining " . $usernameChangeCount . " times more.";
                    } elseif ($remainingCount < 0) {
                        $usernameChangeCount = 0;
                        $successText = "Sorry! Please subscribe to update your name";
                    } else {
                        $successText = "Username changed successfully!. Please subscribe to update your name in future";
                        $usernameChangeCount = 0;
                    }
                    Session::flash('usernameChangeCount', $usernameChangeCount);
                }
            }

            return redirect()->back()->with('success', $successText);
        }else{

            $updateaccount->user_email = $request->user_email;
            $updateaccount->is_notifications_enable = isset($request->notification) ? $request->notification : 0;
            $updateaccount->is_private = isset($request->privacy) ? $request->privacy : 0;
            // if( $request->password ){
            $updateaccount->password = bcrypt($request->password);
            // }
            $updateaccount->save();
            return redirect()->back()->with('message', 'Account Settings Updated');
          }
    }

    // block or report request from user
    public function blockreport(Request $request)
    {
        
        // print_r($request->all());exit;
        $user = new Reportblock;
        $user->user_id = Auth::user()->id;
        $user->block_id = $request->block_id;
        $user->type = $request->type;
        $user->reason = $request->reason;
        $user->description = $request->description;
        $user->save();


        // remove like
        Like::where('user_id' ,Auth::user()->id)->where('liked_by', $request->block_id)->delete();
        Like::where('liked_by' ,$request->block_id)->where('user_id', Auth::user()->id)->delete();
        // remove the match
        Match::whereRaw('( user_id = '. Auth::user()->id .' AND matcher_id = ' . $request->block_id . ' ) OR ( user_id = '. $request->block_id .' AND matcher_id = '. Auth::user()->id .')')->delete();

        $message = 'User ' . $request->type . 'ed Successfully';
        // Alert::success($message)->autoclose(4000);
       
        if (isAdmin()) {
            return redirect()->to('admin/dashboard')->with('message', $message);
        }
        return redirect()->to('home')->with('message', $message);
    }

    // Remove warning on user profile
    public function removewarning(Request $request, $id)
    {
        $notification = Notification::find($id);
        $notification->is_seen = 0;
        $notification->save();
    }

    //Permanently delete user account

    public function permanentDeleteAccount(Request $request)
    {
        $flag=0;
        $user_id=Auth::user()->id;
        $userdata = User::where('id',$user_id)->first();
        if($request->id){
            $user_id=$request->id;
            $flag=1;
        }
        $table_name=['trials','matches','answers','event_buy','hearts','likes','messages','notifications','photo_album','rating','report_block','search_results','subscriptions','ticketit','ticketit_audits','ticketit_categories_users','ticketit_comments','token_debit','wallets','visitors','verify_users','users_family_roles','users_events_saved','user_messages'];

        $bannedips = new Bannedips();
        $bannedips->ip_address = $userdata->ip_address;
        $bannedips->save();
        foreach ($table_name as $t_name )
        {
            DB::table($t_name)->where('user_id', $user_id)->delete();
        }
            DB::table('users')->where('id', $user_id)->delete();
            if($flag==0){
                Auth::logout();
                $message = 'Your account deleted sucessfully. You may register again in the future, if you so choose.';
                return redirect()->route('welcome')->with('warning', $message);
            }
            else
            {
                return redirect('admin/users')->with('message', 'User account Permantly deleted sucessfully');
            }
    }

    public function deleteaccount(Request $request)
    {
        $flag=0;
        $user_id=Auth::user()->id;
        if($request->id){
            $user_id=$request->id;
            $flag=1;
        }

        $table_name=['answers','event_buy','hearts','likes','messages','notifications','photo_album','rating','report_block','search_results','subscriptions','ticketit','ticketit_audits','ticketit_categories_users','ticketit_comments','token_debit','wallets','visitors','verify_users','users_family_roles','users_events_saved','user_messages'];//trials,matches are excluded from list

        foreach ($table_name as $t_name )
        {
            DB::table($t_name)->where('user_id', $user_id)->delete();
        }
            $user = User::find($user_id);
            $user->is_deleted = 'true';
            $user->save();
            if($flag==0){
                Auth::logout();
                $message = 'Your account deactivated sucessfully. Please login to Reactivate it in the future. All your data will remain on our servers.';
                return redirect()->route('welcome')->with('warning', $message);
            }else
            {
               return redirect('admin/users')->with('message', $user->name.' account deactivated sucessfully');
            }

    }

    //profile Notes display
    public function profileNotes()
    {
        $groupid = Auth::user()->group;
        $notes = Note::where('user_group', 'like', '%"' . $groupid . '"%')->paginate(6);
        return view('note.index', compact('notes'));
    }

    public function removeAnnouncement(Request $request)
    {
        $response = array('status' => 0);
        $id = $request->input('id');
        $data = Announcements::find($id);
        if ($data) {
            $users = json_decode($data->user_ids);
            $users[] = (string)Auth::user()->id;
            $data->user_ids = json_encode(array_unique($users));
            $data->save();
            $response['status'] = 1;
        }
        return json_encode($response);
    }

    public function notificationSeen(Request $request)
    {
        $response = array('status' => 0);
        $id = $request->input('id');
        $data = Notification::find($id);
        if ($data) {
            $data->is_seen = 0;
            $data->save();
            $response['status'] = 1;
        }
        return json_encode($response);
    }

    public function envelopeMessageSeen(Request $request)
    {
        $response = array('status' => 0);
        $id = $request->input('id');
        if ($id) {
            DB::table('messages')
                ->where('id', $id)
                ->update(['is_seen' => "0"]);
            $response['status'] = 1;
        }
        return json_encode($response);
    }

    public function heartSeen(Request $request)
    {
        $response = array('status' => 0);
        $id = $request->input('id');
        $data = Heart::find($id);
        if ($data) {
            $data->is_seen = 0;
            $data->save();
            $response['status'] = 1;
        }
        return json_encode($response);
    }

    public function matchSeen(Request $request)
    {
        $response = array('status' => 0);
        $id = $request->input('id');
        $data = Match::find($id);
        if ($data) {
            $data->is_seen = 0;
            $data->save();
            $response['status'] = 1;
        }
        return json_encode($response);
    }

    public function allNotifications()
    {

        Notification::where('user_id', Auth::user()->id)->update(['is_seen' => 0]);
        if (Auth::user()->role_id == 1 || Auth::user()->role_id == 3) {
            return view('admin.allnotifications');
        } else {
            return view('user.allnotifications');
        }
    }

    public function displayBlocksToUser()
    {
        return view('user.blocks');
    }

    public function unblockUser($id)
    {
        $unblock = Reportblock::find($id);
        $unblock->delete();
        return redirect()->back()->with('message', 'User Unblocked successfully.');
    }

    public function removeAllNotification(Request $request)
    {
        if (Notification::where('user_id', Auth::user()->id)->delete())
            return redirect()->back()->with('message', 'Notifications has been deleted.');
        else
            return redirect()->back()->with('errors', 'Notifications not found');
    }

    public function deleteNotification(Request $request, $id = null)
    {
        if ($notification = (Notification::find($id))) {
            $notification->delete();
            return redirect()->back()->with('message', 'Notification has been deleted.');
        } else {
            return redirect()->back()->with('errors', 'Notification not found');
        }

    }

    public function suspendUsers()
    {
        $userid = Auth::user()->id;
        $suspendusers = User::where('suspend',1)->get();
        return view('user.suspendusers', compact('suspendusers'));
    }

    public function matchquests(){
        $math_quest_categories = MatchQuestCategory::with('questions')->get();
        $useranswer = Auth::user()->answer;
        $answerarray = array();
        if($useranswer){
          $answerarray = json_decode($useranswer->answer_data,true);
        }
        $title_by_page = 'Match Quest';
        return view('profile.matchquests', compact('math_quest_categories', 'useranswer', 'answerarray', 'title_by_page'));
    }

    public function viewOtherUserMatchQuests($encrypted_id){
        if($this->checkMoreMacthQuestAccess()){
            $decryted_other_user_id = base64_decode($encrypted_id);
            $other_userdata =  User::find($decryted_other_user_id);
            $visited_user_group_id = $other_userdata->group;
            $math_quest_categories = MatchQuestCategory::with(['visitedUserQuestions' => function ($query) use ($visited_user_group_id){
                $query->where('group_id', $visited_user_group_id);
            }])->get();
            $useranswer = $other_userdata->answer;
            $answerarray = array();
            if($useranswer){
              $answerarray = json_decode($useranswer->answer_data,true);
            }
            $title_by_page = 'Match Quest';
            $subscribed_plan = getCurrentUserPlan();
            $match_quest_count = getWebsiteSettingsByKey('sub_match_quest_count_'.$subscribed_plan->id);
            $match_quest_unlimited = 0;
            if($match_quest_count == -1){
                $match_quest_unlimited = 1;
            }
            $total_cat_count = MatchQuestCategory::all()->count();
            $questions_per_category = round($match_quest_count / $total_cat_count);

            return view('profile.view_other_user_matchquests', compact('math_quest_categories', 'useranswer', 'answerarray', 'title_by_page', 'questions_per_category', 'match_quest_unlimited'));
        }else{
            return redirect()->route('pricing');
        }
    }

    public function matchquestsSubmit(Request $request){
        $user = Auth::user();
        $getWords = WordsSecurity::pluck('title')->toArray();
        $answers = json_encode($request->answers);
        $compareAnswersWords = preg_replace('/\b(' . implode('|', $getWords) . ')\b/', '', $answers);
        $compareAnswersWords = preg_replace('/\b('.$user->second_life_full_name.')\b/','******',$compareAnswersWords);
        $user_second_life_full_name = explode(',',preg_replace('/\s+/',' ',$user->second_life_full_name));
        $getWords = array_merge($getWords,$user_second_life_full_name);
        $compareAnswersWords = preg_replace('/\b(\w*' . implode('|', $getWords) . '\w*)\b/', '******', $answers);

        if ($user->answer) {
            Answer::where('user_id', $user->id)->where('group_id', $user->group)->update(['answer_data' => $answers, 'group_id' => $user->group]);
        } else {
            $arrData = array(
                'user_id' => $user->id,
                'answer_data' => $compareAnswersWords,
                'group_id' => $user->group
            );
            Answer::create($arrData);
        }
        return redirect()->back()->with('success', 'Match Quest updated Successfully !! ');
    }

    // BLOCKED USERS LIST
    public function getBlockedUsers(){

        $users = Reportblock::where('user_id',Auth::user()->id)->paginate(16);
        return view('profile.blocked-users',compact('users'));

    }


}
