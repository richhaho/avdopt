@extends('admin.layout.master')
@section('page_css')
<style>
   .input-group-addon {
    background: none;
    border: none;
        border-left-color: currentcolor;
        border-left-style: none;
        border-left-width: medium;
    padding-top: 0;
    padding-bottom: 0;
}
.copyOptions {
    margin: 10px 0;
    float: left;
    width: 100%;
}


.input-group-addon {
    vertical-align: top;
}
a.btn.btn-danger.removeOpions {
    margin-bottom: 29px!important;
    position: relative;
    top: 7px;
}

a.btn.btn-success.addMoreOptions {
    margin-bottom: 0;
}

.copyOptions input {
    margin-bottom: 15px!important;
}


a.btn.btn-success.addMoreOptions {
    margin-bottom: 23px!important;
}



.optionValue input{
    margin-bottom: 23px!important;
}

.form_common .form-group select {

    height: 40px!important;

    margin-top: 0!important;
	
}
	
	
.form-group {
    margin-bottom: 0;
    display: block !important;
}

.input-group {
    margin-bottom: 10px;
    margin-top: 10px;
}
</style>
@stop
@section('content')
<div class="row">
    <!-- Column -->
    <div class="col-lg-12 col-xlg-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                    <h3 class="inline_block"><b>Update Form</b>
						<a class="btn btn-info pull-right" href="{{ url('admin/jobs/forms') }}"><i class="fa fa-arrow-left"></i> Back</a>
					</h3>
					<hr>
					@if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    <form class="form_inline fullwidth mtop40" method="POST" action="{{route('formsupdate.jobs', $cateId )}}">
					   @csrf
						<div class="form-group">
							<div class="row">
								<label for="tags" class="col-md-4 col-form-label text-md-right">{{ __('Category') }}</label>
                                <div class="col-md-8">
                                    <select name="category" class="form-control">
									   <option value="">Please Select</option>
                                        @foreach($categories as $category)
										@if($category->id == $cateId)
											  <option value="{{ $category->id }}" selected> 
										  {{  $category->category_name }}</option>
										  @else
                                            <option value="{{ $category->category_name }}" {{ (collect(old('Category'))->contains($category->category_name )) ? 'selected':'' }} >{{  $category->category_name }}</option>
										@endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
						<div class="form-group fieldGroup">
							@php $mainId = 1; @endphp
							@php $uniqId = 100; @endphp
							@foreach($job as $_job)
								<div class="input-group" id="group_{{$uniqId}}">
									<div class="row">
										<div class="col-md-4">
											<input type="text" name="data[label][]" class="form-control" id="label_{{$uniqId}}" value="{{$_job->label}}" placeholder="Label name">
										</div>
										<div class="col-md-4">
											<select name="data[type][]" class="form-control" onchange="showOptions(this)" id="{{$uniqId}}">
												@foreach($allTypeInput as $_key => $_value)
													@if($_key == $_job->type)
														  <option value="{{ $_key }}" selected> 
													  {{  $_value }}</option>
													@else
														<option value="{{$_key}}">{{$_value}}</option>
													@endif
												@endforeach
											</select>
										</div>
										<div class="col-md-4">
											<input type="text" name="data[name][]" class="form-control" value="{{$_job->instruction}}" placeholder="Instruction" id="name_{{$uniqId}}"/>
										</div>
										<div class="options {{$uniqId}}">
											@if($_job->type == 'select' || $_job->type == 'radio' || $_job->type == 'checkbox')
												@php $optionData = testing($_job->id); @endphp
												@php $inputId = $removeId = strtotime(date('Y-m-d H:i:s')); @endphp
												<div class="col-md-6 optionValue" id="{{$inputId}}_main_{{$_job->name}}">
													@foreach($optionData as $_option)
														<input type="text" name="options[{{$_job->type}}][{{$_job->name}}][]" value="{{$_option->label}}" id="{{$_job->name}}_{{$inputId}}" class="form-control" placeholder=" Option value">
													@php $inputId++; @endphp
													@endforeach
												</div>
												<div class="col-md-2">
													@php $count = 1; @endphp
													@foreach($optionData as $_option)
														@if($count==1)
															<a href="javascript:void(0)" class="btn btn-success addMoreOptions" attrid="{{$removeId}}_main_{{$_job->name}}" attrtype="{{$_job->type}}" attrname="{{$_job->name}}">
																<span class="glyphicon glyphicon glyphicon-plus" aria-hidden="true"></span>
															</a>
														@else
															<a href="javascript:void(0)" class="btn btn-danger removeOpions" attrid="{{$_job->name}}_{{$removeId}}" attrtype="{{$_job->type}}">
																<span class="glyphicon glyphicon glyphicon-remove" aria-hidden="true"></span>
															</a>
														@endif
														@php $count++; @endphp
														@php $removeId++; @endphp
													@endforeach
												</div>
											@endif
										</div>
									</div>
									@if($mainId==1)
										<div class="input-group-addon"> 
											<a href="javascript:void(0)" class="btn btn-success addMore">
												<span class="glyphicon glyphicon glyphicon-plus" aria-hidden="true"></span>
											</a>
										</div>
									@else
										<div class="input-group-addon"> 
											<a href="javascript:void(0)" class="btn btn-danger remove" id="remove_{{$uniqId}}">
												<span class="glyphicon glyphicon glyphicon-remove"" aria-hidden="true"></span>
											</a>
										</div>
									@endif
									@php $mainId++; @endphp
								</div>
								@php $uniqId++; @endphp
							@endforeach
						</div>
						<input type="submit" name="submit" class="btn btn-success pull-right border_radius" value="SUBMIT"/>
					</form>

					</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page_js')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.searchdropdown').select2({
            placeholder: 'select tags',
          multiple: true
        });
    });

</script>
<script type="text/javascript">
$(document).ready(function(){
    //group add limit
    var maxGroup = 10;
    
    //add more fields group
    $(".addMore").click(function(){
		if($('body').find('.fieldGroup').length < maxGroup){
			var randomId = makeid(8);
			var htmlFrom = '<div class="form-group" id="group_'+randomId+'"><div class="input-group"><div class="row"><div class="col-md-4"><input type="text" name="data[label][]" id="label_'+randomId+'" class="form-control" placeholder="Label name"></div><div class="col-md-4"><select name="data[type][]" id="'+randomId+'" onchange="showOptions(this)" class="form-control">@foreach($allTypeInput as $_key => $_value)<option value="{{$_key}}">{{$_value}}</option>@endforeach</select></div><div class="col-md-4"><input type="text" name="data[name][]" id="name_'+randomId+'" class="form-control" placeholder="Instruction"/></div><div class="options '+randomId+'" ></div></div><div class="input-group-addon"> <a href="javascript:void(0)" class="btn btn-danger remove" id="remove_'+randomId+'"><span class="glyphicon glyphicon glyphicon-remove" aria-hidden="true"></span> </a></div></div></div>';
			
            $('body').find('.fieldGroup').append(htmlFrom);
        }else{
            alert('Maximum '+maxGroup+' groups are allowed.');
        }
    });
        
    //remove fields group
    $("body").on("click",".remove",function(){ 
        var removeId = $(this).attr("id");
		var idsArr = removeId.split('_');
		console.log(idsArr);
		jQuery('#group_'+idsArr[1]).remove();
    });
	
	$("body").on("click",".addMoreOptions",function(){ 
        var addMoreId =  jQuery(this).attr('attrid');
        var type =  jQuery(this).attr('attrType');
        var attrName =  jQuery(this).attr('attrName');
		var removeId = makeid(6);
		jQuery('#'+addMoreId).append('<input type="text" name="options['+type+']['+attrName+'][]" class="form-control" placeholder="Option value" id="'+removeId+'">');
		console.log(addMoreId);
		jQuery(this).parent().append('<a href="javascript:void(0)" class="btn btn-danger removeOpions" attrid="'+removeId+'" attrtype="select"><span class="glyphicon glyphicon glyphicon-remove" aria-hidden="true"></span></a>');
    });
	
	$("body").on("click",".removeOpions",function(){ 
		var rmvId =  jQuery(this).attr('attrid');
		jQuery(this).remove();
		jQuery('#'+rmvId).remove();
	});
});
function showOptions(data){
	var type = data.value;
	var id = data.id;
	console.log(id);
	if(type=='select' || type=='radio' || type=='checkbox' || type=='multipleselect' ){
		var randomId = makeid(5);
		var nameType = document.getElementById('label_'+id).value;
		// if(nameType==''){
			// alert('Please add name first');
			// document.getElementById(id).value = '';
			// return false;
		// }
		console.log(nameType);
		var optionHtml = '<div class="copyOptions"><div class="col-md-6 optionValue" id="'+randomId+'"><input type="text" name="options['+type+']['+nameType+'][]" class="form-control" placeholder=" Option value"/></div><div class="col-md-2"><a href="javascript:void(0)" class="btn btn-success addMoreOptions" attrId="'+randomId+'" attrType="'+type+'" attrName="'+nameType+'"><span class="glyphicon glyphicon glyphicon-plus" aria-hidden="true"></span></a></div></div>';
		$('body').find('.'+id).append(optionHtml);
	}
	if(type=='text' || type=='password' || type=='textarea' || type=='file' || type=='number'){
		$('body').find('.'+id).html('');
	}
}

function makeid(length) {
   var result           = '';
   var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
   var charactersLength = characters.length;
   for ( var i = 0; i < length; i++ ) {
      result += characters.charAt(Math.floor(Math.random() * charactersLength));
   }
   return result;
}
</script>
</script>
@endsection