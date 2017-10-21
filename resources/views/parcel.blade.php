
@extends('layouts.master')
@section('htmlheader')

    <link rel="stylesheet" href="{{ asset('css/stripe.css') }}" type="text/css" />
    <script src="https://js.stripe.com/v3/"></script>
	 <style>
	 .table_sec {
    padding-top: 10px;
    margin-top: 1%;
}
table.table.color-table.info-table.table_sec td {
    text-align: center;
}
table.table.color-table.info-table.table_sec th {
    text-align: center;
}
.lft_sec {
    background: #1976d2;
    padding: 5px;
}
p.txt_sec {
    color: #000;
    
}
b.token_sec {
    /* font-weight: bold; */
    color: #000;
}
.form-row {
    
    padding: 0 98px !important;
}
.lft_sec h1 {
    color: #fff;
    font-size: 26px;
    padding-left: 2rem;
    text-transform: capitalize;
	margin:0;
}
.para_sec h1 {
    font-size: 18px;
    color: #1976d2;
    text-transform: capitalize;
}
.rgt_sec img {
    width: 100%;
	height:100%
}
button.btn.btnred {
    background: #26dad2;
    color: #fff;
}
a.btn.btnred,a.btn.btnred:hover {
    background: #26dad2;
    color: #fff;
}
.btn_bt {
    text-align: center;
}
.para_bt {
    text-align: center;
    margin-top: 1rem;
}
.card {
    height: 100%;
    min-height: 220px;
}
.middle_content {
    text-align: center;
    /* padding: 1px; */
    padding-top: 2rem;
    font-weight: 600;
    font-style: italic;
}
p.txt_sec {
    color: #000;
    text-transform: capitalize;
}
.text_sec {
    float: right;
    /* margin-bottom: 0; */
    /* margin-top: 0; */
    position: relative;
    top: 30px;
}
	</style>
@endsection
@section('main-content')
    <div class="maincontent">
        <div class="content bgwhite">
            <div class="membership" style="margin-top: 2rem;">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="lft_sec">
                                    <h1>AvDopt Terimals</h1>
                                </div>
                                <div class="card-body">
                                    <div class="para_sec">
                                        <p class="txt_sec">register or make payments to out terminals in-world. There are many AvDopt terminal on the
                                            gird . explore beautiful locations in second life , all white using our terminals</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="lft_sec">
                                    <h1>Host A Terimals</h1>
                                </div>
                                <div class="card-body">
                                    <div class="para_sec">
                                        <p class="txt_sec">need a terminal? simply click the request button below and you'll be directed to the right
                                            place to pickup a terminal. AvDopt terminals are free and you may earn a commission for simply hosting one
                                            on your land. </p>
                                    </div>
                                    <button class="btn btnred text_sec">Host Terminal</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="middle_content">
                        <p class="txt_sec">host a AvDopt terminal at your in-world location and get listed below! <br>
                            It's free to host us & we don't cause lag</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card ">
                            <div class="container">
                                <div class="row">
                                    <div class="table-responsive">
                                        <table class="table color-table info-table table_sec ">
                                            <thead>
                                            <tr>
                                                <th>RANK</th>
                                                <th>PARCEL NAME</th>
                                                <th>LOCATION</th>

                                            </tr>
                                            </thead>
                                            <tbody class="parcels_tbody">
                                            <?php $i=1; ?>
                                            @if(count($parcels))
                                                @foreach($parcels as $parcel)
                                                    <tr class="{{($i%2==0)?'footable-odd':'footable-even'}}">
                                                        <td>#{{$i++}}</td>
                                                        <td>{{$parcel->parcel_name}}</td>
                                                        <td>
                                                            <a target="_blank" class="btn btnred " href="{{ $parcel->sl_url }}"  >Visit location</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="10" class="text-center text-danger">We are updating records, please check in a few minutes.</td>
                                                </tr>
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection    
@section('footer')
<!--<script type="text/javascript" src="{{ asset('js/stripe.js') }}"></script>-->
    <script>

        function getLatestParcelData()
        {

            jQuery.ajax({
                url: window.Laravel.url + '/ajaxrequest/parcels',
                type: 'POST',
                data: {'_token': window.Laravel.csrfToken},
                dataType: 'JSON',
                success: function (response) {
                    var html=''+
                            '<tr>'+
                            '<td colspan="10" class="text-center text-danger">We are updating records, please check in a few minutes.</td>'+
                            '</tr>';

                    if(response.success)
                    {
                        if(response.info.parcels.length>0){
                            html='';
                            for(var i=0;i<response.info.parcels.length;i++){
                                var parcel=response.info.parcels[i];

                                html+=''+
                                    '<tr class="'+((i%2==0)?'footable-even':'footable-odd')+'">'+
                                    '   <td>#'+(i+1)+'</td>'+
                                    '   <td>'+parcel.parcel_name+'</td>'+
                                    '   <td>'+
                                    '       <a target="_blank" class="btn btnred " href="'+parcel.sl_url+'"  >Visit location</a>'+
                                    '   </td>'+
                                    '</tr>';
                            }
                        }
                    }

                    jQuery('.parcels_tbody').html(html);

                },
                error: function(xhr, textStatus, errorThrown) {

                },
                complete: function(xhr, textStatus) {

                    setTimeout(function() {
                                getLatestParcelData()
                            }
                            ,30000
                    );
                }
            });

        }

        jQuery(document).ready(function(){
            getLatestParcelData();
        })

    </script>
@endsection        