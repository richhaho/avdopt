@extends('layouts.master')
@section('htmlheader')

<style>
.pricetab h4 {
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
    color: #fff;
    padding: 18px 0;
    margin-bottom: 0;
}
.pricetab > p {
    background-color: #ffffff;
    border-bottom-left-radius: 12px;
    border-bottom-right-radius: 12px;
    color: #000;
    font-size: 17px;
    font-style: italic;
    padding: 48px 40px 40px;
}
.pricetab button {
    border: 0 none;
    border-radius: 8px;
    color: #fff;
    display: inline-block;
    max-width: 150px;
    padding: 4px 30px;
    text-transform: uppercase;
}
.content_heading h3 {
    color: #000;
}
.font28 {
    font-size: 28px;
}
.method .col-md-3:nth-of-type(1) {
    border-left: 2px solid #eee;
}
.list-header .col-md-3, .list-header .col-md-2 {
    border-top: 2px solid #eee;
}
.method .col-md-3, .method .col-md-2 {
    border-bottom: 2px solid #eee;
    border-right: 2px solid #eee;
}
.padding0 {
    padding: 0;
}

element {

}
.header {

    color: #000;
    font-size: 20px;
    font-weight: 900;
    padding: 8px 0;
    text-transform: uppercase;

}
.method .cell:nth-of-type(2) {
    border-top: 2px solid #eee;
}
.method .cell {
    padding: 8px 0px;
    border-bottom: 2px solid #eee;
}
.propertyname, .cell {
    font-size: 14px;
}
.width80 {
    width: 80%;
    margin: 0 auto;
    display: block;
}
.mtop40 {
    margin-top: 40px;
}

h4.pricetab_green, h4.pricetab_red {
    background: #1976d2!important;

}
.pricetab h4 span {
    font-weight: bold;
    margin-bottom: 7px;
}
.block {
    display: block;
}
.pricetab h4 i {
    font-size: 17px;
}
i {
    cursor: pointer;
    position: relative;
}
 .pricetab button {
	border: 0 none;
	border-radius: 8px;
	color: #fff;
	display: inline-block;
	max-width: 150px;
	padding: 4px 30px;
	text-transform: uppercase;
}
.pricetab button {
	 background: #1976d2!important;
}
.buy_tokens span, .top_intro span {
    display: block;
    font-size: 18px;
    font-style: italic;
    color: #111;
}
.content_heading h3 {
    color: #000;
}
.text_cntr {

    margin: auto;
}
.text-center.content_heading {
    margin: auto;
}
.method .cell {
    padding: 8px 0px;
    border-bottom: 2px solid #eee;
}
.propertyname, .cell {
    font-size: 14px;
}
.method .img-responsive {
    display: inline-block;
    margin: 0 11px;
    max-width: 19px;
    vertical-align: middle;
}
.method {
    width: 100%;
}
.cell {
    display: block;
    vertical-align: middle;
}
.content_heading h3 {
    color: #000;
    margin: 1rem 0 2rem 0;
}
.method {
    width: 100%;
    margin-bottom: 20px;
}
.method button {
    background-color: #05aaac;
    border: 0 none;
    color: #fff;
    font-size: 12px;
    line-height: 15px;
    padding: 3px 12px;
}
.col_offset {
    flex: 0 0 18.666667%;
    max-width: 18.666667%;
}
h3.font22.header_sec {
    margin-top: 15px;
}
.pricetab h4 {
    background-color: #1976d2;
}
.container-fluid.featureMembers-Section {
    background: #fff;
    padding: 10px;
    margin: 50px 0;
    border-radius: 15px;
}


@media only screen and (max-width: 767px)
{
    .price_pg {
        padding: 20px;
    }
    .price_pg .pricetab button {
        background: #1976d2!important;
        margin: 10px auto 20px;
    }
}
</style>
@endsection
@section('main-content')
<!-- Start Main Content ---->
<div class="maincontent price_pg">
    <div class="content bgwhite">
        <!-- Start Membership Package Tabs -->
        <div class="pricetabs pt30">
            <div class="container-fluid ">
                <h3 class="font22 header_sec"><b><img src="{{ asset('backend/images/packages.png') }}" alt="Img" title="Img" class="all_users"> Premium Plans</b></h3>
                <hr>
            </div>
            <div class="container">
                <div class="row">
                    <div class="text-center text_cntr top_intro">
                        <span>As you've noticed, AvDopt is the most convenient way to adopt Avatars in Second Life. Although it's free to use AvDopt, upgrading to a premium plan will get you access to advanced search, chat features, visibility, 24/7 support, and more. There's power in premium!</span>
                    </div>
                </div>
                <div class="row">
                    <div class="text-center text_cntr mtop20 content_heading">
                        <h3 class="font28"><b>Pricing Packages</b></h3>
                    </div>
                </div>
                <!-- 3 Tabs -->
                <div class="row">
                	@if( $plans )
                		@php
            	    		$tokenamount = getWebsiteSettingsByKey('token_amount');
            	    	@endphp
            	        @foreach($plans as $plan)
            	        @if( $plan->plan_id != 'plan_DGPRyjNYWH0Y1h' )

            	        	      <div class="col-md-4 pricetab  text-center">
                    			    @php
                    			        $class = 'pricetab_black';
                    			        $user = App\User::find(Auth::user()->id);
                    			        if( $subscription ){
                        			        if( $subscription->stripe_plan == $plan->plan_id && $user->subscribed('main') && ( !$user->subscription('main')->onGracePeriod() ) ){
                        			           $class = 'pricetab_red';
                        			        }else{
                        			            $class = 'pricetab_green';
                        			        }
                        			   }
                    			    @endphp
		                            <h4 class="{{ $class }}">
		                                <span class="font28 block">{{ $plan->name }}</span>
		                                <i>T{{ ( $plan->price ) }} / {{ $plan->billing_interval }}</i>
		                            </h4>
		                            <p>{{$plan->description}}</p>
		                            @if( $subscription )
                        			    @if( $subscription->stripe_plan == $plan->plan_id && $user->subscribed('main') && ( !$user->subscription('main')->onGracePeriod() ) )
                        			        <form class="form-horizontal " role="form" method="POST" action="{{ url('/')}}/subscription/cancel">
            	                                @csrf
            	                                <input type="hidden" name="chargeId" value="{{ $plan->plan_id }}" >
                            			        <button type="submit" onclick="return confirm('Are you sure you want to cancel this plan?')" class="mtop10 pricetab_black btnred">Cancel</button>
                            			    </form>
                        			    @else
                        			        <form class="form-horizontal " role="form" method="POST" action="{{ url('/')}}/subscription/checkout">
            	                                @csrf
            	                                <input type="hidden" name="chargeId" value="{{ $plan->plan_id }}" >
                                                <input type="hidden" name="newplanbuy" value="0" >
                            			        <button type="submit" onclick="return confirm('Are you sure you want to upgrade this plan?')" class="mtop10 pricetab_green">Upgrade</button>
                            			    </form>
                        			    @endif
            	                    @else

                                			<form class="form-horizontal " role="form" method="POST" action="{{ url('/')}}/subscription/checkout">
            	                                @csrf
            	                                <button type="submit" class="mtop10 pricetab_black">Buy</button>
                                                <input type="hidden" name="chargeId" value="{{ $plan->plan_id }}" >
            	                                <input type="hidden" name="newplanbuy" value="1" >
            	                                <div class="hidescript">
            	                                    <!--<script
            	                                        src="https://checkout.stripe.com/checkout.js" class="stripe-button"
            	                                        data-key="{{ env('STRIPE_KEY') }}"
            	                                        data-amount="{{ ( $tokenamount )? $plan->price * ( $tokenamount * 100 ): $plan->price * 100 }}"
            	                                        data-name="{{ $plan->name }}"
            	                                        data-description="Membership Charge"
            	                                        data-image="{{url('/')}}/backend/images/logo.jpg"
            	                                        data-locale="auto">
            	                                    </script>-->
            	                                </div>
            	                            </form>

    	                            @endif
		                            <!--<button type="submit" class="mtop10 pricetab_black">Upgrade</button>-->
		                        </div>

		                    @endif
            	        @endforeach
                	@endif
                </div>
                <!--End  3 Tabs -->

                <!-- 3 Table -->
                <div class="container-fluid featureMembers-Section">
                <div class="col-md-12">
                    <div class="text-center content_heading">
                        <h3 class="font28"><b>Feature Comparison</b></h3>
                    </div>
                </div>
                <div class="col-md-12 mtop30" id="featureMember-table">
                    <div class="method text-center">
                        <div class="row margin-0 list-header hidden-sm hidden-xs">
                            <div class="col-md-3 padding0 ">
                                <div class="header">Feature</div>
                                <div class="cell">
                                    <div class="propertyname">
                                        Free Tokens
                                    </div>
                                </div>
                                <div class="cell">
                                    <div class="propertyname">
                                        Private Chat
                                    </div>
                                </div>
                                <div class="cell">
                                    <div class="propertyname">
                                        Live Chat
                                    </div>
                                </div>
                                <div class="cell">
                                    <div class="propertyname">
                                        Username Change
                                    </div>
                                </div>
                                <div class="cell">
                                    <div class="propertyname">
                                        Change Profile Pic
                                    </div>
                                </div>
                                <div class="cell">
                                    <div class="propertyname">
                                        Additional Photos
                                    </div>
                                </div>
                                <div class="cell">
                                    <div class="propertyname">
                                        View My Likes
                                    </div>
                                </div>
                                <div class="cell">
                                    <div class="propertyname">
                                        View My Matches
                                    </div>
                                </div>
                                <div class="cell">
                                    <div class="propertyname">
                                        Match Quest
                                    </div>
                                </div>
                                 <div class="cell">
                                    <div class="propertyname">
                                        My Hearts
                                    </div>
                                </div>
                                 <div class="cell">
                                    <div class="propertyname">
                                        Advance Search
                                    </div>
                                </div>
                            </div>

                            @if( $plans )
            	        		@foreach($plans as $plan)
            	        			<div class="col-md-2 padding0  col_offset">
                                        <div class="header">{{ $plan->name }}</div>
                                        <div class="cell">
                                            <div class="type">
                                                {{ $plan->tokens }}
                                            </div>
                                        </div>
                                        <div class="cell">
                                            <div class="type">
                                                @php
                                                    $monthlyconnect = getWebsiteSettingsByKey( 'sub_private_messages_'.$plan->id );
                                                @endphp
                                                @if( $monthlyconnect )
                                                    <img src="{{url('/')}}/backend/images/limited.png" class="img-responsive">
                                                    @if( $monthlyconnect == -1 )
                                                        <button type="submit">Unlimited</button>
                                                    @else
                                                        <button type="submit">Limited</button>
                                                    @endif
                                                @else
                                                    <img src="{{url('/')}}/backend/images/wrong.png" class="img-responsive">
                                                @endif
                                            </div>
                                        </div>
                                        <div class="cell">
                                            <div class="type">
                                                @php
                                                    $monthlyconnect = getWebsiteSettingsByKey( 'sub_monthly_connection_'.$plan->id );
                                                @endphp
                                                @if( $monthlyconnect )
                                                    <img src="{{url('/')}}/backend/images/limited.png" class="img-responsive">
                                                    @if( $monthlyconnect == -1 )
                                                        <button type="submit">Unlimited</button>
                                                    @else
                                                        <button type="submit">Limited</button>
                                                    @endif
                                                @else
                                                    <img src="{{url('/')}}/backend/images/wrong.png" class="img-responsive">
                                                @endif
                                            </div>
                                        </div>
                                        <div class="cell">
                                            <div class="type">
                                                @php
                                                    $monthlyconnect = getWebsiteSettingsByKey( 'sub_username_change_'.$plan->id );
                                                @endphp
                                                @if( $monthlyconnect )
                                                    <img src="{{url('/')}}/backend/images/limited.png" class="img-responsive">
                                                    @if( $monthlyconnect == -1 )
                                                        <button type="submit">Unlimited</button>
                                                    @else
                                                        <button type="submit">Limited</button>
                                                    @endif
                                                @else
                                                    <img src="{{url('/')}}/backend/images/wrong.png" class="img-responsive">
                                                @endif
                                            </div>
                                        </div>
                                        <div class="cell">
                                            <div class="type">
                                                @php
                                                    $monthlyconnect = getWebsiteSettingsByKey( 'sub_user_image_change_'.$plan->id );
                                                @endphp
                                                @if( $monthlyconnect )
                                                    <img src="{{url('/')}}/backend/images/limited.png" class="img-responsive">
                                                    @if( $monthlyconnect == -1 )
                                                        <button type="submit">Unlimited</button>
                                                    @else
                                                        <button type="submit">Limited</button>
                                                    @endif
                                                @else
                                                    <img src="{{url('/')}}/backend/images/wrong.png" class="img-responsive">
                                                @endif
                                            </div>
                                        </div>
                                        <div class="cell">
                                            <div class="type">
                                                @php
                                                    $monthlyconnect = getWebsiteSettingsByKey( 'sub_max_images_upload_'.$plan->id );
                                                @endphp
                                                @if( $monthlyconnect )
                                                    <img src="{{url('/')}}/backend/images/limited.png" class="img-responsive">
                                                    @if( $monthlyconnect == -1 )
                                                        <button type="submit">Unlimited</button>
                                                    @else
                                                        <button type="submit">Limited</button>
                                                    @endif
                                                @else
                                                    <img src="{{url('/')}}/backend/images/wrong.png" class="img-responsive">
                                                @endif
                                            </div>
                                        </div>
                                        <div class="cell">
                                            @php
                                                $monthlyconnect = getWebsiteSettingsByKey( 'sub_view_my_likes_'.$plan->id );
                                            @endphp
                                            @if( $monthlyconnect )
                                                <img src="{{url('/')}}/backend/images/limited.png" class="img-responsive">
                                                @if( $monthlyconnect == -1 )
                                                    <button type="submit">Unlimited</button>
                                                @else
                                                    <button type="submit">Limited</button>
                                                @endif
                                            @else
                                                <img src="{{url('/')}}/backend/images/wrong.png" class="img-responsive">
                                            @endif
                                        </div>
                                        <div class="cell">
                                            <div class="type">
                                                @php
                                                    $monthlyconnect = getWebsiteSettingsByKey( 'sub_view_my_matches_'.$plan->id );
                                                @endphp
                                                @if( $monthlyconnect )
                                                    <img src="{{url('/')}}/backend/images/limited.png" class="img-responsive">
                                                    @if( $monthlyconnect == -1 )
                                                        <button type="submit">Unlimited</button>
                                                    @else
                                                        <button type="submit">Limited</button>
                                                    @endif
                                                @else
                                                    <img src="{{url('/')}}/backend/images/wrong.png" class="img-responsive">
                                                @endif
                                            </div>
                                         </div>
                                         <div class="cell">
                                            <div class="type">
                                                @php
                                                    $monthlyconnect = getWebsiteSettingsByKey( 'sub_match_quest_count_'.$plan->id );
                                                @endphp
                                                @if( $monthlyconnect )
                                                    <img src="{{ asset('backend/images/limited.png') }}" class="img-responsive">
                                                    @if( $monthlyconnect == -1 )
                                                        <button type="submit">Unlimited</button>
                                                    @else
                                                        <button type="submit">Limited</button>
                                                    @endif
                                                @else
                                                    <img src="{{ asset('backend/images/wrong.png') }}" class="img-responsive">
                                                @endif
                                            </div>
                                         </div>
                                         <div class="cell">
                                            <div class="type">
                                                @php
                                                    $monthlyconnect = getWebsiteSettingsByKey( 'sub_my_hearts_'.$plan->id );
                                                @endphp
                                                @if( $monthlyconnect )
                                                    <img src="{{url('/')}}/backend/images/limited.png" class="img-responsive">
                                                @else
                                                    <img src="{{url('/')}}/backend/images/wrong.png" class="img-responsive">
                                                @endif
                                            </div>
                                         </div>
                                         <div class="cell">
                                            @php
                                                $monthlyconnect = getWebsiteSettingsByKey( 'sub_advance_search_'.$plan->id );
                                            @endphp
                                            @if( $monthlyconnect )
                                                <img src="{{url('/')}}/backend/images/limited.png" class="img-responsive">
                                            @else
                                                <img src="{{url('/')}}/backend/images/wrong.png" class="img-responsive">
                                            @endif
                                        </div>
                                    </div>
		                        @endforeach
		                    @endif
                        </div>
                    </div>
                </div>

              </div>
                <!--End  3 Table -->
            </div>
        </div>
        <!-- End Match Tabs -->
    </div>
</div>
<!-- End Main Content ---->
@endsection
