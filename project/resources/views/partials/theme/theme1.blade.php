
@extends('layouts.front')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/front/css/category/classic.css') }}">
@endsection
@section('content')

@include('partials.global.subscription-popup')

<header class="ecommerce-header nav-on-banner">
    {{-- Top header currency and Language --}}
    @include('partials.global.top-header')
    {{-- Top header currency and Language  end--}}
    @include('partials.global.responsive-menubar')

</header>
@if($ps->slider == 1)
    <div class="position-relative">
        <span class="nextBtn"></span>
        <span class="prevBtn"></span>
        <section dir="ltr" class="home-slider owl-theme owl-carousel">
            @foreach($sliders as $data)
            <div class="banner-slide-item" style="background: url('{{asset('assets/images/sliders/'.$data->photo)}}') no-repeat center center / cover ;">
                <div class="container">
                    <div class="banner-wrapper-item text-{{ $data->position }}">
                        <div class="banner-content text-dark ">
                            <h5 class="subtitle text-dark slide-h5">{{$data->subtitle_text}}</h5>

                            <h2 class="title text-dark slide-h5">{{$data->title_text}}</h2>

                            <p class="slide-h5">{{$data->details_text}}</p>

                            <a href="{{$data->link}}" class="cmn--btn ">{{ __('SHOP NOW') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </section>
    </div>
    @endif
@if($ps->arrival_section == 1)
        <!--==================== Fashion Banner Section Start ====================-->
        <div class="full-row">
            <div class="container">
                <div class="fashion-banner-wrapper">
                @foreach ($arrivals as $key=>$arrival)

                <div class="row row-cols-lg-2 row-cols-1 justify-content-between">
                    <div class="col">
                        <div class="banner-wrapper hover-img-zoom custom-class-121">
                            <div class="banner-image overflow-hidden transation">
                                <a href="{{ route('front.category') }}"><img class="lazy" data-src="{{ $arrival->photo ?  asset('assets/images/arrival/'.$arrival->photo): "" }}" alt="Banner Image"></a>
                            </div>
                            <div class="banner-content position-absolute">
                                <div class="product-tag" style="font-size: 15px;text-transform: uppercase; color: var(--theme-secondary-color); letter-spacing: 3px;"><span>{{ $arrival->title }}</span></div>
                                <h2 style="margin: 10px 0 20px;"><a href="{{ route('front.category') }}" class="text-dark mb-10 d-block">{{ $arrival->header }}</a></h2>
                                <a href="{{ route('front.category') }}" class="btn-link-left-line">{{ __('Shop Now') }}</a>
                            </div>
                        </div>

                    </div>
                    <div class="col hide1">
                        <div class="products-avilable-number fact-counter">
                            @if($loop->first)
                            <div class="mb-30 count wow fadeIn" data-wow-duration="300ms">
                                <div class="counting d-table">
                                    <div>
                                        <span class="count-num" data-speed="3000" data-stop="{{ $products->count() }}">0</span>
                                        <span>+</span>
                                        <span class="title">@lang('Products For You')</span>
                                    </div>
                                </div>
                            </div>
                            @elseif($loop->last)
                            <div class="mb-30 count wow fadeIn counting-bottom" data-wow-duration="300ms">
                                <div class="counting d-table">
                                    <div>
                                        <span class="count-num" data-speed="3000" data-stop="{{ $ratings->count()>0 ? $ratings->count() : '2156' }}">0</span>
                                        <span>+</span>
                                        <span class="title">@lang('Feedback Given By Customer')</span>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>



                @endforeach
            </div>
            </div>
        </div>
        <!--==================== Fashion Banner Section End ====================-->
@endif


<div id="extraData">
    <div class="text-center">
        <img  src="{{asset('assets/images/'.$gs->loader)}}">
    </div>
</div>



    @if(isset($visited))
    @if($gs->is_cookie == 1)
        <div class="cookie-bar-wrap show">
            <div class="container d-flex justify-content-center">
                <div class="col-xl-10 col-lg-12">
                    <div class="row justify-content-center">
                        <div class="cookie-bar">
                            <div class="cookie-bar-text">
                                {{ __('The website uses cookies to ensure you get the best experience on our website.') }}
                            </div>
                            <div class="cookie-bar-action">
                                <button class="btn btn-primary btn-accept">
                                {{ __('GOT IT!') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @endif
<!-- Scroll to top -->
<a href="#" class="scroller text-white" id="scroll"><i class="fa fa-angle-up"></i></a>
<!-- End Scroll To top -->

@endsection
@section('script')
	<script>
		let checkTrur = 0;
		$(window).on('scroll', function(){

		if(checkTrur == 0){
			$('#extraData').load('{{route('front.extraIndex')}}');
			checkTrur = 1;
		}
		});
        var owl = $('.home-slider').owlCarousel({
        loop: true,
        nav: false,
        dots: true,
        items: 1,
        autoplay: true,
        margin: 0,
        animateIn: 'fadeInDown',
        animateOut: 'fadeOutUp',
        mouseDrag: false,
    })
    $('.nextBtn').click(function() {
        owl.trigger('next.owl.carousel', [300]);
    })
    $('.prevBtn').click(function() {
        owl.trigger('prev.owl.carousel', [300]);
    })
	</script>
@endsection
