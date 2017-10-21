@extends('layouts.master')

@section('main-content')
<div class="maincontent">
  <style>
  .table.table-bordered img{
    max-width: 50px;
  }
  </style>
    <div class="content bgwhite">
        <!-- Start Upgrade Membership ---->
        <div class="membership">
            <div class="container-fluid">
                    <h4 class="inline_block font22">
                        <b class="vertical_align"><img src="{{ asset('backend/images/allusers.png') }}" alt="Report" title="Img" class="all_users">CATEGORIES</b>
                    </h4>
				
				<a class="btn btnred pull-right" href="{{ url('admin/blogs') }}"><i class="fa fa-arrow-left"></i> Back</a>
					    <a href="{{route('blogs.addcategory')}}" style="margin:0 10px" class="btn btnred pull-right">Add Category</a>
						
                </div>
           <hr>
        </div>
        <!-- End Upgrade Membership ---->


        <!-- Start Message Tabs -->
        <div class="msgtabs mtop30">
            <div class="container-fluid">
                @if(session()->has('message'))
                    <div class="alert alert-success">
                        {{ session()->get('message') }}
                    </div>
                @endif
				 @if (session('warning'))
                        <div class="alert alert-warning">
                            {{ session('warning') }}
                        </div>
                    @endif
                <div class="tab-content">
                    <div id="inbox" class="tab-pane fade in active">
                        <table class="table table-bordered">
                            <th>#</th>
                            <th>Title</th>
                            <th>Action</th>
							@if(!empty($category))
                            @foreach($category as $_category)   
							 <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{$_category->category_name}}</td>
                                <td>
                                    <a href="{{ route('categoriesedit.blogs', $_category->id)}}" class="btn btn-info btn-circle"><i class="fa fa-pencil"></i></a>
                                    <a onclick="return confirm('Are you sure you want to delete?')" href="{{ route('destroycategories.blogs', $_category->id)}}" class="btn btn-info btn-circle btn-danger" title="Suspend"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                 </td>
                            </tr>   
                            @endforeach     
							@endif
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

@endsection
@section('footer')
<script type="text/javascript">

$(document).ready(function(){
    $(".warningtouser").click(function(){
        var username = $(this).attr('data-user');
        var userid = $(this).attr('data-id');
        var urlaction = 'http://avdopt-saurabhrishu.c9users.io/profile/warning/'+userid;
        $('#warninguser').text(username);
        $('#warningform').attr('action',urlaction);

    });
});

$(document).ready(function() {
  $('.table').DataTable();
} );

</script>

@endsection
