@extends('layouts.master')

@section('page_level_styles')
<link rel="stylesheet" type="text/css" href="{{ asset('frontendnew/css/userads_style.css') }}">
@yield('page_level_styles')

@section('main-content')
 <div class="container-fluid page-titles">
    <div class="row">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor"><i class="fa fa-life-bouy"></i> Advertisement Packages</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                <li class="breadcrumb-item active">Advertisement Packages</li>
            </ol>
        </div>
    </div>
</div>
<!-- Start Main Content ---->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
        	<div class="card1">
        		<div class="card-body">                    

                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <h2 class="pkgttl">Advertisement Packages</h2>
                        </div>
                    </div>
        			
                    <div class="row">
                        <div class="col-md-12">
                            @if(session()->has('success'))
                                <div class="mt10">
                                    <div class="alert alert-success">
                                        {{ session()->get('success') }}
                                    </div>
                                </div>
                                @endif

                                @if(session()->has('error'))
                                <div class="mt10">
                                    <div class="alert alert-error">
                                        {{ session()->get('error') }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mtb10">
                        @foreach($advertisement as $key=>$value)                            
                        <div class="col-xs-12 col-sm-6 col-md-4">
                            <div class="single_ads">
                                <h4>
                                    <span class="pckttlsec">{{ $value->title }}</span>
                                    <span class="pckamntsec">T{{ $value->total_amt }}/ {{ $value->banner_plan }}</span>
                                </h4>
                                <div class="single_ads_inner">
                                    <div class="sec_hgt">                                        
                                        <div class="adsdesc">
                                            <p>{!! $value->description !!}</p>
                                        </div> 
                                        <h5 class="ttl_sec">Banner List</h5>
                                        @if($value->banners_list)
                                           <ul class="usrbanner_lists">
                                                @foreach($value->banners_list as $key2=>$value2) 
                                                    <li>
                                                    @foreach($value2 as $key3=>$value3)
                                                        <span class="banerprice_ttl">
                                                            @if($key3 == 'banner_width')
                                                                {{ $value3 }}
                                                            @endif
                                                            
                                                            @if($key3 == 'banner_height')
                                                                X {{ $value3 }}
                                                            @endif
                                                        </span>
                                                        @if($key3 == 'page_location')
                                                            <span class="pagesec">page - {{$value3}}</span>
                                                        @endif
                                                    @endforeach
                                                </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    
                                        <h5 class="ttl_sec">Target Audience</h5>
                                        @if($value->target_audiences)
                                            <ul class="taraud_lists">
                                            @foreach($value->target_audiences as $key2=>$value2)
                                                <li>
                                                    <p>
                                                        <span class="target_aud_sec">Group {{ $key2+1}} - {{ $value2['usergroup_names'] }}</span>
                                                        <!-- <span class="target_aud_price">Group price - {{ $value2['price'] }}</span> -->
                                                    </p>
                                                </li>
                                            @endforeach    
                                            </ul> 
                                        @endif                                        
                                    </div>
                                    <div class="paybtn_sec"> 
                                        @if($value->paid == 0)
                                            <a href="{{ route('checkoutads.manageads', $value->id)}}" class="btn btn-info">Buy</a>
                                        @elseif( $value->paid == 1)
                                            <button  class="btn btn-disabled upgraded_ads" disabled >Paid</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

        		</div>
        	</div>
        </div>            
    </div>
</div>
<!-- End Main Content ---->
@endsection