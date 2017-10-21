@extends('layouts.master')

@section('page_level_styles')
<link rel="stylesheet" type="text/css" href="{{ asset('frontendnew/css/userads_style.css') }}">
<link href="{{ URL::asset('new-assets/common/plugins/bootstrap-fileinput/bootstrap-fileinput.css')}}" rel="stylesheet" type="text/css" />
@yield('page_level_styles')

@section('main-content')
 <div class="container-fluid page-titles">
    <div class="row">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor"><i class="fa fa-life-bouy"></i> Upload Banners</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                <li class="breadcrumb-item active">Upload Banners</li>
            </ol>
        </div>
    </div>
</div>
<!-- Start Main Content ---->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
        	<div class="card">
        		<div class="card-body">
                    <div class="row mtb10">
                        <div class="col-md-12 text-center">
                            <h4 class="formttl">Upload Banner for Advertisement</h4>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('saveuserbanners.manageads') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="ads_id" id="ads_id" value="{{ $ads_id }}">
                        @foreach($banners_list as $banner)
                            <input type="hidden" name="mybanners[]" value="{{ $banner->id }}">
                            <div class="row">
                                <div class="col-md-12">
                                    <h5 class="banner_lbl">Upload banner images for size - {{ $banner->banner_width }}X{{ $banner->banner_height }}</h5>
                                </div>
                            </div>

                            <div class="row mb10">
                                <div class="col-md-3">
                                    <label>Banner Image:</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="file" name="banner_image[]" class="form-control" accept="image/jpeg, image/png" required>
                                </div>
                            </div>

                            <div class="row mb10">
                                <div class="col-md-3">
                                    <label>Banner Url:</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" name="banner_url[]" class="form-control">
                                </div>
                            </div>
                        @endforeach

                        <div class="row mb10">
                            <div class="col-md-3">
                                <label class="labeld">Select Targetaudience:</label>
                            </div>

                            <div class="col-md-9">
                                <select name="mytargetaudience[]" class="searchtargetaudience" id="mytargetaudience" multiple>
                                    @foreach($target_audiences as $value)
                                        <option value="{{ $value->id }}">{{ $value->usergroup_names }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row pt10">
                            <div class="col-md-3"></div>
                            <div class="col-md-9">
                                <button type="submit" class="btn btn-success border_radius">Submit</button>
                            </div>
                        </div>    
                    </form>
                    
        			
        		</div>
        	</div>
        </div>
        <div class="col-md-1"></div>

    </div>
</div>
<!-- End Main Content ---->
@endsection
@section('footer')
<script src="{{ URL::asset('new-assets/common/plugins/bootstrap-fileinput/bootstrap-fileinput.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('new-assets/common/plugins/select2/js/select2.min.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $(".searchtargetaudience").select2({
            placeholder: "Select a Target Audience",
            allowClear: true
        });
    });
</script>
@endsection