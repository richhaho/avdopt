@extends('admin.layout.master')
@section('content')
<div class="row">
    <!-- Column -->
    <div class="col-lg-12 col-xlg-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                         <h3 class="inline_block font22"><b><img src="{{ asset('backend/images/questionnaries2.png') }}" alt="Img" title="Img" class="announcement">Match Quest</b>
                            <div class="pull-right">
                            <a href="{{route('questionnaires.create', $groupId )}}" class="btn btn-info ">Add Questionnaires</a> &nbsp;
                            <a href="{{route('admin.usergroup')}}" class="btn btn-info">Back To User Groups</a>
                             </div>
                        </h3>
                        <hr>
                        <div class="gender_box mtop30">
                            <div class="container-fluid">
                                @if(session()->has('message'))
                                <div class="alert alert-success">
                                    {{ session()->get('message') }}
                                </div>
                                @endif
                                <div class="form-group">
                                        <div class="row">
                                            <label for="catagory" class="col-xs-12 col-sm-4 col-md-4 col-lg-2 col-form-label text-md-right">Select User Group</label>
                                            <div class="col-md-4">
                                                <select class="form-control" id="user_group" onchange="redirectToQuestionnairePage(this.value)"
                                                        name="user_group" >
                                                    <option value="">Please Select</option>
                                                    @if( $usergroups )
                                                        @foreach( $usergroups as $row )
                                                            @if($usergroup)
                                                                @if($row->id==$usergroup->id)
                                                                    <option value="{{ $row->id }}" selected><?php echo $row->title ?></option>
                                                                @else
                                                                    <option value="{{ $row->id }}"><?php echo $row->title ?></option>
                                                                @endif
                                                            @else
                                                                <option value="{{ $row->id }}"><?php echo $row->title ?></option>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                <div class="table-responsive m-t-40">
                                    <table class="table table-striped table-bordered">
                                      <thead>
                                <tr>
                                    <th>Sr.No.</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>User Group</th>
                                    <th>Action</th>
                                </tr>
                              </thead>
                              <tbody id="sortable">
                                @if(count($questionnaires))
                                @foreach ($questionnaires as $questionnairy)
                                    <tr id="{{$questionnairy->id}}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $questionnairy->question_title }}</td>
                                        <td>@if($questionnairy->category){{ $questionnairy->category->name }}@endif</td>
                                        <td>
                                            @php
                                            $groupInfo = array();
                                            $groupids = explode(',',$questionnairy->group_id);

                                            if( $groupids ){
                                                foreach( $groupids as $groupid ){

                                                $title = App\Usergroup::find($groupid);
                                                    if($title){
                                                       $groupInfo[] = @$title->title;
                                                    }
                                                }
                                                    echo implode(', ', $groupInfo);
                                            }
                                            @endphp
                                        </td>
                                        <td>
                                           <a href="{{route('question.edit',$questionnairy->id)}}" class="btn btn-info btn-circle  border0"><i class="fa fa-edit"></i></a>
                                           <a href="{{route('question.destroy',$questionnairy->id)}}" onclick="return confirm('Are you sure you want to delete this Questionnary?');" class="btn btn-info btn-circle btn-danger border0"><i class="fa fa-trash"></i></a>
                                        </td>
                                     </tr>
                                @endforeach
                                </tbody>
                                @else
                                    <tr>
                                        <td colspan="10" class="text-center text-danger">No record found</td>
                                    </tr>
                                @endif
                                {{ $questionnaires->links() }}
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
@section('page_js')
    <script>
        var questionnaire_page_url='{{ url('admin/questionnaires') }}';
        function redirectToQuestionnairePage(value)
        {
            if(value) {
                location.href = questionnaire_page_url + '/' + value;
            }
        }
    </script>

 <script>
   $(document).ready(function(){

    $( "#sortable" ).sortable({
    update: function(event, ui) {
      var data=[];
      $("#sortable tr").each(function(){
        data.push($(this).attr("id"));
      });
      $.ajax({
          method: "POST",
          url: "{{url('ajaxrequest/sort-match-quests')}}",
          data: {
            data: data,
            action: 'action_sort_match_quests',
            _token: "{{csrf_token()}}"
          }
      })
      .done(function( msg ) {
        location.reload();
      });
    }
  });
  $( "#sortable" ).disableSelection();
});
  </script>
@endsection
