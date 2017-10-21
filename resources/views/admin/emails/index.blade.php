@extends('admin.layout.master')
@section('content')
<div class="row">
    <!-- Column -->
    <div class="col-lg-12 col-xlg-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <h3 class="inline_block font22"><b><img src="{{ asset('backend/images/taguser.png') }}" alt="Img" title="Img" class="announcement"> Emails</b></h3> 
                        <hr>
                        <div class="gender_box mtop30">
                            <div class="container-fluid">
                                @if(session()->has('message'))
                                    <div class="alert alert-success">
                                        {{ session()->get('message') }}
                                    </div>
                                @endif
                                <div class="table-responsive m-t-40">
                                    <table class="table table-bordered">
                                    <tr>
                                        <th>#</th>
                                        <th>Email</th>
                                        <th>Action</th>
                                    </tr>
                                        @foreach($emails as $email)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ @$email->subject }}</td>
                                                <td>
                                                    <a href="{{ route('edit.email.notify', $email->id) }}" class="btn btn-info btn-circle"><i class="fa fa-pencil"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach   
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
