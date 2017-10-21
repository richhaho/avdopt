@extends('layouts.master')

@section('main-content')
<div class="maincontent">
  <style>
  .table.table-bordered img{
    max-width: 50px;
  }
  img.blog_img.img-circle {
    width: 40px;
    height: 40px;
}
  </style>
    <div class="content bgwhite">
        <!-- Start Upgrade Membership ---->
        <div class="membership">
            <div class="container-fluid">
                    <h4 class="inline_block font22">
                        <b class="vertical_align"><img src="{{ asset('backend/images/allusers.png') }}" alt="Report" title="Img" class="all_users">BLOGS</b>
                    </h4>
					<a href="{{route('blogs.categories')}}" style="margin:0 10px" class="btn btnred pull-right">Categories </a>
					    <a href="{{route('blogs.create')}}" style="margin:0 10px" class="btn btnred pull-right">Add Blogs</a>
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
                <div class="tab-content">
                    <div id="inbox" class="tab-pane fade in active">
                        <table class="table table-striped table-bordered" style="width:100%">
                          <thead>
                            <tr>
                              <th>#</th>
                       
                              <th>Title</th>
                              <th>Descripton</th>
							         <th>Images</th>
                              <th>Action</th>
                            </tr>
                          </thead>
						  
                          <tbody>
						  @if(!empty($blog))
							@foreach($blog as $_blog)
						
							  
						
							
						<tr>
							<td>{{ $loop->iteration }}</td>
							<td>{{ $_blog->title }}</td>
							<td>{{ substr($_blog->description ,0,100)}} </td>
							<td>@foreach(json_decode($_blog->image) as $img)
						<img  class="blog_img img-circle" src="{{ asset('/uploads/'.$img)}}" title="blog">
							
							  @endforeach
							</td>
							<td>
							<a href="{{ route('blog.edit', $_blog->id)}}" class="btn btn-info btn-circle"><i class="fa fa-pencil"></i></a>
							<a onclick="return confirm('Are you sure you want to delete job?')" href="{{ route('blog.delete', $_blog->id)}}" class="btn btn-info btn-circle btn-danger" title="Suspend"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
							</td>
						</tr> 
					@endforeach
					@endif							
                          </tbody>
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
