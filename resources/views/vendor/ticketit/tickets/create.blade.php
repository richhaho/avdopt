@extends('ticketit::layouts.master')
@section('page', trans('ticketit::lang.create-ticket-title'))
@section('page_title', trans('ticketit::lang.create-new-ticket'))
<style>
.userInstructions p {
    font-size: 15px;
    margin: 0;
    line-height: 22px;
    color: #000;
}
.userInstructions {
    background-color: #cdeccd;
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 5px;
}
</style>
@section('ticketit_content')
    {!! CollectiveForm::open([
                    'route'=>$setting->grab('main_route').'.store',
                    'method' => 'POST'
                    ]) !!}

        <div class="form-group row">
            <div class="col-lg-12">
                <div class="userInstructions">
                    <p>Hello <b>{{Auth::user()->name}}</b>, if you haven't found the information you're looking for in our F.A.Q or if you require further assistance, feel free to open a ticket with our lovely Support Team.</p>
                </div>
                
            </div>
        </div>
        <div class="form-group row">
            {!! CollectiveForm::label('subject', trans('ticketit::lang.subject') . trans('ticketit::lang.colon'), ['class' => 'col-lg-2 col-form-label']) !!}
            <div class="col-lg-10">
                {!! CollectiveForm::text('subject', null, ['class' => 'form-control', 'required' => 'required']) !!}
                <small class="form-text text-muted">{!! trans('ticketit::lang.create-ticket-brief-issue') !!}</small>
            </div>
        </div>
        <div class="form-group row">
            {!! CollectiveForm::label('content', trans('ticketit::lang.description') . trans('ticketit::lang.colon'), ['class' => 'col-lg-2 col-form-label']) !!}
            <div class="col-lg-10">
                {!! CollectiveForm::textarea('content', null, ['class' => 'form-control summernote-editor', 'rows' => '5', 'required' => 'required']) !!}
                <small class="form-text text-muted">{!! trans('ticketit::lang.create-ticket-describe-issue') !!}</small>
            </div>
        </div>
        <div class="form-row mt-5">
            <div class="form-group col-lg-4 row">
                {!! CollectiveForm::label('priority', trans('ticketit::lang.priority') . trans('ticketit::lang.colon'), ['class' => 'col-lg-6 col-form-label']) !!}
                <div class="col-lg-6">
                    {!! CollectiveForm::select('priority_id', $priorities, null, ['class' => 'form-control', 'required' => 'required']) !!}
                </div>
            </div>
            <div class="form-group offset-lg-1 col-lg-4 row">
                {!! CollectiveForm::label('category', trans('ticketit::lang.category') . trans('ticketit::lang.colon'), ['class' => 'col-lg-6 col-form-label']) !!}
                <div class="col-lg-6">
                    {!! CollectiveForm::select('category_id', $categories, null, ['class' => 'form-control', 'required' => 'required']) !!}
                </div>
            </div>
            {!! CollectiveForm::hidden('agent_id', 'auto') !!}
        </div>
        <br>
        <div class="form-group row">
            <div class="col-lg-10 offset-lg-2">
                {!! link_to_route($setting->grab('main_route').'.index', trans('ticketit::lang.btn-back'), null, ['class' => 'btn btn-link']) !!}
                {!! CollectiveForm::submit(trans('ticketit::lang.btn-submit'), ['class' => 'btn btn-primary']) !!}
            </div>
        </div>
    {!! CollectiveForm::close() !!}
@endsection

@section('footer')
    @include('ticketit::tickets.partials.summernote')
@append