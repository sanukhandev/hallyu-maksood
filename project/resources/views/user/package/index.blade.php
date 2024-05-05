@extends('layouts.front')

@section('content')
@include('partials.global.common-header')

 <!-- breadcrumb -->
 <div class="full-row bg-light overlay-dark py-5" style="background-image: url({{ $gs->breadcrumb_banner ? asset('assets/images/'.$gs->breadcrumb_banner):asset('assets/images/noimage.png') }}); background-position: center center; background-size: cover;">
    <div class="container">
        <div class="row text-center text-white">
            <div class="col-12">
                <h3 class="mb-2 text-white">{{ __('Pricing Plans') }}

                </h3>
            </div>
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 d-inline-flex bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="{{ route('user-dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('Pricing Plans') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<!-- breadcrumb -->

<!--==================== Blog Section Start ====================-->
<div class="full-row">
    <div class="container">
        <div class="mb-4 d-xl-none">
            <button class="dashboard-sidebar-btn btn bg-primary rounded">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <div class="row">
            <div class="col-xl-4">
                @include('partials.user.dashboard-sidebar')
            </div>
            <div class="col-xl-8">
                <div class="user-profile-details">
                    <div class="row">
                        @foreach($subs as $sub)
                            <div class="col-lg-6">
                                <div class="elegant-pricing-tables style-2 text-center">
                                    <div class="pricing-head">
                                        <h3>{{ $sub->title }}</h3>
                                        @if($sub->price  == 0)
                                        <span class="price">
                                        <span class="price-digit">{{ __('Free') }}</span>
                                        </span>
                                        @else
                                        <span class="price">
                                            <sup>{{ $curr->sign }}</sup>
                                            <span class="price-digit">{{ round($sub->price * $curr->value,2) }}</span><br>
                                            <span class="price-month">{{ $sub->days }} {{ __('Day(s)') }}</span>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="pricing-detail">
                                        {!! clean($sub->details , array('Attr.EnableID' => true)) !!}
                                    </div>
                                @if(!empty($package))
                                    @if($package->subscription_id == $sub->id)
                                        <a href="javascript:;" class="btn btn-default">{{ __('Current Plan') }}</a>
                                        <br>
                                        @if(Carbon\Carbon::now()->format('Y-m-d') > $user->date)
                                        <small class="hover-white">{{ __('Expired on:') }} {{ date('d/m/Y',strtotime($user->date)) }}</small>
                                        @else
                                        <small class="hover-white">{{ __('Ends on:') }} {{ date('d/m/Y',strtotime($user->date)) }}</small>
                                        @endif
                                         <a href="{{route('user-vendor-request',$sub->id)}}" class="hover-white"><u>{{ __('Renew') }}</u></a>
                                    @else
                                        <a href="{{route('user-vendor-request',$sub->id)}}" class="btn btn-default">{{ __('Get Started') }}</a>
                                        <br><small>&nbsp;</small>
                                    @endif
                                @else
                                    <a href="{{route('user-vendor-request',$sub->id)}}" class="btn btn-default">{{ __('Get Started') }}</a>
                                    <br><small>&nbsp;</small>
                                @endif

                                </div>
                            </div>

                            @endforeach

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!--==================== Blog Section End ====================-->

<!-- Order Tracking modal Start-->
<div class="modal fade" id="order-tracking-modal" role="dialog"  data-bs-backdrop="static" data-bs-keyboard="false"  aria-labelledby="order-tracking-modal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content text-center">
        <div class="modal-header">
            <h5 class="modal-title pt-3 pl-3 mx-auto"> <b>{{ __('Order Tracking') }}</b> </h5>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body" id="order-track">

        </div>
        </div>
    </div>
</div>
<!-- Order Tracking modal End -->

@includeIf('partials.global.common-footer')

@endsection
@section('script')


<script type="text/javascript">



</script>

@endsection




