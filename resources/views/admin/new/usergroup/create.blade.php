@extends('admin.New.layout.master')
@section('content')

<div class="row">
    <!-- Column -->
    <div class="col-lg-12 col-xlg-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <h3><i class="fa fa-envelope"></i> User Group</h3>
                        <hr>
                        <form class="form-horizontal form_common" role="form" method="POST" action="{{route('usergroup.store')}}">
                            {{ csrf_field() }}
                            <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                                <div class="row">
                                    <label for="title" class="col-md-3 col-form-label text-md-right">Title</label>
                                    <div class="col-md-9">
                                        <input id="title"  type="text" class="form-control" name="title" value="{{ old('title') }}" required autofocus>
                                    </div>
                                </div>
                            </div>
                            @if ($errors->has('title'))
                            <span class="invalid-feedback">
                            <strong>{{ $errors->first('title') }}</strong>
                            </span>
                            @endif
                            <div class="form-group{{ $errors->has('minage') ? ' has-error' : '' }}">
                                <div class="row">
                                    <label for="agegroup" class="col-md-3 control-label text-md-right">Age(Min.)</label>
                                    <div class="col-md-9">
                                        <input id="minage"  type="number" class="form-control" name="minage" value="{{ old('minage') }}" required autofocus>
                                    </div>
                                </div>
                            </div>
                            @if ($errors->has('minage'))
                            <span class="invalid-feedback">
                            <strong>{{ $errors->first('minage') }}</strong>
                            </span>
                            @endif
                            <div class="form-group{{ $errors->has('maxage') ? ' has-error' : '' }}">
                                <div class="row">
                                    <label for="maxage" class="col-md-3 control-label text-md-right">Age(Max.)</label>
                                    <div class="col-md-9">
                                        <input id="maxage"  type="number" class="form-control" name="maxage" value="{{ old('maxage') }}" required autofocus>
                                    </div>
                                </div>
                            </div>
                            @if ($errors->has('maxage'))
                            <span class="invalid-feedback">
                            <strong>{{ $errors->first('maxage') }}</strong>
                            </span>
                            @endif
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <div class="row">
                                    <label for="role" class="col-md-3 control-label text-md-right">Gender role</label>
                                    <div class="col-md-9">
                                        <select class="form-control searchdropdown" id="role" name="gender[]" multiple>
                                            @foreach($genderrole as $row)
                                            <option value="{{ $row->id }}"><?php echo $row->title ?></option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @if ($errors->has('gender'))
                            <span class="invalid-feedback">
                            <strong>{{ $errors->first('gender') }}</strong>
                            </span>
                            @endif
                            <div class="form-group{{ $errors->has('plans') ? ' has-error' : '' }}">
                                <div class="row">
                                    <label for="plans" class="col-md-3 control-label text-md-right">Membership Plans</label>
                                    <div class="col-md-9">
                                        <select class="form-control searchdropdown" id="plans" name="plans[]" multiple>
                                            @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}"><?php echo $plan->name ?></option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('tags') ? ' has-error' : '' }}">
                                <div class="row">
                                    <label for="tags" class="col-md-3 control-label text-md-right">Group Tag</label>
                                    <div class="col-md-9">
                                        <select name="tags" class="form-control">
                                            @foreach($usergroupstags as $usergroupstag)
                                            <option value="{{ $usergroupstag->id }}">{{ $usergroupstag->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @if ($errors->has('tags'))
                            <span class="invalid-feedback">
                            <strong>{{ $errors->first('tags') }}</strong>
                            </span>
                            @endif
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12"><button type="submit" class="btn btn-success pull-right border_radius"><i class="fa fa-check"></i> Submit</button></div>
                                </div>
                            </div>
                        </form>
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
<script>
    $(document).ready(function() {
        $('.searchdropdown').select2({
            placeholder: 'select',
          multiple: true
        });
    });
</script>
@endsection