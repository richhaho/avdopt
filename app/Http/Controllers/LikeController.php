<?php

namespace App\Http\Controllers;
use App\Events\UsersWasMatched;
use App\Events\UserWasLiked;
use App\Events\UserAllNotification;
use Auth;
use App\Like;
use App\Match;
use App\GenderRole;
use App\UserMessage;
use App\User;
use App\Trials;
use App\WebsiteSetting;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MatchController;
use Illuminate\Http\Request;

class LikeController extends Controller
{

    public function index( $user_id = 0 ){
        $likes = array();
        if( !$user_id ){
            $user_id = Auth::user()->id;
        }
        $displaylike = 0;
        if(Auth::user()){
          if(isthisSubscribed()){
             $getSubscriptionsLikes = WebsiteSetting::where('meta_key','sub_view_my_likes_'.getCurrentUserPlan()->id)->first();
             $displaylike = $getSubscriptionsLikes->meta_value;
          }
          if(Auth::user()->role_id=='1'){
            $displaylike = 1000;
          }
        }
        $liked_by = Like::where('isliked', 1)->where('user_id', $user_id )->pluck('liked_by')->take($displaylike)->toArray();
        $mylikes = Like::where('isliked', 1)->where('liked_by', $user_id )->pluck('user_id')->take($displaylike)->toArray();
		$title_by_page = "My Likes";
        /*if( $liked_by ){
            $collection = collect($liked_by);
            $collection = $collection->merge($mylikes);
            $likes =  $collection->unique();
        }*/
        return view('user.myLikes', compact('likes','liked_by','mylikes','title_by_page') );
    }


   public function dolike(Request $request){


       $response = array('status' => 0 );
       $user = $request->input('user');

       $authuser = $request->input('authuser');
       $authuser_id = base64_decode($authuser);

       if($request->get('action') != null){
          $userid = $request->get('user');
          //get trial id
          $checkReq = Trials::WhereRaw('( (user_id = ' . Auth::user()->id .' && matcher_id = ' . $userid .' ) OR (user_id = ' . $userid .' && matcher_id = ' . Auth::user()->id .' ))' )->get()->first();

          if($checkReq){
            $accept = Trials::findorfail($checkReq->id);
            $accept->delete();
          }
       }else{
         $userid = base64_decode($user);
       }
       $userdata = User::find($userid);

       $authuser_data = User::where('id', '=', Auth::user()->id)->first();

       $genderdata = GenderRole::where('id', '=', $authuser_data->gender)->first();

       $gender = "his/her";

       if($genderdata != null){
         if($genderdata->gender == "male"){
           $gender = "his";
         }if($genderdata->gender == "female"){
           $gender = "her";
         }
       }


       $auth_username = $authuser_data->displayname;

       if( $userdata ){
           $data = Like::where('liked_by', Auth::user()->id )->where('user_id', $userid )->first();
           if( !$data ){
               $data = new Like;
               $response['like'] = $data->isliked = 1;
               //\Event::fire(new UserWasLiked($userdata, Auth::user()));

                $datalikeeother = Like::where('liked_by', $userid )->where('user_id',Auth::user()->id  )->first();
                
                $encodeid = base64_encode($authuser_data->id); 
                $url = url('/userprofile/'.$encodeid);               
                $userprofilelink = '<a href="'.$url.'">'.$auth_username.'</a>';
                
                $message = $userprofilelink." liked your profile. Like ".$gender." profile and you’ll have a potential match!";
                $admin = User::where('role_id', '=', 1)->first();
                $admin_id = $admin->id;

                $emaildata = array(
                  'email' => $userdata->email,
                  'displayname' => $userdata->displayname,
                  'email_message' => $message
                );

                $notficationdata = array(
                  'user_id' => $userdata->id,
                  'message' => $message,
                  'type' => 'like',
                  'created_by' => $admin_id
                );

                $sldata = array(
                  'uuid' => $userdata->uuid,
                  'message' => $message,
                  'type' => 'Like Notification'
                );

                $messagedata = array(
                  'user_id' => $admin_id,
                  'reciever_id' => $userdata->id,
                  'message' => $message,
                  'type' => 'message_notification'
                );

                $allnoticationdata = array(
                  'emailtype' =>$emaildata,
                  'messagetype' =>$messagedata,
                  'notificationtype' =>$notficationdata,
                  'sl_notificationtype' =>$sldata
                );
                \Event::fire(new UserAllNotification($allnoticationdata));

            }else{
               if( $data->isliked ){
                   $response['like'] = $data->isliked = 0;
               }else{
                  $response['like'] = $data->isliked = 1;
                  //\Event::fire(new UserWasLiked($userdata, Auth::user()));
                  
                  $encodeid = base64_encode($authuser_data->id); 
                  $url = url('/userprofile/'.$encodeid);               
                  $userprofilelink = '<a href="'.$url.'">'.$auth_username.'</a>';
                  
                  $message = $userprofilelink." liked your profile again! Since you already liked ".$gender.", ";
                  $message .= "this is an opportunity for a second chance match! Would you like to go on a Trial Date with ".$auth_username."? please visit url: ";
                  $message .= '<a href="'.url('/trials').'">Trial Date</a>';
                  $admin = User::where('role_id', '=', 1)->first();
                  $admin_id = $admin->id;

                  $emaildata = array(
                    'email' => $userdata->email,
                    'displayname' => $userdata->displayname,
                    'email_message' => $message
                  );

                  $notficationdata = array(
                    'user_id' => $userdata->id,
                    'message' => $message,
                    'type' => 'like',
                    'created_by' => $admin_id
                  );

                  $sldata = array(
                    'uuid' => $userdata->uuid,
                    'message' => $message,
                    'type' => 'Like Notification'
                  );

                  $messagedata = array(
                    'user_id' => $admin_id,
                    'reciever_id' => $userdata->id,
                    'message' => $message,
                    'type' => 'message_notification'
                  );

                  $allnoticationdata = array(
                    'emailtype' =>$emaildata,
                    'messagetype' =>$messagedata,
                    'notificationtype' =>$notficationdata,
                    'sl_notificationtype' =>$sldata
                  );
                  \Event::fire(new UserAllNotification($allnoticationdata));
               }
           }
           $data->user_id = $userid;
           $data->liked_by = Auth::user()->id;
           $data->save();
           $response['status'] = 1;
           $notification = array();
           if( $response['like'] ){

           }

           $ismatch = Match::checkMatch($userid);
           $matchdata = array();
           if( $ismatch ){
              $matchdata['user_id'] = $userid;
              $matchdata['is_match'] = ( $ismatch == 2 )? 1 : 0;
               MatchController::doMatch($matchdata);
               if($ismatch == 2)
               {
                  \Event::fire(new UsersWasMatched($userdata, Auth::user()));
               }
           }

       }
       $likecount = Like::where('isliked', 1)->where('user_id', $userid )->count();
       $response['likecount'] = $likecount;
       $response['matchcount'] = Match::matchCount($userid);
       return json_encode($response);
   }


}
