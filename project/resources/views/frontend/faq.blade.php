@extends('layouts.front')
@section('content')
@include('partials.global.common-header')
<!-- breadcrumb -->
<div class="full-row bg-light overlay-dark py-5" style="background-image: url({{ $gs->breadcrumb_banner ? asset('assets/images/'.$gs->breadcrumb_banner):asset('assets/images/noimage.png') }}); background-position: center center; background-size: cover;">
   <div class="container">
      <div class="row text-center text-white">
         <div class="col-12">
            <h3 class="mb-2 text-white">{{ __('Faq') }}</h3>
         </div>
         <div class="col-12">
            <nav aria-label="breadcrumb">
               <ol class="breadcrumb mb-0 d-inline-flex bg-transparent p-0">
                  <li class="breadcrumb-item"><a href="{{ route('front.index') }}">{{ __('Home') }}</a></li>
                  <li class="breadcrumb-item active" aria-current="page">{{ __('Faq') }}</li>
               </ol>
            </nav>
         </div>
      </div>
   </div>
</div>
<!-- breadcrumb -->
<div class="full-row">
   <div class="container">
      <div class="row">
         <div class="col-lg-8 mx-auto">
            @foreach($faqs as $faq)
            <div class="simple-collaps bg-light px-4 py-3 border mb-3">
               <span class="accordion text-secondary d-block {{$loop->first ? 'active' : ''}}">{{ $faq->title }}</span>
               <div class="panel" style="{{$loop->first ? 'max-height: 330px;' : ''}}">
                  {!! clean($faq->details , array('Attr.EnableID' => true)) !!}
               </div>
            </div>
            @endforeach
         </div>
      </div>
   </div>
</div>
@include('partials.global.common-footer')
@endsection
@section('script')
@endsection
