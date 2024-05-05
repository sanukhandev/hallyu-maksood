<a href="{{ route('front.product', $prod->slug) }}" class="single-product-flas">
    <div class="img">
       <img class="lazy" data-src="{{ $prod->thumbnail ? asset('assets/images/thumbnails/'.$prod->thumbnail):asset('assets/images/noimage.png') }}" alt="">
       @if(!empty($prod->features))
       <div class="sell-area">
          @foreach($prod->features as $key => $data1)
          <span class="sale" style="background-color:{{ $prod->colors[$key] }}">
          {{ $prod->features[$key] }}
          </span>
          @endforeach
       </div>
       @endif
    </div>
    <div class="content">
       <h4 class="name">
          {{ $prod->showName() }}
       </h4>
       <ul class="stars d-flex">
          <div class="ratings">
             <div class="empty-stars"></div>
             <div class="full-stars" style="width:{{ App\Models\Rating::ratings($prod->id) }}%"></div>
          </div>
          <li class="ml-2">
             <span>({{ App\Models\Rating::ratingCount($prod->id) }})</span>
          </li>
       </ul>
       <div class="price">
          <span class="new-price">{{ $prod->showPrice() }}</span>
          <small class="old-price"><del>{{ $prod->showPreviousPrice() }}</del></small>
       </div>
       <ul class="action-meta">
          {{-- WISHLIST SECTION --}}
          @if(Auth::check())
          <li>
             <span class="wish add-to-wish" data-href="{{ route('user-wishlist-add',$prod->id) }}" data-toggle="tooltip" data-placement="top" title="{{ __('Wish') }}">
             <i class="far fa-heart"></i>
             </span>
          </li>
          @else
          <li>
             <span rel-toggle="tooltip" title="{{ __('Wish') }}" data-placement="top" class="wish add-to-wish" data-toggle="modal" data-target="#user-login">
             <i class="far fa-heart"></i>
             </span>
          </li>
          @endif
          {{-- WISHLIST SECTION ENDS --}}
          {{-- ADD TO CART SECTION --}}
          @if($prod->product_type == "affiliate")
          <li>
             <span class="cart-btn affilate-btn" data-href="{{ $prod->affiliate_link }}" data-toggle="tooltip" data-placement="top" title="{{ __('Buy Now') }}">
             <i class="icofont-cart"></i>
             </span>
          </li>
          @else
          @if($prod->emptyStock())
          <li>
             <span class="cart-btn cart-out-of-stock" data-toggle="tooltip" data-placement="top" title="{{ __('Out Of Stock') }}">
             <i class="icofont-close-circled"></i>
             </span>
          </li>
          @else
          <li>
             <span class="cart-btn add-to-cart add-to-cart-btn" data-href="{{ route('product.cart.add',$prod->id) }}"  title="{{ __('Add To Cart') }}">
             <i class="icofont-cart"></i>
             </span>
          </li>
          <li>
             <span class="cart-btn quick-view" data-href="{{ route('product.quick',$prod->id) }}" rel-toggle="tooltip" data-placement="top" title="{{ __('Quick View') }}" data-toggle="modal" data-target="#quickview">
             <i class="fas fa-eye"></i>
             </span>
          </li>
          @endif
          @endif
          {{-- ADD TO CART SECTION ENDS --}}
          {{-- ADD TO COMPARE SECTION --}}
          <li>
             <span class="compear add-to-compare" data-href="{{ route('product.compare.add',$prod->id) }}" data-toggle="tooltip" data-placement="top" title="{{ __('Compare') }}">
             <i class="fas fa-random"></i>
             </span>
          </li>
          {{-- ADD TO COMPARE SECTION ENDS --}}
       </ul>
       <div class="deal-counter">
          <div data-countdown="{{ $prod->discount_date }}"></div>
       </div>
    </div>
 </a>
