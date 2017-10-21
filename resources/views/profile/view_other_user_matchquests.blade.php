@extends('layouts.master')
@section('page_css')
<link rel="stylesheet" type="text/css" href="{{ asset('css/match_quest_edit.css') }}">
@endsection
@section('main-content')
<div class="maincontent">
    <div class="container-fluid">
        <div class="row">
            <div class="card col-md-12 all-categories">
                @if (session('success'))
                <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    {{ session('success') }}
                </div>
                @endif
                @foreach($math_quest_categories as $cat_row)
                @if($loop->index == 0)
                    <div class="col-md-12">
                            <div class="step-form">
                                <div class="f1-steps">
                                    <div class="f1-progress">
                                        <div class="f1-progress-line" data-now-value="16.66" data-number-of-steps="3" style="width: 16.66%;"></div>
                                    </div>
                                    @endif
                                    <div class="f1-step @if($loop->index == 0)current-active @endif">
                                        <div class="f1-step-icon"><i class="fa fa-check"></i></div>
                                        <p>{{ $cat_row->name }}</p>
                                    </div>
                                    @if($loop->last)
                                </div>
                            </div>
                        </div>
                        @endif
                @endforeach
                @foreach($math_quest_categories as $cat_row)
                <!--Start of  category-steps-->
                <div class="col-md-12 category-steps @if($loop->first)first @endif @if($loop->last)last @endif step_{{$loop->iteration}} @if($loop->index == 0)active @endif">
                    <div class="col-md-12 banner">
                        <img src="{{ asset($cat_row->bannerimage) }}" class="banner" alt="your image" />
                    </div>
                    <div class="col-md-12 desciption">
                        {{ $cat_row->description }}
                    </div>
                    <div class="col-md-12 questions">
                        @if($cat_row->visitedUserQuestions)
                            @foreach($cat_row->visitedUserQuestions as $question_row)
                                @if(isset($answerarray[$question_row->id]))
                                    @if($loop->index < $questions_per_category || $match_quest_unlimited)
                                        <div class="single-question">
                                        <h5>{{ $question_row->question_title }}</h5>
                                        @switch($question_row->question_type)
                                            @case(1)
                                                <p class="answer">{{ @$answerarray[$question_row->id][0] }}</p>
                                                @break
                                            @case(2)
                                            @case(5)
                                                @php
                                                    $options = json_decode($question_row->question_data);
                                                @endphp
                                                @if($options)
                                                    <p class="answer">
                                                    <ul class="answer-options">
                                                    @foreach ($options->options as $option)
                                                        @if(in_array($option, $answerarray[$question_row->id]))
                                                            <li>{{ $option }}</li>
                                                        @endif
                                                    @endforeach
                                                    </ul>
                                                    </p>
                                                @endif
                                                @break
                                            @case(3)
                                                @php
                                                    $options = json_decode($question_row->question_data);
                                                    $seloptions = @$answerarray[$question_row->id];
                                                @endphp
                                                @if($options)
                                                    <p class="answer">
                                                    <ul class="answer-options">
                                                    @foreach ($seloptions as $option_row)
                                                        <li>{{ $option_row }}</li>
                                                    @endforeach
                                                    </ul>
                                                    </p>
                                                @endif
                                                @break
                                            @default
                                                <p class="answer">{{ @$answerarray[$question_row->id][0] }}</p>
                                        @endswitch
                                        </div>
                                    @endif
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
                <!--End of  category-steps-->
                @endforeach
                <div class="col-md-12 nex-prev-btn">
                    <button type="button" class="btn btn-info prev-next prev-category" data-index="-1">Previous</button>
                    <button type="button" class="btn btn-success prev-next next-category" data-index="1">Next</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('page_level_scripts')
<script type="text/javascript">
    $(".prev-next").click(function (e) {
        if($(this).data('index') == 1){
            if(!$(".category-steps.active").hasClass('last')){
                var obj = $(".category-steps.active");
                $(".category-steps.active").removeClass('active');
                $(obj).next('.category-steps').addClass('active');
                $(".prev-category").show();
                if($(".category-steps.active").hasClass('last')){
                    $(".next-category").hide();
                }
                if(!$('.f1-step').hasClass('active')){
                    $('.f1-step:first').addClass('active');
                }else{
                    $(".f1-steps .active:last").next('.f1-step').addClass('active');
                }
                $(".f1-steps .current-active:last").removeClass('current-active');
                $(".f1-steps .active:last").next('.f1-step').addClass('current-active');
            }
        }else{
            if(!$(".category-steps.active").hasClass('first')){
                var obj = $(".category-steps.active");
                $(".category-steps.active").removeClass('active');
                $(obj).prev('.category-steps').addClass('active');
                $(".next-category").show();
                if($(".category-steps.active").hasClass('first')){
                    $(".prev-category").hide();
                }
                $(".submit-match-quest").hide();
                $(".f1-steps .active:last").removeClass('active');
                $(".f1-steps .current-active:last").prev().addClass('current-active');
                $(".f1-steps .current-active:last").removeClass('current-active');
            }
        }
    });
</script>
@endsection