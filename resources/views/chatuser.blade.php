@extends('layouts.master')
@section('htmlheader')
<style>
    .chat_notify {
    position: absolute;
    left: 20px;
    top: 13px;
    }
    .alert.alert-info {
    top: 0px;
    }
    .chat_list h3 {
    float: left;
    margin-top: 10px;
    margin-left: 6px;
    }
</style>

@endsection
@section('main-content')

<!-- ============================================================== -->
<div class="container-fluid appposi">
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="row">
        <div class="col-12">
            <div class="card">
              <div class="sucessmessage"></div>
                <div class="card-body">
                        @if ( isthisSubscribed() || getmanualfeatures('token_monthly_connection_'))
                        <div class="mtop30">
                            <chat-users v-on:fetchusers="fetchMessages" :chatnewusers="chatnewusers" :chatusers="chatusers" :chatstaffadmins="chatstaffadmins"></chat-users>
                            <chat-messages v-on:messagesent="addMessage" v-on:fetchusers="fetchMessages" :user="{{ Auth::user() }}" :messages="messages"></chat-messages>
                        </div>
                        @else
                        @include('includes.debitTokens', ['featurevalue'=>'token_monthly_connection_value_','featureclass'=>'chat','featurename'=>'token_monthly_connection_', 'featureMessage'=>'Hey '. ucfirst( Auth::user()->name ) .'!. Upgrade your membership today to experience unlimited chat.'])
                        @endif
                    </div>
                <!-- ============================================================== -->
                <!-- End PAge Content -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Right sidebar -->
                <!-- ============================================================== -->
                <!-- .right-sidebar -->
            </div>
        </div>
    </div>
</div>
</div>

@endsection
