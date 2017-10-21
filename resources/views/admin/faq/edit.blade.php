@extends('layouts.master')
@section('main-content')
<div class="maincontent">
	<div class="content bgwhite">
		<div class="row justify-content-center">
			<div class="col-md-12">
				<div class="padding40">
					<div class="card-header">
						<h3 class="inline_block"><b>Update FAQ</b></h3>
						<a class="btn btn-primary pull-right" href="{{ route('faq.index') }}"><i class="fa fa-arrow-left"></i> Back</a>
					</div>
					<hr>
					@if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
					<form class="form_inline fullwidth mtop40" method="POST" action="{{route('faq.update', $faq->id)}}">
					    @csrf
						<div class="form-group">
                           <div class="row">
                            <label for="question" class="col-md-4 col-form-label text-md-right">{{ __('Question') }}</label>

                            <div class="col-md-8">
								<textarea class="form-control" name="question" rows="3" cols="80" id="question">{{ $faq->question }}</textarea>
								@if ($errors->has('question'))
								<span class="invalid-feedback">
									<strong>{{ $errors->first('question') }}</strong>
								</span>
								@endif
                            </div>
                            </div>
							<hr>
							<div class="row">
                             <label for="answer" class="col-md-4 col-form-label text-md-right">{{ __('Answer') }}</label>

                             <div class="col-md-8">
                                 <textarea class="form-control" name="answer" rows="8" cols="80" id="answer">{{ $faq->answer }}</textarea>
                                 @if ($errors->has('answer'))
                                 <span class="invalid-feedback">
                                     <strong>{{ $errors->first('answer') }}</strong>
                                 </span>
                                 @endif
                             </div>
                             </div>
                        </div>

						<div class="form-group">
						    <div class="row">
							<div class="col-md-3"></div>
							<div class="col-md-9"><button type="submit" class="btnpad btnred pull-right border_radius">Submit</button></div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
