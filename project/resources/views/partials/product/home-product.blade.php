<div class="product type-product">
    <div class="product-wrapper">
       <div class="product-image">
          <a href="{{ route('front.product', $prod->slug) }}" class="woocommerce-LoopProduct-link"><img class="lazy" data-src="{{ $prod->thumbnail ? asset('assets/images/thumbnails/'.$prod->thumbnail):asset('assets/images/noimage.png') }}" alt="Product Image"></a>
          @if(!empty($prod->features))
          <div class="product-variations">
             @foreach($prod->features as $key => $data1)
             <span class="active sale"><a href="#" style="background-color: {{ $prod->colors[$key] }}">{{ $prod->features[$key] }}</a></span>
             @endforeach
          </div>
          @endif
          @if (round($prod->offPercentage() )>0)
          <div class="on-sale">- {{ round($prod->offPercentage() )}}%</div>
          @endif

          <div class="hover-area">
            @if($prod->product_type == "affiliate")
            <div class="cart-button">
               <a href="javascript:;" data-href="{{ $product->affiliate_link }}" class="button add_to_cart_button affilate-btn" data-bs-toggle="tooltip" data-bs-placement="right" title="" data-bs-original-title="{{ __('Add To Cart') }}" aria-label="{{ __('Add To Cart') }}"></a>
            </div>
            @else
            @if($prod->emptyStock())
            <div class="closed">
               <a class="cart-out-of-stock button add_to_cart_button" href="#" title="{{ __('Out Of Stock') }}" ><i class="flaticon-cancel flat-mini mx-auto"></i></a>
            </div>
            @else
            <div class="cart-button">
               <a href="javascript:;" data-href="{{ route('product.cart.add',$prod->id) }}" class="add-cart button add_to_cart_button" data-bs-toggle="tooltip" data-bs-placement="right" title="" data-bs-original-title="{{ __('Add To Cart') }}" aria-label="{{ __('Add To Cart') }}"></a>
            </div>
            <div class="cart-button buynow">
                <a  class="button add_to_cart_button add-to-cart-quick" href="javascript:;" data-bs-toggle="tooltip" data-href="{{ route('product.cart.quickadd',$prod->id) }}" data-bs-placement="right" title="{{ __('Buy Now') }}" data-bs-original-title="{{ __('Buy Now') }}"></a>
            </div>
            @endif
            @endif
            @if(Auth::check())
            <div class="wishlist-button">
               <a class="add_to_wishlist  new button add_to_cart_button" id="add-to-wish" href="javascript:;" data-href="{{ route('user-wishlist-add',$prod->id) }}" data-bs-toggle="tooltip" data-bs-placement="right" title="" data-bs-original-title="Add to Wishlist" aria-label="Add to Wishlist">{{ __('Wishlist') }}</a>
            </div>
            @else
            <div class="wishlist-button">
               <a class="add_to_wishlist button add_to_cart_button" href="{{ route('user.login') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="" data-bs-original-title="Add to Wishlist" aria-label="Add to Wishlist">{{ __('Wishlist') }}</a>
            </div>
            @endif
            <div class="compare-button">
               <a class="compare button add_to_cart_button" data-href="{{ route('product.compare.add',$prod->id) }}" href="javascrit:;" data-bs-toggle="tooltip" data-bs-placement="right" title="" data-bs-original-title="Compare" aria-label="Compare">{{ __('Compare') }}</a>
            </div>
         </div>
       </div>
       <div class="product-info">
          <h3 class="product-title"><a href="{{ route('front.product', $prod->slug) }}">{{ $prod->showName() }}</a></h3>
          <div class="product-price">
             <div class="price">
                <ins>{{ $prod->showPrice() }} </ins>
                <del>{{ $prod->showPreviousPrice() }}</del>
             </div>
          </div>
          <div class="shipping-feed-back">
             <div class="star-rating">
                <div class="rating-wrap">
                   <p><i class="fas fa-star"></i><span> {{ App\Models\Rating::ratings($prod->id) }} ({{ App\Models\Rating::ratingCount($prod->id) }})</span></p>
                </div>
             </div>
          </div>
       </div>
    </div>
 </div>
