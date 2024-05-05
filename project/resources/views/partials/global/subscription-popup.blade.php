@if(isset($visited))

@if($gs->is_popup == 1)

<!-- Advertising Banner Section -->

<!--  Starting of subscribe-pre-loader Area   -->

<div style="display:none">
    <img src="{{asset('assets/images/'.$gs->popup_background)}}">
</div>

<div class="subscribe-preloader-wrap" id="subscriptionForm">
    <div class="subscribePreloader__thumb" style="background-image: url({{asset('assets/images/'.$gs->popup_background)}});">
        <span class="preload-close"><i class="fas fa-times"></i></span>
        <div class="subscribePreloader__text text-center">
            <h1>{{ __('NEWSLETTER') }}</h1>
            <p>{{ $gs->pop_description }}</p>
            <form action="{{route('front.subscribe')}}" class="subscribeform" method="POST">
                {{csrf_field()}}
                <div class="form-group">
                    <input type="email" name="email"  placeholder="{{ __('Enter Your Email Address') }}" required="">
                    <button   type="submit" class="subscribe-btn">{{ __('SUBSCRIBE') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Advertising Banner Section Ends -->

@endif

@endif
