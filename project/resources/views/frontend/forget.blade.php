@extends('layouts.front')
@section('content')
@include('partials.global.common-header')
<!-- breadcrumb -->
<div class="full-row bg-light overlay-dark py-5" style="background-image: url({{ $gs->breadcrumb_banner ? asset('assets/images/'.$gs->breadcrumb_banner):asset('assets/images/noimage.png') }}); background-position: center center; background-size: cover;">
   <div class="container">
      <div class="row text-center text-white">
         <div class="col-12">
            <h3 class="mb-2 text-white">{{ __('Forget Password') }}</h3>
         </div>
         <div class="col-12">
            <nav aria-label="breadcrumb">
               <ol class="breadcrumb mb-0 d-inline-flex bg-transparent p-0">
                  <li class="breadcrumb-item"><a href="{{ route('front.index') }}">{{ __('Home') }}</a></li>
                  <li class="breadcrumb-item active" aria-current="page">{{ __('Forget Password') }}</li>
               </ol>
            </nav>
         </div>
      </div>
   </div>
</div>
<!-- breadcrumb -->
<!--==================== Registration Form Start ====================-->
<div class="full-row">
   <div class="container">
      <div class="row">
         <div class="col">
            <div class="woocommerce">
               <div class="row">
                  <div class="col-lg-6 col-md-8 col-12 mx-auto">
                     <div class="registration-form border">
                        @include('includes.admin.form-login')
                        <h3>{{ __('Forget Password') }}</h3>
                        <form id="forgotform" action="{{route('user.forgot.submit')}}" method="POST">
                           {{ csrf_field() }}
                           <p>
                              <label for="reg_email">{{ __('Email address') }}<span class="required">*</span></label>
                              <input type="email" name="email" class="form-control border" placeholder="{{ __('Enter Email Address') }}" id="reg_email"  required="">
                           </p>
                           <p>{{ __('A password will be sent to your email address.') }}</p>
                           <p>
                              <input class="authdata" type="hidden" value="{{ __('Checking...')}}">
                              <button type="submit" class="btn btn-primary rounded-0 submit-btn" name="register" value="Register">{{ __('Submit') }}</button>
                           </p>
                        </form>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<!--==================== Registration Form Start ====================-->
@include('partials.global.common-footer')
@endsection
@section('script')
@endsection
