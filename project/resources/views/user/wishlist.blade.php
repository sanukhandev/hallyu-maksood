@extends('layouts.front')
@section('content')
@include('partials.global.common-header')
<!-- breadcrumb -->
<div class="full-row bg-light overlay-dark py-5" style="background-image: url({{ $gs->breadcrumb_banner ? asset('assets/images/'.$gs->breadcrumb_banner):asset('assets/images/noimage.png') }}); background-position: center center; background-size: cover;">
   <div class="container">
      <div class="row text-center text-white">
         <div class="col-12">
            <h3 class="mb-2 text-white">{{ __('Wishlist') }}</h3>
         </div>
         <div class="col-12">
            <nav aria-label="breadcrumb">
               <ol class="breadcrumb mb-0 d-inline-flex bg-transparent p-0">
                  <li class="breadcrumb-item"><a href="{{ route('user-dashboard') }}">{{ __('Dashboard') }}</a></li>
                  <li class="breadcrumb-item active" aria-current="page">{{ ('Wishlist') }}</li>
               </ol>
            </nav>
         </div>
      </div>
   </div>
</div>
<!-- breadcrumb -->
<!--==================== Wishlist Section Start ====================-->
<div class="full-row">
   <div class="container" id="ajaxContent">
        <div class="mb-4 d-xl-none">
            <button class="dashboard-sidebar-btn btn bg-primary rounded">
                <i class="fas fa-bars"></i>
            </button>
        </div>
      <div class="row wish_load">
         <div class="col-12">
            <table class="shop_table cart wishlist_table wishlist_view traditional table" data-pagination="no" data-per-page="5" data-page="1" data-id="3989" data-token="G5CZRAZPRKEY">
               <thead>
                  <tr>
                     <th class="product-thumbnail">{{ __('Product Image') }}</th>
                     <th class="product-name"> <span class="nobr"> {{ __('Product name') }} </span></th>
                     <th class="product-price"> <span class="nobr"> {{ __('Unit price') }} </span></th>
                     <th class="product-stock-status"> <span class="nobr"> {{ __('Stock status') }} </span></th>
                     <th class="product-add-to-cart"> <span class="nobr"> </span>{{ __('Actions') }}</th>
                     <th class="product-remove"> <span class="nobr"> </span></th>
                  </tr>
               </thead>
               <tbody class="wishlist-items-wrapper">
                  @foreach($wishlists as $wishlist)
                  <tr id="yith-wcwl-row-103" data-row-id="103">
                     <td class="product-thumbnail">
                        <a href="{{ route('front.product', $wishlist->slug) }}"> <img src="{{ $wishlist->photo ? asset('assets/images/products/'.$wishlist->photo):asset('assets/images/noimage.png') }}" alt=""> </a>
                     </td>
                     <td class="product-name"> <a href="{{ route('front.product', $wishlist->slug) }}">{{  mb_strlen($wishlist->name,'UTF-8') > 35 ? mb_substr($wishlist->name,0,35,'UTF-8').'...' : $wishlist->name }}</a></td>
                     <td class="product-price"> <span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">{{ $wishlist->showPrice() }}  <small>
                        <del>
                        {{ $wishlist->showPreviousPrice() }}
                        </del>
                        </small></bdi>
                        </span>
                     </td>
                     <td class="product-stock-status">
                        @if($wishlist->type == 'Physical')
                        @if($wishlist->emptyStock())
                        <div class="stock-availability out-stock">{{ ('Out Of Stock') }}</div>
                        @else
                        <div class="stock-availability in-stock text-bold">{{ ('In Stock') }}</div>
                        @endif
                        @else
                        <div class="stock-availability in-stock text-bold">{{ $wishlist->type }}</div>
                        @endif
                     </td>
                     <td class="product-add-to-cart">
                        <!-- Date added -->
                        {{-- PRODUCT QUANTITY SECTION ENDS --}}
                        @if($wishlist->type == "affiliate")
                        <li class="addtocart">
                           <a class="affilate-btn"  data-href="{{ $productt->affiliate_link }}" target="_blank" > {{ __('Buy Now') }}</a>
                        </li>
                        @else
                        @if($wishlist->emptyStock())
                        <li class="addtocart">
                           <a href="javascript:;" class="cart-out-of-stock">
                           {{ __('Out Of Stock') }}</a>
                        </li>
                        @else
                        <li class="addtocart">
                           <a href="javascript:;" class="add-cart"  data-href="{{ route('product.cart.add',$wishlist->id) }}">{{ __('Add to Cart')}}</a>
                        </li>
                        <li class="addtocart">
                           <a id="qaddcrt" class="add-to-cart-quick" href="javascript:;" data-href="{{ route('product.cart.quickadd',$wishlist->id) }}">
                           {{ __('Buy Now') }}
                           </a>
                        </li>
                        @endif
                        @endif
                        <!-- Remove from wishlist -->
                     </td>
                     <td class="product-remove">
                        <div>
                           <a href="javascript:;" data-href="{{ route('user-wishlist-remove', App\Models\Wishlist::where('user_id','=',$user->id)->where('product_id','=',$wishlist->id)->first()->id ) }}" class="remove wishlist-remove remove_from_wishlist" title="Remove this product">×</a>
                        </div>
                     </td>
                     <input type="hidden" id="product_price" value="{{ round($wishlist->vendorPrice() * $curr->value,2) }}">
                     <input type="hidden" id="product_id" value="{{ $wishlist->id }}">
                     <input type="hidden" id="curr_pos" value="{{ $gs->currency_format }}">
                     <input type="hidden" id="curr_sign" value="{{ $curr->sign }}">
                  </tr>
                  @endforeach
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>
<!--==================== Wishlist Section End ====================-->
@includeIf('partials.global.common-footer')
@endsection
