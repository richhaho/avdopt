@extends('layouts.master')
@section('htmlheader')
@php
use App\TrialLocation;
use App\FamilyRole;
@endphp
<link rel="stylesheet" type="text/css" href="{{ asset('css/slick.css') }}"/><link rel="stylesheet" type="text/css" href="http://kenwheeler.github.io/slick/slick/slick-theme.css"/>
<link rel="stylesheet" href="{{asset('user/css/bowseModal.css')}}">

<style>

  .showmodal{
    display: block;
  }

  .modal .form-group p{
      padding: 10px;
  }
  .success{
      color:green;
  }
  .failure{
      color: red;
  }
  .adptnow{
      max-width: 500px;
  }
  .adptnow .modal-header{
      display: flex;
      align-items: center;
  }
  .adptnow .modal-header h4{
      font-size: 18px;
      color: #455a64;
  }
  .adptnow .form-group{
    display: inherit;
  }
  .adptnow .modal-header button.close{
      margin-left: auto;
      opacity: 0.7;
  }
  .adptnow .modal-header button.close span{
      font-size: 24px;
      color: #000;
  }
  .adptnow .modal-body{
      padding: 20px 20px 25px;
  }
  .adptnow input[type="checkbox"]{
      width: 20px;
      height: 20px;
  }

</style>


<script type="text/javascript" src="{{ asset('js/slick.js') }}"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

@endsection
@section('main-content')

            <!-- ============================================================== -->
            <!-- Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->



            <div class="container-fluid trial_req_sec">
              <div class="row page-titles">
                @if(session()->has('message'))
                    <div class="col-md-12 alert alert-success">
                        {!! session()->get('message') !!}
                    </div>
                @endif


                @if(session()->has('warning'))
                    <div class="col-md-12 alert alert-warning">
                        {!! session()->get('warning') !!}
                    </div>
                @endif
                  
                <div class="col-xs-12 col-md-12 sucessmessage"></div>
                  <div class="col-md-5 align-self-center padding0">
                      <h3 class="text-themecolor">Trails Requests</h3>
                  </div>
                  <div class="col-md-7 align-self-center">
                      <ol class="breadcrumb">
                          <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                          <li class="breadcrumb-item">Trials Requests</li>

                      </ol>
                  </div>
              </div>
                    <div class="col-md-12 pd_all0">
                        <div class="card">
                            <!-- <div class="card-body p-b-0">
                                <h4 class="card-title"><b class="vertical_align"><img src="{{ asset('backend/images/trial.png') }}" alt="" class="all_users"><span>TRIAL REQUESTS</span></b></h4>
                            </div> -->
                            <div class="row">
                            <div class="col-md-3 leftSidebar">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs customtab" role="tablist">
                                <li class="nav-item">
                                  <a class="nav-link active" href="{{ route('trials.index')}}">
                                    <span class="hidden-sm-up"><i class="ti-home"></i></span>
                                    <span class="hidden-xs-down"><img src="{{ url('images')}}/Connected_Users.png" class="tabIcons"/> All Requests</span></a>
                                </li>
                                <li class="nav-item">
                                  <a class="nav-link" href="{{ route('trials.activeTrialsIndex')}}" >
                                    <span class="hidden-sm-up"><i class="ti-user"></i></span>
                                    <span class="hidden-xs-down"><img src="{{ url('images')}}/Timer.png" class="tabIcons"/> Active Trial Dates</span></a>
                                </li>
                                <li class="nav-item">
                                  <a class="nav-link" href="{{ route('trials.sentTrialsIndex')}}">
                                    <span class="hidden-sm-up"><i class="ti-email"></i></span>
                                    <span class="hidden-xs-down"><img src="{{ url('images')}}/TaskList.png" class="tabIcons"/> Sent Requests</span></a>
                                </li>
                                <!-- <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#decline" role="tab"><span class="hidden-sm-up"><i class="ti-email"></i></span> <span class="hidden-xs-down">Declined</span></a> </li> -->
                                <li class="nav-item">
                                  <a class="nav-link" href="{{ route('trials.expiredTrialsIndex')}}">
                                    <span class="hidden-sm-up"><i class="ti-email"></i></span>
                                    <span class="hidden-xs-down"><img src="{{ url('images')}}/Scheduled_Event.png" class="tabIcons"/>Expired Trial Dates</span></a>
                                </li>
                            </ul>
                            <!-- Tab panes -->
                            <div class="featuredmembers">
                              <div class="subs_sec new_sb ">
                                  <p><b><i class="fa fa-user" aria-hidden="true"></i>
                                      Featured Members</b></p>
                               </div>
                              @php
                                  $featuredUsers = getSubscribedFeatureUsers();
                              @endphp
                              @if($featuredUsers)
                              <ul class="trialFeature-members">
                                  @foreach($featuredUsers as $featuredUser)
                                      @php
                                           $profilepic = ( $featuredUser->user->profile_pic )? 'uploads/'.$featuredUser->user->profile_pic : 'images/default.png';
                                      @endphp
                                      <li>
                                      <a href="{{ url('userprofile')}}/{{ base64_encode($featuredUser->user->id) }}">
                                          <div class="match_img">
                                              <img src="{{ asset($profilepic) }}" alt="user" class="img-circle" />
                                          </div>
                                      </a>
                                    </li>
                                  @endforeach
                                </ul>

                              @endif
                              <div class="subs_sec shrt_sb"><span>Want to stand out from the crowd?</span></div>
                              <a data-toggle="modal" data-target="#feeaturePlanModal" class="btn btn-success" id="featurePlan-btn"> Get Featured
                              </a>
                            </div>
                            <div class="addBanners-sidebar">
                                <p>AD BANNER</p>
                            </div>
                          </div>
                          <div class="col-md-7 mainContent">
                            <div class="tab-content">
                                <div class="tab-pane active" id="home2" role="tabpanel">

                                  <p class="trialDefaultText">Although Trial Dates are not mandatory, we advise members to go on at least one Trial Date with a match before completing the adoption process. Trial Dates usually last for three days, although the attendees may end them at any time. <a href="{{url('cms/trialdates')}}">Learn More</a></p>

                                  @if(count($allRequests) > 0)
                                  <div class="row">
                                      @foreach($allRequests as $request)
                                      <?php
                                      $getLocation = TrialLocation::find($request->trial_location_id);
                                      $adopterFamilyRole = FamilyRole::find($request->trial_family_role);
                                      $adopteeFamilyRole = FamilyRole::find($request->adoptee_family_role);
                                      ?>


                                      <!-- SENT New Requests -->
                                      @if($request->is_sent == 1 && $request->is_accepted == 0 && $request->is_decline == 0 && $request->is_success == 0 && $request->is_ended == 0 && $request->auto_ended == 0)
                                      <div class="col-md-6 col-sm-6 col-lg-6">
                                        <div class="request-content">
                                         <div class="request-header">
                                             <div class="row">
                                               <div class="col-md-4">
                                                 @if($request->user_id == Auth::user()->id)
                                                   <img src="{{ url('uploads')}}/{{$request->matcherid->profile_pic}}" class="img-responsive"/>
                                                 @endif
                                                 @if($request->matcher_id == Auth::user()->id)
                                                   <img src="{{ url('uploads')}}/{{$request->userid->profile_pic}}" class="img-responsive"/>
                                                 @endif
                                               </div>
                                               <div class="col-md-8">
                                                 @if($request->matcher_id == Auth::user()->id)
                                                 <p><a href="{{ url('userprofile')}}/{{base64_encode($request->user_id)}}">{{$request->userid->display_name_on_pages}}</a> invited you on a Trial Date. Would you like to go as his/her {{$adopteeFamilyRole->title}}?</p>
                                                 @endif

                                                 @if($request->user_id == Auth::user()->id)
                                                 <p>You invited <a href="{{ url('userprofile')}}/{{base64_encode($request->matcher_id)}}">{{$request->matcherid->display_name_on_pages}}</a> on a Trial Date.</p>
                                                 @endif
                                               </div>
                                             </div>
                                         </div>
                                         <div class="request-body">
                                           <div class="row">
                                             <div class="col-md-4">
                                                 <a href="{{$getLocation->address}}" target="_blank"><img src="{{ url('uploads/location')}}/{{$getLocation->image}}" class="img-responsive locationImg"/></a>
                                             </div>
                                             <div class="col-md-8">
                                                <div class="locInfo">
                                                    <p class="location_name">{{$getLocation->name}}</p>
                                                    <p class="text-left"><b>Date: </b>{{date("d F Y",strtotime($request->trial_date))}}</p>
                                                    <p class="text-left"><b>Time:</b> {{date("h:ia", strtotime($request->trial_time))}} (SLT)</p>
                                                    <p class="text-left"><a href="{{$getLocation->address}}" class="label label-info" target="_blank"><i class="fa fa-map-marker" aria-hidden="true"></i> Location</a></p>
                                                </div>
                                             </div>
                                           </div>
                                         </div>
                                         <div class="request-footer">
                                           <div class="requestActionButtons">
                                            @if($request->matcher_id == Auth::user()->id)
                                              <a class="btn btn-success" href="{{ route('trials.accept', $request->id) }}">ACCEPT</a>
                                              <a class="btn btn-danger" href="{{ route('trials.decline', $request->id) }}">DECLINE</a>

                                              @if($request->is_maybe == 0)
                                                <a class="btn btn-warning" href="{{ route('trials.maybe', $request->id) }}">MAY BE</a>
                                              @else
                                                <a data-href="{{url('schedule')}}/{{base64_encode($request->user_id)}}" class="btn btn-warning" onclick="secondRequest($(this) , '{{$request->userid->display_name_on_pages}}', '{{ Auth::user()->id}}', '{{$request->matcher_id}}')">RESCHEDULE</a>
                                                <a class="btn btn-danger" href="{{ route('trials.cancelrequest', $request->id) }}">CANCEL</a>
                                              @endif
                                            @else
                                              @if($request->is_maybe == 1)
                                              <a data-href="{{url('schedule')}}/{{base64_encode($request->matcher_id)}}" class="btn btn-warning" onclick="secondRequest($(this) , '{{$request->matcherid->display_name_on_pages}}', '{{ Auth::user()->id}}', '{{$request->matcher_id}}')">RESCHEDULE</a>
                                              <a class="btn btn-danger" href="{{ route('trials.cancelrequest', $request->id) }}">CANCEL</a>
                                              @endif
                                            @endif
                                              <a href="{{ url('chat')}}" class="btn btn-info">CHAT</a>
                                            </div>
                                         </div>
                                       </div>
                                      </div>
                                      @endif

                                      <!-- Accepted Requests -->
                                      @if($request->is_sent == 1 && $request->is_accepted == 1 && $request->is_decline == 0 && $request->is_success == 0 && $request->is_ended == 0 && $request->auto_ended == 0)

                                      <div class="col-md-6 col-sm-6 col-lg-6">
                                        <div class="request-content">
                                         <div class="request-header">
                                             <div class="row">
                                               <div class="col-md-4">
                                                 @if($request->user_id == Auth::user()->id)
                                                   <img src="{{ url('uploads')}}/{{$request->matcherid->profile_pic}}" class="img-responsive"/>
                                                 @endif
                                                 @if($request->matcher_id == Auth::user()->id)
                                                   <img src="{{ url('uploads')}}/{{$request->userid->profile_pic}}" class="img-responsive"/>
                                                 @endif
                                               </div>
                                               <div class="col-md-8">

                                                 @if($request->matcher_id == Auth::user()->id)
                                                 <p>You and <a href="{{ url('userprofile')}}/{{base64_encode($request->user_id)}}">{{$request->userid->display_name_on_pages}}</a> are currently on a Trial Date :)</p>
                                                 @endif
                                                 @if($request->user_id == Auth::user()->id)
                                                 <p>You and <a href="{{ url('userprofile')}}/{{base64_encode($request->matcher_id)}}">{{$request->matcherid->display_name_on_pages}}</a> are currently on a Trial Date :)</p>
                                                 @endif
                                               </div>
                                             </div>
                                         </div>
                                         <div class="request-body">
                                             <?php
                                             $planInfo = getUserPlan($request->user_id);
                                             $planInfo = json_decode($planInfo);

                                             $planTrialDays = $planInfo->trial_period;
                                             $currentDateTime = new DateTime(date("Y-m-d H:i:s"));

                                             $trialDateTime = $request->trial_date.' '.$request->trial_time;

                                             if($planTrialDays != null){
                                               $newTrialDate = date("Y-m-d H:i:s", strtotime('+'.$planTrialDays.' days', strtotime($trialDateTime)));
                                             }else{
                                                $newTrialDate = date("Y-m-d H:i:s", strtotime('+2 days', strtotime($trialDateTime)));
                                             }

                                             $newTrialDate1 =  new DateTime($newTrialDate);
                                             $trialDateTime1 = new DateTime($trialDateTime);

                                             if($currentDateTime < $trialDateTime1){

                                                $interval = $currentDateTime->diff($trialDateTime1);
                                                echo '<p>This Trial Date will be begins in:</p>';
                                                $days     =  $interval->format('%d');
                                                if($days > 0)
                                                    echo '<p class="remaining_time">'.$interval->format('%a days %h hours %i minutes').'</p>';
                                                else
                                                    echo '<p class="remaining_time">'.$interval->format('%h hours %i minutes').'</p>';

                                             }

                                             if($currentDateTime >= $trialDateTime1 && $currentDateTime <= $newTrialDate1){

                                               $interval = $currentDateTime->diff($newTrialDate1);
                                                echo '<p>This Trial Date will end in:</p>';
                                                $days     =  $interval->format('%d');
                                                if($days > 0)
                                                    echo '<p class="remaining_time">'.$interval->format('%a days %H hours %i minutes').'</p>';
                                                else
                                                    echo '<p class="remaining_time">'.$interval->format('%h hours %i minutes').'</p>';
                                             }
                                             ?>
                                         </div>
                                         <div class="request-footer">
                                           <div class="requestActionButtons">
                                            <a href="{{$getLocation->address}}" class="btn btn-primary" target="_blank"><i class="fa fa-map-marker" aria-hidden="true"></i> Location</a>
                                           @if($request->is_accepted == 1 && $request->is_success == 0 && $request->is_ended == 0)
                                             <a href="#" data-toggle="modal" data-id="{{$request->id}}" data-target="#endTrial-modal" class="btn btn-danger trialEnd-btn">END</a>
                                           @endif
                                           <a href="{{ url('chat')}}" class="btn btn-info">CHAT</a>
                                         </div>
                                         </div>
                                       </div>
                                      </div>
                                      @endif

                                      <!-- Declined Requests -->

                                      @if($request->is_sent == 1 && $request->is_decline == 1 && $request->is_ended == 0 && $request->auto_ended == 0)
                                      <div class="col-md-6 col-sm-6 col-lg-6">
                                        <div class="request-content">
                                         <div class="request-header">
                                             <div class="row">
                                               <div class="col-md-4">
                                                  @if($request->matcher_id == Auth::user()->id)
                                                   <img src="{{ url('uploads')}}/{{$request->userid->profile_pic}}" class="img-responsive"/>
                                                   @else
                                                      <img src="{{ url('uploads')}}/{{$request->matcherid->profile_pic}}" class="img-responsive"/>
                                                   @endif
                                               </div>
                                               <div class="col-md-8">
                                                   @if($request->matcher_id == Auth::user()->id)
                                                     <p>You have declined the Trial Request sent by <b>{{ $request->userid->display_name_on_pages }}</b></p>
                                                   @else
                                                    <p>Your Trial Date has declined by <b>{{ $request->matcherid->display_name_on_pages }}</b></p>
                                                   @endif
                                               </div>
                                             </div>
                                         </div>
                                         <div class="request-body">
                                           <div class="row">
                                             <div class="col-md-4">
                                                 <a href="{{$getLocation->address}}" target="_blank"><img src="{{ url('uploads/location')}}/{{$getLocation->image}}" class="img-responsive locationImg"/></a>
                                             </div>
                                             <div class="col-md-8">
                                                <div class="locInfo">
                                                    <p class="location_name">{{$getLocation->name}}</p>
                                                    <p class="text-left"><b>Date: </b>{{date("d F Y",strtotime($request->trial_date))}}</p>
                                                    <p class="text-left"><b>Time:</b> {{date("h:ia", strtotime($request->trial_time))}} (SLT)</p>
                                                    <p class="text-left"><a href="{{$getLocation->address}}" class="label label-info" target="_blank"><i class="fa fa-map-marker" aria-hidden="true"></i> Location</a></p>
                                                </div>
                                             </div>
                                           </div>

                                         </div>
                                         <div class="request-footer">
                                           <div class="requestActionButtons">
                                               <a class="btn btn-danger" href="{{ route('trials.cancelrequest', $request->id) }}">CANCEL</a>
                                             </div>
                                         </div>
                                       </div>
                                      </div>
                                      @endif

                                      <!-- expired Requests -->

                                      @if($request->is_ended == 1 || $request->auto_ended == 1)
                                      @if($request->is_accepted == 1)

                                      @php

                                      $adopter_family_role = FamilyRole::find($request->trial_family_role)->title;
                                      $adopter_family_gender = (FamilyRole::find($request->trial_family_role)->gender == 'female')  ? "she" : "he" ;
                                      $adoptee_family_role = FamilyRole::find($request->adoptee_family_role)->title;
                                      $adoptee_family_gender = (FamilyRole::find($request->adoptee_family_role)->gender == 'female')? "she" : "he";

                                      @endphp
                                      <div class="col-md-6 col-sm-6 col-lg-6">
                                        <div class="request-content">
                                         <div class="request-header">
                                             <div class="row">
                                               <div class="col-md-4">
                                                 @if($request->user_id == Auth::user()->id)
                                                     @php
                                                       $userId = $request->user_id;
                                                     @endphp
                                                   <img src="{{ url('uploads')}}/{{$request->matcherid->profile_pic}}" class="img-responsive"/>
                                                @endif
                                                @if($request->matcher_id == Auth::user()->id)
                                                    @php
                                                      $userId = $request->matcher_id;
                                                    @endphp
                                                   <img src="{{ url('uploads')}}/{{$request->userid->profile_pic}}" class="img-responsive"/>
                                                @endif
                                               </div>
                                               <div class="col-md-8">

                                                 @if($request->user_id == Auth::user()->id)
                                                       @php
                                                         $userId = $request->user_id;
                                                       @endphp
                                                       <b>{{$request->userid->display_name_on_pages}}</b> & <b>{{$request->matcherid->display_name_on_pages}}</b> your Trial Date has ended.
                                                 @endif

                                                 @if($request->matcher_id == Auth::user()->id)
                                                     @php
                                                       $userId = $request->matcher_id;
                                                     @endphp
                                                     <b>{{$request->matcherid->display_name_on_pages}}</b> & <b>{{$request->userid->display_name_on_pages}}</b> your Trial Date has ended.
                                                 @endif

                                               </div>
                                             </div>
                                         </div>
                                         <div class="request-body">

                                            @if($request->user_id == Auth::user()->id)
                                             <p>You may leave a review on <b>{{$request->matcherid->display_name_on_pages}}</b> profile when ready. Reviews are permanent and it will add to <b>{{$request->matcherid->display_name_on_pages}}</b> adoption history.</p>

                                           @else
                                            <p>You may leave a review on <b>{{$request->userid->display_name_on_pages}}</b> profile when ready. Reviews are permanent and it will add to <b>{{$request->userid->display_name_on_pages}}</b> adoption history.</p>

                                           @endif
                                         </div>

                                         <div class="request-footer">
                                           <!-- <h3>How was Trial Date?</h3> -->
                                           <div class="requestActionButtons">
                                           @if($request->user_id == Auth::user()->id)
                                             <a class="btn btn-success" data-toggle="modal" data-target="#TrialSuccess-{{$request->id}}" data-id="{{$request->id}}" link="{{ route('trials.success', $request->id) }}">SUCCESS</a>


                                             <div class="modal fade trialSuccessPopup" id="TrialSuccess-{{$request->id}}">
                                                 <div class="modal-dialog">
                                                   <div class="modal-content">
                                                     <!-- Modal Header -->
                                                     <div class="modal-header">
                                                       <h4 class="modal-title">Adopt Request</h4>
                                                       <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                     </div>
                                                     <!-- Modal body -->
                                                     <div class="modal-body">
                                                        <form class="form-horizontal form_common submitAdoptForm" id="submitAdoptForm" role="form" name="submitAdoptForm" method="POST">

                                                         <input type="hidden" name="trial" value="{{ $request->id }}" id="trial"/>
                                                             <div class="row">
                                                               <div class="row">
                                                                 <div class="form-group d-flex">
                                                                    <div class="col-md-1">
                                                                        <input type="checkbox" class="form-control checkbox" id="agree" name="agree">
                                                                    </div>
                                                                    <div class="col-md-11 terms">
                                                                      @php

                                                                          $reciverUrl = url("userprofile").'/'.base64_encode($request->matcher_id);

                                                                          $reciverName = $request->matcherid->display_name_on_pages;

                                                                          $reciverLink = '<a href="'.$reciverUrl.'">'.$reciverName.'</a>';

                                                                          $adopt_message = "By signing this certificate, you promise to give ".$reciverLink." all of the love and care that ".$adoptee_family_gender." require in return, your ".$adoptee_family_role." will give you all the love, comfort and attention you need. Failure to meet adoption requirements herein, may result in dissolution. ";

                                                                      @endphp
                                                                           <p>
                                                                             {!! $adopt_message !!}
                                                                           </p>
                                                                      </div>
                                                                     </div>
                                                                 </div>
                                                             </div>

                                                               <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                                               <button type="button" class="btn btn-primary submitBtn" id="submit">Yes, Please</button>

                                                       </form>

                                                     </div>
                                                     <!-- Modal footer -->
                                                     <div class="modal-footer">
                                                     </div>
                                                   </div>
                                                 </div>
                                             </div>

                                             <a class="btn btn-danger " href="{{ route('trials.cancelrequest', $request->id) }}">CANCEL</a>

                                             <a data-href="{{url('schedule')}}/{{base64_encode($request->matcher_id)}}" class="btn btn-warning" onclick="secondRequest($(this) , '{{$request->matcherid->display_name_on_pages}}', '{{ Auth::user()->id}}', '{{$request->matcher_id}}')">RESCHEDULE</a>

                                             <a class="btn btn-primary" HREF="#">REVIEW</a>
                                           @endif

                                           @if($request->matcher_id == Auth::user()->id)
                                             <a class="btn btn-success" data-toggle="modal" data-target="#TrialSuccess-{{$request->id}}" data-id="{{$request->id}}" link="{{ route('trials.success', $request->id) }}">SUCCESS</a>

                                             <div class="modal fade trialSuccessPopup" id="TrialSuccess-{{$request->id}}">
                                                 <div class="modal-dialog">
                                                   <div class="modal-content">
                                                     <!-- Modal Header -->
                                                     <div class="modal-header">
                                                       <h4 class="modal-title">Adoptee Trial Success</h4>
                                                       <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                     </div>
                                                     <!-- Modal body -->
                                                     <div class="modal-body">
                                                         <form class="form-horizontal form_common submitAdoptForm" id="submitAdoptForm" role="form" name="submitAdoptForm" method="POST">
                                                           <input type="hidden" name="trial" value="{{ $request->id }}" id="trial"/>

                                                               <div class="row">
                                                                 <div class="form-group d-flex">
                                                                    <div class="col-md-1">
                                                                        <input type="checkbox" class="form-control checkbox" id="agree" name="agree">
                                                                    </div>
                                                                    <div class="col-md-11 terms">
                                                                      @php
                                                                      $reciverUrl = url("userprofile").'/'.base64_encode($request->user_id);
                                                                      $reciverName = $request->userid->display_name_on_pages;
                                                                      $reciverLink = '<a href="'.$reciverUrl.'">'.$reciverName.'</a>';
                                                                      $adopt_message = "By signing this certificate, you promise to give ".$reciverLink." all of the love and care that ".$adopter_family_gender."  require in return, your ".$adopter_family_role." will give you all the love, comfort and attention you need. Failure to meet adoption requirements herein, may result in dissolution. ";


                                                                      @endphp
                                                                           <p>{!! @$adopt_message !!}</p>
                                                                      </div>
                                                                     </div>

                                                               </div>
                                                               <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                                              <button type="button" class="btn btn-primary submitBtn" id="submit">Yes, Please</button>

                                                         </form>
                                                     </div>
                                                     <!-- Modal footer -->
                                                     <div class="modal-footer">
                                                     </div>
                                                   </div>
                                                 </div>
                                             </div>

                                             <a class="btn btn-danger " href="{{ route('trials.cancelrequest', $request->id) }}">CANCEL</a>

                                             <a data-href="{{url('schedule')}}/{{base64_encode($request->user_id)}}" class="btn btn-warning" onclick="secondRequest($(this) , '{{$request->userid->display_name_on_pages}}', '{{ Auth::user()->id}}', '{{$request->user_id}}')">RESCHEDULE</a>

                                             <a class="btn btn-primary" HREF="#">REVIEW</a>
                                           @endif
                                           <a href="{{ url('chat')}}" class="btn btn-info">CHAT</a>
                                         </div>
                                         </div>
                                       </div>
                                      </div>
                                      @else
                                      <div class="col-md-6 col-sm-6 col-lg-6">
                                        <div class="request-content">
                                         <div class="request-header">
                                             <div class="row">
                                               <div class="col-md-4">
                                                   <img src="{{ url('uploads')}}/{{$request->userid->profile_pic}}" class="img-responsive"/>
                                               </div>
                                               <div class="col-md-8">
                                                 <p>A Trial request sent by @if($request->matcher_id == Auth::user()->id) <b>"{{$request->userid->display_name_on_pages}}"</b> @else You to <b>"{{$request->matcherid->display_name_on_pages}}"</b> @endif has been Expired.</b></p>
                                               </div>
                                             </div>
                                         </div>
                                         <div class="request-body">
                                           <div class="row">
                                             <div class="col-md-4">
                                                 <a href="{{$getLocation->address}}" target="_blank"><img src="{{ url('uploads/location')}}/{{$getLocation->image}}" class="img-responsive locationImg"/></a>
                                             </div>
                                             <div class="col-md-8">
                                                <div class="locInfo">
                                                    <p class="location_name">{{$getLocation->name}}</p>
                                                    <p class="text-left"><b>Date: </b>{{date("d F Y",strtotime($request->trial_date))}}</p>
                                                    <p class="text-left"><b>Time:</b> {{date("h:ia", strtotime($request->trial_time))}} (SLT)</p>
                                                    <p class="text-left"><a href="{{$getLocation->address}}" class="label label-info" target="_blank"><i class="fa fa-map-marker" aria-hidden="true"></i> Location</a></p>
                                                </div>
                                             </div>
                                           </div>
                                         </div>
                                         <div class="request-footer">
                                           <div class="requestActionButtons">
                                           @if($request->user_id == Auth::user()->id)
                                             <a data-href="{{url('schedule')}}/{{base64_encode($request->matcher_id)}}" class="btn btn-info" onclick="secondRequest($(this),'{{$request->matcherid->display_name_on_pages}}', '{{ Auth::user()->id}}', '{{$request->matcher_id}}')">RESCHEDULE</a>
                                           @endif
                                           @if($request->matcher_id == Auth::user()->id)
                                             <a data-href="{{url('schedule')}}/{{base64_encode($request->user_id)}}" class="btn btn-info" onclick="secondRequest($(this), '{{$request->userid->display_name_on_pages}}', '{{ Auth::user()->id}}', '{{$request->user_id}}')">RESCHEDULE</a>

                                           @endif
                                           <a class="btn btn-danger" href="{{ route('trials.cancelrequest', $request->id) }}">CANCEL</a>
                                           </div>
                                         </div>
                                       </div>
                                      </div>
                                      @endif
                                      @endif





                                      <!-- End Expired -->
                                      @endforeach
                                    </div>
                                  @else

                                    <h6 class="text-center">You have no Trial Date requests as yet. After matching with users, you may ask them on Trial Dates.</h6>

                                  @endif
                                  <div class="trialSPagnation">
                                    {{ $allRequests->links() }}
                                  </div>
                                </div>
                        </div>
                      </div>
                      <div class="col-md-2 rightSidebar">
                          <div class="rightBanner-images">
                              <img src="{{ url('images')}}/img2.jpg" class="img-respomsive" />
                              <img src="{{ url('images')}}/img2.jpg" class="img-respomsive" />
                              <img src="{{ url('images')}}/img2.jpg" class="img-respomsive" />
                              <img src="{{ url('images')}}/img2.jpg" class="img-respomsive" />
                          </div>
                      </div>
                    </div><!-- end row -->
                        </div>
                    </div>
                </div>



                <!-- --------------Featured Members popup----------------- -->
                <!-- Modal -->
                <div class="featuredmembers_img">
                    <div class="modal fade" id="feeaturePlanModal" tabindex="-1" role="dialog"
                         aria-labelledby="feeaturePlanModalLabel"
                         aria-hidden="true">
                        <div class="modal-dialog" style="max-width: inherit" role="document">
                            <div class="modal-content featured_popup">
                                <div class="modal-header padding0">
                                    <h5 class="modal-title" id="feeaturePlanModalLabel">
                                        <img class="img-responsive" alt="Profile Img"
                                             src="{{asset('frontend/images/flame.png')}}">GET FEATURED</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p>The first part of standing out is getting noticed. Even before visitors see your profile
                                        and activity, they see you. Studies show that featured profiles are nine times
                                        more likely to be viewed.</p>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <a href="{{ url('featured-members')}}" class="btn btn-md featmembtn">View Featured Members</a>
                                        </div>
                                    </div>
                                    <div class="scheme_box">
                                        @php
                                            $tokenamount = getWebsiteSettingsByKey('token_amount');
                                            $featuredPlans = getFeaturedPlans();
                                            if(Auth::check())
                                                $subscription = App\Subscription::where('user_id', Auth::user()->id)->where('name', 'feature')->first();
                                        @endphp
                                        @if(!empty($featuredPlans))
                                            <div class="">
                                                @foreach($featuredPlans as $featuredPlan)
                                                    <div class="col-sm-12 col-md-6 ">
                                                        <div class="basic">
                                                            <div class="left">
                                                                <h3 class="fontclr">{{ @$featuredPlan->name }}</h3>
                                                            </div>
                                                            <div class="right">
                                                                <h5>{{ @$featuredPlan->tokens }} Tokens</h5>
                                                            </div>
                                                            <div class="feat_infop"><p class=" mtop20">{{ $featuredPlan->info }}</p></div>
                                                            @if( $subscription )
                                                                @php
                                                                    $user = App\User::find(Auth::user()->id);
                                                                @endphp
                                                                @if( $subscription->stripe_plan == $featuredPlan->plan_id && $user->subscribed('feature') && ( !$user->subscription('feature')->onGracePeriod() ) )
                                                                    <form class="form-horizontal " role="form" method="POST"
                                                                          action="{{ url('/')}}/subscription/featurecancel">
                                                                        @csrf
                                                                        <input type="hidden" name="chargeId"
                                                                               value="{{ $featuredPlan->plan_id }}">
                                                                        <button type="submit"
                                                                                onclick="return confirm('Are you sure you want to cancel this plan?')"
                                                                                class="mtop10 mb popbtn"><span>Cancel</span>
                                                                        </button>
                                                                    </form>
                                                                @else
                                                                    <form class="form-horizontal " role="form" method="POST"
                                                                          action="{{ url('/')}}/subscription/checkout">
                                                                        @csrf
                                                                        <input type="hidden" name="chargeId"
                                                                               value="{{ $featuredPlan->plan_id }}">
                                                                        <button type="submit"
                                                                                onclick="return confirm('Are you sure you want to upgrade this plan?')"
                                                                                class="mtop10 mb popbtn"><span>Buy Now</span>
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            @else
                                                                <form class="form-horizontal " role="form" method="POST"
                                                                      action="{{ url('/')}}/subscription/checkout">
                                                                    @csrf
                                                                    <button type="submit" class="mtop10 mb"><span>Buy</span>
                                                                    </button>
                                                                    <input type="hidden" name="chargeId"
                                                                           value="{{ $featuredPlan->plan_id }}">
                                                                    <div class="hidescript">
                                                                    <!--<script
                                                                                src="https://checkout.stripe.com/checkout.js"
                                                                                class="stripe-button"
                                                                                data-key="{{ env('STRIPE_KEY') }}"
                                                                                data-amount="{{ ( $tokenamount )? $featuredPlan->tokens * ( $tokenamount * 100 ): $featuredPlan->tokens * 100 }}"
                                                                                data-name="{{ $featuredPlan->name }}"
                                                                                data-description="Feature Profile charge"
                                                                                data-image="{{url('/')}}/backend/images/logo.jpg"
                                                                                data-locale="auto">
                                                                        </script>-->
                                                                    </div>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="col-md-12">
                                                <h4 class="alert alert-info"> Please Contact with admin to get featured
                                                    plans </h4>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- --------------End Featured Members popup----------------- -->


                <!----- End Trial Popup -->

                <!-- Modal -->
                <div id="endTrial-modal" class="modal fade" role="dialog">
                  <div class="modal-dialog">
                    <form method="post" action="{{ route('trials.trialEnded') }}">
                          @csrf
                     <!-- Modal content-->
                        <div class="modal-content">
                          <div class="modal-header">
                            <h4 class="modal-title">Please select a reason to End this Trial Date</h4>
                          </div>
                          <div class="modal-body">
                                <input type="hidden" name="trial_id" value="" id="endTrialId" />




                                @if($endReasons)
                                    @foreach($endReasons as $reason)
                                        <div class="form-group">
                                          @if($loop->first)
                                            <input type="radio" name="trial_end_reason" data-id="{{$reason->id}}" value="{{$reason->id}}" id="reason_{{$reason->id}}" checked required/>
                                          @else

                                            <input type="radio" name="trial_end_reason" data-id="{{$reason->id}}" value="{{$reason->id}}" id="reason_{{$reason->id}}" required/>
                                          @endif
                                          <label for="reason_{{$reason->id}}">{{$reason->title}}</label>
                                        </div>

                                    @endforeach
                                  @endif
                          </div>
                          <div class="modal-footer">
                            <center>
                              @if($endReasons)
                                <button type="submit" name="submitEndReason" value="End Trial" class="btn btn-success submitEndReason">End Trial</button>
                              @endif
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                          </center>
                          </div>
                      </div>
                    </form>
                  </div>
                </div>


                {{-- ADOPTION MODAL STARTS HERE --}}

                @if(@session()->get('done_status') == 'adoption')

                  @php
                    $showclass = 'show showmodal';
                  @endphp

                @endif

                <!-- Modal -->
                    <div class="modal fade {{@$showclass}}" id="sendRequestBtn" role="dialog">
                       <div class="modal-dialog adptnow">
                           <div class="modal-content">
                               <!-- Modal Header -->
                                <div class="modal-header">
                                   <h4 class="modal-title" id="myModalLabel">Adopt Request</h4>
                                   <button type="button" class="close" data-dismiss="modal">
                                       <span aria-hidden="true">&times;</span>
                                       <span class="sr-only">Close</span>
                                   </button>
                               </div>

                               <!-- Modal Body -->
                               <div class="modal-body">
                                   <p class="statusMsg"></p>
                                   <form class="form-horizontal form_common submitAdoptForm" id="submitAdoptForm" role="form" name="submitAdoptForm" method="POST">
                                    {{-- <input type="hidden" name="trial" id="" value="" id="trial"/> --}}
                                      <input type="hidden" name="trial_id" value="{{@session()->get('adoption_trail_id')}}" id="endTrialIdAdoption" />


                                      <div class="row">
                                       <div class="form-group">
                                          <div class="col-md-1">
                                              <input type="checkbox" class="form-control checkbox" id="agree" name="agree">
                                          </div>
                                          <div class="col-md-11 terms">
                                                 <p>{!! @session()->get('adoption_message') !!}</p>
                                            </div>
                                           </div>
                                       </div>
                                          <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                          <button type="button" class="btn btn-primary submitBtnAdoption" id="submit">Yes, Please</button>

                                   </form>
                               </div>
                          </div>
                       </div>
                    </div>
                {{-- ADOPTION MODAL ENDS HERE --}}


                <!----------------------->
@endsection

@section('footer')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-sweetalert/1.0.1/sweetalert.min.css" integrity="sha256-zuyRv+YsWwh1XR5tsrZ7VCfGqUmmPmqBjIvJgQWoSDo=" crossorigin="anonymous" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-sweetalert/1.0.1/sweetalert.min.js" integrity="sha256-JirYRqbf+qzfqVtEE4GETyHlAbiCpC005yBTa4rj6xg=" crossorigin="anonymous"></script>
<script type="text/javascript">
    $(document).ready(function(){
          $(".submitBtnAdoption").click(function(e){
            e.preventDefault();

            var agree = 0;
            if ($('#agree').is(":checked"))
            {
              agree = 1;
            }

                $.ajax({
                    method: "POST",
                    url: "{{url('ajaxrequest/adopt-request')}}",
                    data: {
                        // adoptee_family_role: $("#adoptee_family_role").val(),
                        agree: agree,
                        trial: $("#endTrialIdAdoption").val(),
                        _token: "{{csrf_token()}}"
                    },
                })
              .done(function( data ) {

                console.log(data);
                if(data.status == 200){
                    $("#sendRequestBtn .modal-body").html("<h5 class='success'><i class='fa fa-check'> </i> "+data.message+"</h5>");
                    $("#sendRequestBtn .modal-footer").empty();
                    $('#sendRequestBtn').modal('toggle');
                    setInterval(function(){ $("#trialRequest_alert").remove(); window.location = '{{route('adoptions.index')}}'   }, 1000);

                    //
                }else if(data.status == 400){

                    $("#sendRequestBtn .modal-body .terms .failure").remove();
                     $("#sendRequestBtn .modal-body .terms").append("<p class='failure'> "+data.message+"</p>");
                    $("#sendRequestBtn .modal-footer").empty();
                }else{
                    $("#sendRequestBtn .modal-body").html("<h5 class='failure'><i class='fa fa-check'> </i> "+data.message+"</h5>");
                    $("#sendRequestBtn .modal-footer").empty();
                }
              //location.reload();
            });

          });




        $('#accepted .btn').click(function(){
            var href = $(this).attr('link');
            var id = $(this).attr('data-id');
            $('#forsucccess').attr('action',href);
            $('#hiddenval').val(id);
        });

      $(".trialEnd-btn").click(function(){
        var trial_id = $(this).attr('data-id');

        // alert(trial_id);

        $("#endTrialId").val(trial_id);
        $("#endTrialIdAdoption").val(trial_id);



      });
    });


  function get_adoption_message(id){
    alert();
     jQuery.ajax({
          url: window.Laravel.url + '/ajaxrequest/get-adoption-message/'+id,
          type: 'GET',
          data: {'_token': window.Laravel.csrfToken},
          dataType: 'JSON',
          success: function (data) {
            console.log("DATA IS ",data);
              // if( data.status ){
              //   swal.close();
              //   location.reload();
              // }
          }
      });
  }

    function secondRequest(item, username, liked_by, user_id){
      var link = item.attr("data-href");
      swal({
          title: "Are you sure?",
          text: "Every match deserves a second chance! Would you like to go on another Trial Date with '"+username+"'?",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          confirmButtonText: "Yes, Reschedule it",
          cancelButtonText: "No",
          closeOnConfirm: false,
          closeOnCancel: false
        },
        function(isConfirm) {
          if (isConfirm) {
            window.location.href=link;
          } else {
            jQuery.ajax({
                url: window.Laravel.url + '/ajaxrequest/like',
                type: 'POST',
                data: {'_token': window.Laravel.csrfToken, 'user':user_id ,'action':'trialDislike'},
                dataType: 'JSON',
                success: function (data) {
                    if( data.status ){
                      swal.close();
                      location.reload();
                    }
                }
            });

          }
        });

    }
    $("button#submit").click(function(e){
      e.preventDefault();
        var agree = 0;
        if ($('#agree').is(":checked"))
        {
          agree = 1;
        }
        var id =$("#trial").val();
        $.ajax({
            method: "POST",
            url: "{{url('ajaxrequest/adopt-request')}}",
            data: {
                // adoptee_family_role: $("#adoptee_family_role").val(),
                agree: agree,
                trial: $("#trial").val(),
                _token: "{{csrf_token()}}"
            },
        })
        .done(function( data ) {
        console.log(data);
        if(data.status == 200){
            $("#btnModal"+id).remove();
            $(".sucessmessage").empty();
            $(".sucessmessage").html("<h5 class='success'><i class='fa fa-check'> </i> "+data.message+"</h5>");
            // $("#sendRequestBtn .modal-footer").empty();
            $('#TrialSuccess-'+id).modal('toggle');
            setInterval(function(){
                  location.reload();
            }, 1000);
            //
        }else if(data.status == 400){
            $("#sendRequestBtn"+id+" .modal-body .terms .failure").remove();
            $("#sendRequestBtn"+id+" .modal-body .terms").append("<p class='failure'> "+data.message+"</p>");
            $("#sendRequestBtn"+id+" .modal-footer").empty();
        }else{
            $("#sendRequestBtn"+id+" .modal-body").html("<h5 class='failure'><i class='fa fa-check'> </i> "+data.message+"</h5>");
            $("#sendRequestBtn"+id+" .modal-footer").empty();
        }
      //location.reload();
    });
  });



//     $('.pagination a').on('click', function(e){
//     e.preventDefault();
//     var url = $(this).attr('href');
//     $.post(url, $('#search').serialize(), function(data){
//         $('#expired').html(data);
//     });
// });
</script>
@endsection
