@extends('layouts.master')

@section('page_level_styles')
<link rel="stylesheet" type="text/css" href="{{ asset('frontendnew/css/userads_style.css') }}">
@yield('page_level_styles')

@section('main-content')
 <div class="container-fluid page-titles">
    <div class="row">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor"><i class="fa fa-life-bouy"></i> My Adevertisements</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                <li class="breadcrumb-item active">My Adevertisements</li>
            </ol>
        </div>
    </div>
</div>
<!-- Start Main Content ---->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
        	<div class="card">
        		<div class="card-body">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <!-- <a href="{{ route('createuseradvertise.manageads') }}" class="btn btn-info pull-right">Create Advertisement</a> -->
                            <a href="{{ route('adspackages.manageads') }}" class="btn btn-info pull-right">Visit Advertisement Packages</a>
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
                            <div class="single_ads_manageads">
                                <h4>
                                    @if($value->adevertisementlist)
                                    <span class="pckttlsec">{{ $value['adevertisementlist']['title'] }}</span>
                                    <span class="pckamntsec">T{{ $value['adevertisementlist']['total_amt'] }}/ {{ $value['adevertisementlist']['banner_plan'] }}</span>
                                    @endif
                                </h4>
                                <div class="single_ads_inner">
                                    <div class="sec_hgt_manageads"> 
                                        <h5 class="ttl_sec">Ads Status</h5>
                                        <p>current status - <span class="boldfont">{{ $value->status }}</span></p>
                                        <p>Ads started time - <span class="addstime">@if($value->start_at != ''){{ $value->start_at }} @else Not started @endif</span></p>
                                        <p>Ads ends time - <span class="addetime">@if($value->end_date == '') Not started @else {{ $value->end_date }} @endif</span></p>
                                        <p>Paid Amount - <span class="paidamnt boldfont">@if($value->paid == 1){{ $value['adevertisementlist']['total_amt'] }}Token @else Not paid @endif</span></p>                                      
                                    </div>
                                    
                                    <div class="paybtn_sec">
                                        @if(count($value->userbanners) == 0)
                                            <a href="{{ route('uploadbanners.manageads', $value->id) }}" class="btn btn-info">Upload Banners Images</a>
                                        @else
                                            <a href="{{ route('editbanners.manageads', $value->id) }}" class="btn btn-info">Edit Banners Images</a>
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