<style>
    .text-center {
    text-align: center!important;
    margin: 0 auto;
}
</style>

 @if($ps->category==1)
 <div class="full-row">
     <div class="container">
         <div class="row justify-content-center">
             <div class="col-lg-5">
                 <span class="text-secondary pb-2 d-table tagline mx-auto text-uppercase text-center">{{ __('Featured Products') }}</span>
                 <h2 class="main-title mb-4 text-center text-secondary">{{ __('Our Featured Products') }}</h2>
             </div>
         </div>
         <div class="products product-style-1">
            <div class="row g-4 row-cols-xl-4 row-cols-md-3 row-cols-sm-2 row-cols-1 e-title-general e-title-hover-primary e-image-bg-light e-hover-image-zoom e-info-center">

                @foreach($popular_products as $prod)
                <div class="col">
                    @include('partials.product.home-product')
                </div>
                @endforeach
            </div>
        </div>
     </div>
 </div>
 <!--==================== Top Products Section End ====================-->
 @endif

 @if($ps->deal_of_the_day==1)

<!--==================== Deal of the day Section Start ====================-->
<div class="full-row bg-light">
    <div class="container">
        <div class="row offer-product align-items-center">
            <div class="col-xl-5 col-lg-7">
                <h1 class="down-line-secondary text-dark text-uppercase mb-30">{{ __('Deal') }} <br> {{ __('of the Day') }}</h1>
                <div class="product type-product">
                    <div class="product-wrapper">
                        <div class="product-info">

                            <h3 class="product-title">{{ $gs->deal_title }}</h3>
                            <div class="product-price">

                                <div class="on-sale"><span>50</span><span>% off</span></div>
                            </div>
                            <div class="font-fifteen">
                                <p>{{ $gs->deal_details }}</p>
                            </div>
                            <div class="time-count time-box text-center my-30 flex-between w-75" data-countdown="{{ $gs->deal_time }}"></div>
                            <a href="{{ route('front.category').'?type=flash'  }}" class="btn btn-dark text-uppercase ">{{ __('Shop Now') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-5 offset-xl-1">

                <div class="xs-mt-30"><img src="{{ $gs->deal_background ? asset('assets/images/'.$gs->deal_background):asset('assets/images/noimage.png') }}" alt=""></div>

            </div>
        </div>
    </div>
</div>
<!--==================== Deal of the day Section End ====================-->

 @endif
        <!--==================== Deal of the day Section End ====================-->


@if($ps->top_big_trending==1)
        <!--==================== Top Collection Section Start ====================-->
        <div class="full-row bg-white">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="top-collection-tab nav-tab-active-secondary">
                            <ul class="nav nav-pills list-color-general justify-content-center mb-5">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="pill" href="#pills-new-arrival-two">{{ __('New Arrival') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="pill" href="#pills-Trending-two">{{ __('Trending') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="pill" href="#pills-best-selling-two">{{ __('Best Selling') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="pill" href="#pills-featured-two">{{ __('Popular') }}</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="pills-new-arrival-two">
									<div class="products product-style-1">
										<div class="row g-4 row-cols-xl-4 row-cols-md-3 row-cols-sm-2 row-cols-1 e-title-general e-title-hover-primary e-image-bg-light e-hover-image-zoom e-info-center">
										    @if($latest_products->count()>0)

                                            @foreach($latest_products as $prod)
											<div class="col">
                                                @include('partials.product.home-product')
											</div>
                                            @endforeach
                                            @else
                                            <div  class="text-center">
                                                <h2>{{__('No Product Found!')}}</h2>
                                            </div>
                                            @endif
										</div>
									</div>
                                </div>
                                <div class="tab-pane fade" id="pills-Trending-two">
									<div class="products product-style-1">
										<div class="row g-4 row-cols-xl-4 row-cols-md-3 row-cols-sm-2 row-cols-1 e-title-general e-title-hover-primary e-image-bg-light e-hover-image-zoom e-info-center">
										    @if($trending_products->count()>0)
											@foreach($trending_products as $prod)
											<div class="col">
                                                @include('partials.product.home-product')
											</div>
                                            @endforeach
                                            @else
                                            <div  class="text-center">
                                                <h2>{{__('No Product Found!')}}</h2>
                                            </div>
                                            @endif

										</div>
									</div>
                                </div>
                                <div class="tab-pane fade" id="pills-best-selling-two">
									<div class="products product-style-1">
										<div class="row g-4 row-cols-xl-4 row-cols-md-3 row-cols-sm-2 row-cols-1 e-title-general e-title-hover-primary e-image-bg-light e-hover-image-zoom e-info-center">
										    @if($sale_products->count()>0)
											@foreach($sale_products as $prod)
											<div class="col">
                                                @include('partials.product.home-product')
											</div>
                                            @endforeach
                                            @else
                                            <div  class="text-center">
                                                <h2>{{__('No Product Found!')}}</h2>
                                            </div>
                                            @endif

										</div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-featured-two">
									<div class="products product-style-1">
										<div class="row g-4 row-cols-xl-4 row-cols-md-3 row-cols-sm-2 row-cols-1 e-title-general e-title-hover-primary e-image-bg-light e-hover-image-zoom e-info-center">
										      @if($popular_products->count()>0)
                                            @foreach($popular_products as $prod)
											<div class="col">
                                                @include('partials.product.home-product')
											</div>
                                            @endforeach
                                            @else
                                            <div  class="text-center">
                                                <h2>{{__('No Product Found!')}}</h2>
                                            </div>

                                            @endif
										</div>
									</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--==================== Top Collection Section End ====================-->
@endif
<!--==================== Service Section Start ====================-->
@if ($ps->partner==1)
<div class="full-row bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5">

                <h2 class="main-title mb-4 text-center text-secondary">{{ __('Our Brands')}}</h2>
                <span class="mb-30 sub-title text-general font-medium ordenery-font font-400 text-center">{{ $gs->partner_text }}</span>
            </div>
        </div>
        <div class="row g-3">
            @foreach (DB::table('partners')->get() as $data)
            <div class="col-6 col-sm-4 col-lg-3 col-xl-2">
                <div class="simple-service">
                    <img class="lazy" data-src="{{ asset('assets/images/partner/'.$data->photo) }}" alt="">

                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endif

<!--==================== Service Section End ====================-->

<!--==================== Top Products Section Start ====================-->
@if($ps->best_sellers==1)
<div class="full-row">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <span class="text-secondary pb-2 d-table tagline mx-auto text-uppercase text-center">{{ __('Top Products') }}</span>
                <h2 class="main-title mb-4 text-center text-secondary">{{ __('Best Selling Products') }}</h2>

            </div>
        </div>

        <div class="row">
            <div class="col-12">

                 <div class="products product-style-1 owl-mx-15">
                    <div class="four-carousel owl-carousel dot-disable nav-arrow-middle-show e-title-general e-title-hover-primary e-image-bg-light  e-info-center e-title-general e-title-hover-primary e-image-bg-light e-hover-image-zoom e-info-center">
                    @foreach($best_products as $prod)
                        <div class="item">
                            @include('partials.product.home-product')
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--==================== Top Products Section End ====================-->
@endif
 <!--==================== Our Blog Section Start ====================-->
@if($ps->blog==1)
    <!-- <div class="full-row pt-0">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-5">

                        <h2 class="main-title mb-4 text-center text-secondary">{{ __('Latest Post') }}</h2>
                        <span class="mb-30 sub-title text-general font-medium ordenery-font font-400 text-center">{{ __('Cillum eu id enim aliquip aute ullamco anim. Culpa deserunt nostrud excepteur voluptate velit ipsum esse enim.') }}</span>
                    </div>
                </div>
                <div class="row row-cols-lg-2 row-cols-1">
                    @foreach ($blogs as $blog)
                    <div class="col">
                        <div class="thumb-latest-blog text-center transation hover-img-zoom mb-3">
                            <div class="post-image overflow-hidden">
                                <a href="{{ route('front.blogshow',$blog->slug) }}">
                                    <img class="lazy" data-src="{{ asset('assets/images/blogs/'.$blog->photo) }}" alt="Image not found!">
                                </a>

                            </div>
                            <div class="post-content">
                                <h3><a href="{{ route('front.blogshow',$blog->slug) }}" class="transation text-dark hover-text-primary d-table my-10 mx-sm-auto">{{ mb_strlen($blog->title,'UTF-8') > 200 ? mb_substr($blog->title,0,200,'UTF-8')."...":$blog->title }}</a></h3>
                                <div class="post-meta font-small text-uppercase list-color-general my-3">
                                    <p class="post-date">{{ date('d M, Y',strtotime($blog->created_at)) }}</p>
                                </div>
                                <a href="{{ route('front.blogshow',$blog->slug) }}" class="btn-link-left-line">{{ __('Read More') }}</a>
                            </div>
                        </div>
                    </div>
                    @endforeach


                </div>
            </div>
        </div> -->
        <!--==================== Our Blog Section End ====================-->
@endif
@if($ps->third_left_banner==1)
    <!--==================== Newsleter Section Start ====================-->
    <div class="full-row bg-dark py-30">
        <div class="container">
            <div class="row mx-auto">
                <div class="col-lg-5 col-md-6 mx-auto">
                    <div class="d-flex align-items-center h-100">
                        <h4 class="text-white mb-0 text-uppercase">{{ __('Sign up to newslatter') }}</h4>
                    </div>
                </div>

                <div class="col-lg-5 col-md-12">
                    <form action="{{route('front.subscribe')}}" class="subscribe-form subscribeform  position-relative md-mt-20" method="POST">
                        @csrf
                        <input class="form-control rounded-pill mb-0" type="text" placeholder="Enter your email" aria-label="Address" name="email">
                        <button type="submit" class="btn btn-secondary rounded-right-pill text-white">{{ __('Send') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--==================== Newsleter Section End ====================-->
@endif
    <!--==================== Footer Section Start ====================-->
    <footer class="full-row bg-white">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="footer-widget mb-5">
                        <div class="footer-logo mb-4">
                            <a href="{{ route('front.index') }}"><img class="lazy" data-src="{{ asset('assets/images/'.$gs->footer_logo) }}" alt="Image not found!" /></a>
                        </div>
                        <div class="widget-ecommerce-contact">
                            @if($ps->phone != null)
                            <span class="font-medium font-500 text-dark">{{ __('Got Questions Call us 24/7') }}</span>
                            <div class="text-dark font-medium font-400 ">{{ $ps->phone }}</div>
                            @endif
                            @if($ps->street != null)
                            <span class="h6 text-secondary mt-2">{{ __('Address :') }}</span>
                            <div class="text-general">{{ $ps->street }}</div>
                            @endif
                            @if($ps->email != null)
                            <span class="h6 text-secondary mt-2">{{ __('Email :') }}</span>
                            <div class="text-general">{{ $ps->email }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="footer-widget media-widget mb-5">
                        @foreach(DB::table('social_links')->where('user_id',0)->where('status',1)->get() as $link)
                        <a href="{{ $link->link }}"><i class="{{ $link->icon }}"></i></a>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-widget category-widget mb-5">
                        <h3 class="widget-title mb-4">{{ __('Product Category') }}</h3>
                        <ul>
                            @foreach (DB::table('categories')->where('language_id',$langg->id)->get()->take(6) as $cate)
                            <li><a href="{{route('front.category', $cate->slug)}}{{!empty(request()->input('search')) ? '?search='.request()->input('search') : ''}}">{{ $cate->name }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <div class="footer-widget category-widget mb-5">
                        <h3 class="widget-title mb-4 xs-mx-none">{{ __('Footer Links') }}</h3>
                        <ul>
                            @if($ps->home == 1)
                            <li>
                                <a href="{{ route('front.index') }}">{{ __('Home') }}</a>
                            </li>
                            @endif
                            <!-- @if($ps->blog == 1)
                            <li>
                                <a href="{{ route('front.blog') }}">{{ __('Blog') }}</a>
                            </li>
                                @endif -->
                                @if($ps->faq == 1)
                            <li>
                                <a href="{{ route('front.faq') }}">{{ __('Faq') }}</a>
                            </li>
                            @endif
                            @foreach(DB::table('pages')->where('language_id',$langg->id)->where('footer','=',1)->get() as $data)
                            <li><a href="{{ route('front.vendor',$data->slug) }}">{{ $data->title }}</a></li>
                            @endforeach
                            @if($ps->contact == 1)
                                <li>
                                <a href="{{ route('front.contact') }}">{{ __('Contact Us') }}</a>
                            </li>
                                @endif

                        </ul>
                    </div>
                </div>
                <!-- <div class="col-lg-3 col-md-6">
                    <div class="footer-widget widget-nav mb-5">
                        <h3 class="widget-title mb-4">{{ __('Recent Post') }}</h3>
                        <ul>
                            @foreach (DB::table('blogs')->where('language_id',$langg->id)->latest()->limit(3)->get() as $footer_blog)
                            <li>
                                <div class="post">
                                    <div class="post-img">
                                        <img class="lozad lazy" data-src="{{ asset('assets/images/blogs/'.$footer_blog->photo) }}" alt="">
                                        </div>
                                        <div class="post-details">
                                        <a href="{{ route('front.blogshow',$footer_blog->slug) }}">
                                            <h4 class="post-title">
                                                {{mb_strlen($footer_blog->title,'UTF-8') > 45 ? mb_substr($footer_blog->title,0,45,'UTF-8')." .." : $footer_blog->title}}
                                            </h4>
                                        </a>
                                        <p class="date">
                                            {{ date('M d - Y',(strtotime($footer_blog->created_at))) }}
                                        </p>
                                    </div>
                                </div>
                            </li>

                            @endforeach
                        </ul>
                    </div>
                </div> -->
            </div>
        </div>
    </footer>
    <!--==================== Footer Section End ====================-->

<!--==================== Copyright Section Start ====================-->

        <div class="container">

                <div class="mx-auto text-center py-3">
                    <span class="sm-mb-10 d-block">{{ $gs->copyright }}</span>
                </div>


        </div>
<!--==================== Copyright Section End ====================-->



<script src="{{ asset('assets/front/js/extraindex.js') }}"></script>

<script>

    $(".lazy").Lazy();
</script>

