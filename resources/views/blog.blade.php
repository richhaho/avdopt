@extends('v7.frontend')
@section('page_level_styles')
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
@yield('head')
<style>


.thumbnail {
	border: none;
	position: relative;
	background-color: #000000;
	padding:0;
}

.thumbnail a>img,
.thumbnail>img {
	margin-right: auto;
	margin-left: auto;
	width: 100%;
	opacity:0.5;
	height: 240px;
}

.img img {
    width: 100%;
    height: 266px;
}

.heads_sec h1 {
	font-size: 25px;
	margin: 0;
	text-transform: capitalize;
	color: #3c3a3a;
	font-weight: bold;
}

.heads_sec {
	margin-bottom: 3rem;
}

.heads_sec::before {
    position: absolute;
    content: "";
    background: #e36940;
    width: 81px;
    height: 4px;
    bottom: 78%;
}

.para_sec p {
	font-size: 15px;
	color: #7d7aa3;
}

.spanner_sec span {
	font-size: 16px;
	color: #7d7a7a;
}

.parag_sec {
	margin: 1rem 0;
}

.parag_sec p {
	color: #7d7a7a;
}

.parag_sec a {
    padding: 7px 20px;
    border-radius: 50px;
    border: none;
    font-size: 15px;
    font-weight: bold;
    background-image: linear-gradient(to right, #ff2f00 , #ffc46d);
}

.para_sec p img {
	width: 100%;
	max-width: 28px;
	padding-right: 5px;
}

.social_sec i {
	color: grey;
	padding: 0px 7px;
}

.fl_sec {
	background: #fdfbf6;
}

.mid_bit {
	background: #fff;
	border-radius: 4px;
	-webkit-box-shadow: 0px 5px 20px rgba(0, 0, 0, 0.05);
	box-shadow: 0px 5px 20px rgba(0, 0, 0, 0.05);
	margin-bottom: 2rem;
}

.rgt {
    padding: 2rem 1rem;
}

.categ_sec {
	text-align: center;
}

.categ_sec h1 {
    font-size: 27px;
    border: 2px solid #e36940;
    width: 44%;
    margin: auto;
    font-weight: bold;
    padding: 3px 5px;
    color: #000;
}
.categ_sec ul li a {
    color: #000;
}

.categ_sec {
    text-align: center;
    background: #fff;
    border-radius: 4px;
    -webkit-box-shadow: 0px 5px 20px rgba(0, 0, 0, 0.05);
    box-shadow: 0px 5px 20px rgba(0, 0, 0, 0.05);
    margin-bottom: 2rem;
    height: auto;
    padding: 1rem;
}

.bxs_sec ul li {
	text-align: center;
	padding: 2rem 0;
	background: #f00;
	color: #fff;
	list-style-type: none;
	margin-bottom: 1rem;
	font-weight: bold;
	font-size: 49px;
}

.ad1 {
	background: #e6e63d !important;
}

.ad2 {
	background: #66dfea !important;
}

.ad3 {
	background: #6688ea !important;
}

.bxs_sec ul {
	margin: 0;
	padding: 0;
}

.bxs_sec ul p {
    text-align: center;
    font-size: 25px;
    color: #337ab7;
    font-weight: 600;
    font-style: italic;
    padding: 1rem;
}

.text {
	position: absolute;
	z-index: 999;
	margin: 0 auto;
	left: 0;
	right: 0;
	top: 50%;
	text-align: center;
	width: 80%;
}
.reapeter{
    display:none;
}
.text p {
	font-size: 25px;
	color: #fff;
}
.tp_hd h1 {
    font-size: 23px;
    border: 2px solid #e36940;
    width: 10%;
    font-weight: bold;
    padding: 5px;
    text-align: center;
}
.tp_hd {
    border-bottom: 2px solid #e36940;
    margin-bottom: 2rem;
}

.btm_btn a {
    background: linear-gradient(to right, #ff2f00 , #ffc46d);
    color: #fff;
    font-size: 22px;
    border-radius: 0;
    text-align: center;
    padding: 6px 30px;
    border: none;
}
.btm_btn {
	text-align: center;
}
.btm_btn {
	text-align: center;
	margin-bottom: 2rem;
}
.categ_sec ul li {
    width: 44%;
    margin: auto;
    text-align: left;
    margin-top: 5px;
}
.carousel-indicators li {

    border: 1px solid #000!important;

}.carousel-indicators {

    bottom: -15px!important;

}
.carousel-indicators .active {

    background-color: #000;
}
</style>
@stop
@section('content')
<div class="fl_sec">
         <div class="container-fluid">
		 <div class="tp_hd">
		 <h1>New Post</h1>
		 </div>
            <div class="tp_sec">
               <div class="row">
                  <div class="col-md-12">
                     <div id="Carousel" class="carousel slide">
                        <ol class="carousel-indicators">
                           <li data-target="#Carousel" data-slide-to="0" class="active"></li>
                           <li data-target="#Carousel" data-slide-to="1"></li>
                           <li data-target="#Carousel" data-slide-to="2"></li>
                        </ol>
                        <!-- Carousel items -->
                        <div class="carousel-inner">
                           <div class="item active">
                              <div class="row">
                                 <div class="col-md-3"><a href="#" class="thumbnail"><img src="{{ asset('frontend/images/5.jpg') }}"> <div class="text"><p>Lorem Ipsum is simply dummy text </p></div></a></div>
                                 <div class="col-md-3"><a href="#" class="thumbnail"><img src="{{ asset('frontend/images/5.jpg') }}"><div class="text"><p>Lorem Ipsum is simply dummy text </p></div></a></div>
                                 <div class="col-md-3"><a href="#" class="thumbnail"><img src="{{ asset('frontend/images/5.jpg') }}"><div class="text"><p>Lorem Ipsum is simply dummy text </p></div></a></div>
                                 <div class="col-md-3"><a href="#" class="thumbnail"><img src="{{ asset('frontend/images/5.jpg') }}"><div class="text"><p>Lorem Ipsum is simply dummy text </p></div></a></div>
                              </div>
                              <!--.row-->
                           </div>
                           <!--.item-->
                           <div class="item">
                              <div class="row">
                                 <div class="col-md-3"><a href="#" class="thumbnail"><img src="{{ asset('frontend/images/5.jpg') }}"><div class="text"><p>Lorem Ipsum is simply dummy text </p></div></a></div>
                                 <div class="col-md-3"><a href="#" class="thumbnail"><img src="{{ asset('frontend/images/5.jpg') }}"><div class="text"><p>Lorem Ipsum is simply dummy text </p></div></a></div>
                                 <div class="col-md-3"><a href="#" class="thumbnail"><img src="{{ asset('frontend/images/5.jpg') }}"><div class="text"><p>Lorem Ipsum is simply dummy text </p></div></a></div>
                                 <div class="col-md-3"><a href="#" class="thumbnail"><img src="{{ asset('frontend/images/5.jpg') }}"><div class="text"><p>Lorem Ipsum is simply dummy text </p></div></a></div>
                              </div>
                              <!--.row-->
                           </div>
                           <!--.item-->
                           <div class="item">
                              <div class="row">
                                 <div class="col-md-3"><a href="#" class="thumbnail"><img src="{{ asset('frontend/images/5.jpg') }}"><div class="text"><p>Lorem Ipsum is simply dummy text </p></div></a></div>
                                 <div class="col-md-3"><a href="#" class="thumbnail"><img src="{{ asset('frontend/images/5.jpg') }}"><div class="text"><p>Lorem Ipsum is simply dummy text </p></div></a></div>
                                 <div class="col-md-3"><a href="#" class="thumbnail"><img src="{{ asset('frontend/images/5.jpg') }}"><div class="text"><p>Lorem Ipsum is simply dummy text </p></div></a></div>
                                 <div class="col-md-3"><a href="#" class="thumbnail"><img src="{{ asset('frontend/images/5.jpg') }}"><div class="text"><p>Lorem Ipsum is simply dummy text </p></div></a></div>
                              </div>
                              <!--.row-->
                           </div>
                           <!--.item-->
                           <!--
                              <a data-slide="prev" href="#Carousel" class="left carousel-control">‹</a>
                              <a data-slide="next" href="#Carousel" class="right carousel-control">›</a>
                              </div>-->
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-md-8">


                    @if(!empty($blog))
			   @foreach($blog as $_blog)
		    <div class="mid_bit reapeter">
                     <div class="row">
                        <div class="col-md-5">
                           <div class="img">

                              @foreach(json_decode($_blog->image) as $img)
							  @endforeach
							  	<img  src="{{ asset('/uploads/'.$img)}}" class="blog_img" title="blog">
                           </div>
                        </div>
                        <div class="col-md-7">
                           <div class="rgt">
                              <div class="heads_sec">
                                 <h1>{{ substr($_blog->title ,0,38)}} </h1>
                              </div>
                              <div class="spanner_sec">
                                 <span>November 27, 2016  #Lifestyles </span>
                              </div>
                              <div class="parag_sec">
                                 <p>{{ substr($_blog->description ,0,100)}}  </p>
                              </div>
                              <div class="parag_sec">

                                 <a href="{{route('blogview', $_blog->id )}}" class="btn btn-primary">Read More!</a>
                              </div>
                              <div class="row">
                                 <div class="col-md-4">
                                    <div class="para_sec">
                                       <p><!--<img class="img-circle"src="images/software1.jpg">mix theme--></p>
                                    </div>
                                 </div>
                                 <div class="col-md-4">
                                    <div class="social_sec">
                                      @php($url=url('share/'.$_blog->id))

                                     <a href="javascript:void(0);" onclick="fb_share('{{ $url }}', '{{ $_blog->title }}')" class="fbBtm"> <i class="fa fa-facebook"></i></a>
                                     <a href="http://www.twitter.com/intent/tweet?url={{ $url }}&text={{ $_blog->title }}"> <i class="fa fa-twitter"></i></a>
                                      <a href="">  <i class="fa fa-amazon"></i></a>
                                    </div>
                                 </div>
                                 <div class="col-md-4">
                                    <div class="cmt_sec">
                                       <p><i class="fa fa-comments"></i> {{$_blog->count}}&nbsp; Comments</p>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  @endforeach

                  @endif
                 @if($count >4)
				  <div class="btm_btn">
				  <a href="#" class="btn " id="loadMore">Load More</a>
					</div>
					@endif
               </div>
               <div class="col-md-4">
                  <div class="categ_sec">
				  	<ul>
                     <h1>Categories</h1>
					 @if(!empty($Category))
								@foreach($Category as $cate)
							<li><a href="{{ route('blogfilter', $cate->id)}}">{{$cate->category_name}} ({{$cate->count}})</a></li>

							@endforeach
							@endif
								</ul>
                  </div>
                  <div class="bxs_sec">
                     <ul>
                        <li>AD</li>
                        <li class="ad1">AD</li>
                        <li class="ad2">AD</li>
                        <li class="ad3">AD</li>
                        <p>Advertise here</p>
                     </ul>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div id="fb-root"></div>
			<script>
			(function (d, s, id) {
			        var js, fjs = d.getElementsByTagName(s)[0];
			        if (d.getElementById(id))
			            return;
			        js = d.createElement(s);
			        js.id = id;
			        js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.3&appId=2155520864748239";
			        fjs.parentNode.insertBefore(js, fjs);
			    }(document, 'script', 'facebook-jssdk'));

			function fb_share(dynamic_link,dynamic_title) {
			    var app_id ='2155520864748239';
			    var pageURL="https://www.facebook.com/dialog/feed?app_id=" + app_id + "&link=" + dynamic_link;
			    var w = 600;
			    var h = 400;
			    var left = (screen.width / 2) - (w / 2);
			    var top = (screen.height / 2) - (h / 2);
			    window.open(pageURL, dynamic_title, 'toolbar=no, location=no, directories=no, status=no, menubar=yes, scrollbars=no, resizable=no, copyhistory=no, width=' + 800 + ', height=' + 650 + ', top=' + top + ', left=' + left)
			    return false;
			}
			</script>
	  <script>
        $(function () {
        $(".reapeter").slice(0, 6).show();

        $("#loadMore").on('click', function (e) {
        e.preventDefault();
        $(".reapeter:hidden").slice(0, 6).slideDown();
        if ($(".reapeter:hidden").length == 0) {
        $("#load").fadeOut('slow');
        }
        $('html,body').animate({
        scrollTop: $(this).offset().top
        }, 1500);
        });
        });
</script>
@endsection
