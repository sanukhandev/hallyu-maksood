@extends('layouts.front')
@section('content')
@include('partials.global.common-header')
<!-- breadcrumb -->
<div class="full-row bg-light overlay-dark py-5 mb-4" style="background-image: url({{ $gs->breadcrumb_banner ? asset('assets/images/'.$gs->breadcrumb_banner):asset('assets/images/noimage.png') }}); background-position: center center; background-size: cover;">
   <div class="container">
      <div class="row text-center text-white">
         <div class="col-12">
            <h3 class="mb-2 text-white">{{ __('Compare') }}</h3>
         </div>
         <div class="col-12">
            <nav aria-label="breadcrumb">
               <ol class="breadcrumb mb-0 d-inline-flex bg-transparent p-0">
                  <li class="breadcrumb-item"><a href="#">{{ __('Home') }}</a></li>
                  <li class="breadcrumb-item active" aria-current="page">{{ __('Compare') }}</li>
               </ol>
            </nav>
         </div>
      </div>
   </div>
</div>
<!-- breadcrumb -->
<!-- Compare Area Start -->
<section class="compare-page">
   <div class="container">
      @if(Session::has('compare'))
      <div class="row">
         <div class="col-lg-12">
            <div class="content">
               <div class="com-heading">
                  <h2 class="title">
                     {{ __('Product Compare') }}
                  </h2>
               </div>
               <div class="compare-page-content-wrap">
                  <div class="compare-table table-responsive">
                     <table class="table table-bordered mb-0">
                        <tbody>
                           <tr>
                              <td class="first-column top">{{ __('Product Name')}}</td>
                              @foreach($products as $product)
                              <td class="product-image-title c{{$product['item']['id']}}">
                                 <img class="img-fluid lazy" data-src="{{ $product['item']['thumbnail'] ? asset('assets/images/thumbnails/'.$product['item']['thumbnail']):asset('assets/images/noimage.png') }}" alt="Compare product['item']">
                                 <a href="{{ route('front.product', $product['item']['slug']) }}">
                                    <h4 class="title">
                                       {{ $product['item']['name'] }}
                                    </h4>
                                 </a>
                              </td>
                              @endforeach
                           </tr>
                           <tr>
                              <td class="first-column">{{ __('Price') }}</td>
                              @foreach($products as $product)
                              <td class="pro-price c{{$product['item']['id']}}">{{ App\Models\Product::find($product['item']['id'])->showPrice() }}</td>
                              @endforeach
                           </tr>
                           <tr>
                              <td class="first-column">{{ __('Rating') }}</td>
                              @foreach($products as $product)
                              <td class="pro-ratting c{{$product['item']['id']}}">
                                 <div class="ratings">
                                    <div class="empty-stars"></div>
                                    <div class="full-stars" style="width:{{App\Models\Rating::ratings($product['item']['id'])}}%"></div>
                                 </div>
                              </td>
                              @endforeach
                           </tr>
                           <tr>
                              <td class="first-column">{{ __('Description') }}</td>
                              @foreach($products as $product)
                              <td class="pro-desc c{{$product['item']['id']}}">
                                 <p>{{ strip_tags($product['item']['details']) }}</p>
                              </td>
                              @endforeach
                           </tr>
                           <tr>
                              <td class="first-column">{{ __('Add To Cart') }}</td>
                              @foreach($products as $product)
                              <td class="c{{$product['item']['id']}}">
                                 @if($product['item']['product_type'] == "affiliate")
                                 <a href="{{  $product['item']['affiliate_link'] }}" class="btn__bg">{{ __('Buy Now') }}</a>
                                 @else
                                 <li class="addtocart">
                                    <a href="javascript:;" class="add-cart"  data-href="{{ route('product.cart.add',$product['item']['id']) }}">{{ __('Add to Cart')}}</a>
                                 </li>
                                 <li class="addtocart">
                                    <a id="qaddcrt" class="add-to-cart-quick" href="javascript:;" data-href="{{ route('product.cart.quickadd',$product['item']['id']) }}">
                                    {{ __('Buy Now') }}
                                    </a>
                                 </li>
                                 @endif
                              </td>
                              @endforeach
                           </tr>
                           <tr>
                              <td class="first-column">{{ __('Remove') }}</td>
                              @foreach($products as $product)
                              <td class="pro-remove c{{$product['item']['id']}}">
                                 <i class="far fa-trash-alt compare-remove" data-href="{{ route('product.compare.remove',$product['item']['id']) }}" data-class="c{{$product['item']['id']}}"></i>
                              </td>
                              @endforeach
                           </tr>
                        </tbody>
                     </table>
                  </div>
               </div>
            </div>
         </div>
      </div>
      @else
      <div class="row mb-2">
         <div class="col-lg-12">
            <div class="content">
               <div class="com-heading ">
                  <h2 class="title p-5 text-center text-center border ">
                     {{ __('No Product To Compare.') }}
                  </h2>
               </div>
            </div>
         </div>
      </div>
      @endif
   </div>
</section>
<!-- Compare Area End -->
@include('partials.global.common-footer')
@endsection
@section('script')
@endsection
