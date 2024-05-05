<div class="full-row pb-0">
  <div class="container">
      <div class="row single-product-wrapper">
          <div class="col-12 col-lg-4 mb-4 mb-lg-0">
              <div class="product-images overflow-hidden">
                  <div class="images-inner">
                      <div class="woocommerce-product-gallery woocommerce-product-gallery--with-images woocommerce-product-gallery--columns-4 images" data-columns="4" style="opacity: 1; transition: opacity 0.25s ease-in-out 0s;">
                          <figure class="woocommerce-product-gallery__wrapper">
                              <div class="bg-light">
                                  <img  id="single-image-zoom" src="{{filter_var($productt->photo, FILTER_VALIDATE_URL) ?$productt->photo:asset('assets/images/products/'.$productt->photo)}}" alt="Thumb Image" data-zoom-image="{{filter_var($productt->photo, FILTER_VALIDATE_URL) ?$productt->photo:asset('assets/images/products/'.$productt->photo)}}" />
                              </div>

                              <div id="gallery_09" class="product-slide-thumb">
                                  <div class="owl-carousel four-carousel dot-disable nav-arrow-middle owl-mx-5">
                                  @foreach($productt->galleries as $gal)
                                      <div class="item">
                                          <a class="active" href="{{asset('assets/images/galleries/'.$gal->photo)}}" data-image="{{asset('assets/images/galleries/'.$gal->photo)}}" data-zoom-image="{{asset('assets/images/galleries/'.$gal->photo)}}">
                                              <img src="{{asset('assets/images/galleries/'.$gal->photo)}}" alt="Thumb Image" />
                                          </a>
                                      </div>
                                  @endforeach
                                  </div>
                              </div>
                          </figure>
                      </div>
                  </div>
              </div>
          </div>

          <div class="col-12 col-lg-5 col-md-8">
              <div class="summary entry-summary">
                  <div class="summary-inner">
                      <div class="entry-breadcrumbs w-100">
                          <nav class="breadcrumb-divider-slash" aria-label="breadcrumb">
                              <ol class="breadcrumb pro-bread">
                                  <li class="breadcrumb-item"><a href="{{route('front.index')}}">{{__('Home')}}</a></li>
                                  <li class="breadcrumb-item"><a href="{{route('front.category',$productt->category->slug)}}">{{$productt->category->name}}</a></li>
                                  @if($productt->subcategory_id != null)
                                  <li class="breadcrumb-item">
                                      <a href="{{ route('front.category',[$productt->category->slug, $productt->subcategory->slug]) }}">
                                      {{$productt->subcategory->name}}
                                      </a>
                                  </li>
                                  @endif
                                  @if($productt->childcategory_id != null)
                                  <li class="breadcrumb-item">
                                      <a href="{{ route('front.category',[ $productt->category->slug,$productt->subcategory->slug,$productt->childcategory->slug]) }}">
                                      {{$productt->childcategory->name}}
                                      </a>
                                  </li>
                                  @endif

                              </ol>
                          </nav>
                      </div>
                      <h1 class="product_title entry-title">{{ $productt->name }}</h1>

                      <div class="pro-details">
                         <div class="pro-info">
                              <div class="woocommerce-product-rating">
                                  <div class="fancy-star-rating">
                                      <div class="rating-wrap"> <span class="fancy-rating good">{{ App\Models\Rating::ratings($productt->id) }} ★</span>
                                      </div>
                                      <div class="rating-counts-wrap">
                                          <a href="#reviews" class="bigbazar-rating-review-link" rel="nofollow"> <span class="rating-counts"> ({{ App\Models\Rating::ratingCount($productt->id) }}) </span> </a>
                                      </div>
                                  </div>
                              </div>

                              <p class="price">
                                  <span class="woocommerce-Price-amount amount mr-4">
                                      <bdi><span class="woocommerce-Price-currencySymbol" id="sizeprice">{{ $productt->showPrice() }}</bdi>
                                  </span>
                                  <del class="ml-3"><small>{{ $productt->showPreviousPrice() }}</small></del>
                                 <span class="on-sale"><span>{{ round((int)$productt->offPercentage() )}}</span>% Off</span>

                              </p>

                            @if($productt->type == 'Physical')
                               @if($productt->emptyStock())
                                     <div class="stock-availability out-stock">{{ ('Out Of Stock') }}</div>
                                     @else
                                     <div class="stock-availability in-stock text-bold">{{ $gs->show_stock == 0 ? '' : $productt->stock }} {{ ('In Stock') }}</div>
                               @endif
                            @endif



                         {{-- PRODUCT OTHER DETAILS SECTION --}}
                         <div class="product-offers">
                            <ul class="product-offers-list">
                               @if($productt->ship != null)
                               <li class="product-offer-item"><span class="h6">{{ __('Estimated Shipping Time:') }}</span> {{ $productt->ship }}
                               </li>
                               @endif
                               @if( $productt->sku != null )
                               <li class="product-offer-item product-id{{ $productt->product_type == 'affiliate' ? 'mt-4' : '' }}"><span class="h6">{{ __('Product SKU:') }} </span> {{ $productt->sku }}
                               </li>
                               @endif
                                  {{-- PRODUCT LICENSE SECTION --}}
                                  @if($productt->type == 'License')
                                  @if($productt->platform != null)
                                  <li class="product-offer-item license-id"><span class="h6">{{ __('Platform:') }}</span> {{ $productt->platform }}
                                  </li>
                                  @endif
                                  @if($productt->region != null)
                                  <li class="product-offer-item license-id"><span class="h6">{{ __('Region:') }}</span> {{ $productt->region }}
                                  </li>
                                  @endif
                                  @if($productt->licence_type != null)
                                  <li class="product-offer-item license-id"><span class="h6"> {{ __('License Type:') }}</span> {{ $productt->licence_type }}
                                  </li>
                                  @endif
                               @endif
                               {{-- PRODUCT LICENSE SECTION ENDS--}}
                            </ul>
                         </div>
                         </div>
                         {{-- PRODUCT OTHER DETAILS SECTION ENDS --}}
                          </div>
                         @if ($productt->stock_check == 1)
                              @if(!empty($productt->size))
                              <div class="product-size">
                                  <p class="title">{{ __('Size :') }}</p>
                                  <ul class="siz-list">
                                    @foreach(array_unique($productt->size) as $key => $data1)
                                  <li class="{{ $loop->first ? 'active' : '' }}" data-key="{{ str_replace(' ','',$data1) }}">
                                        <span class="box">
                                          {{ $data1 }}
                                        </span>
                                      </li>
                                    @endforeach
                                  </ul>
                                </div>
                         @endif
                         {{-- PRODUCT COLOR SECTION  --}}

    @if(!empty($productt->color))

      <div class="product-color">
          <div class="title">{{ __('Color :') }}</div>
          <ul class="color-list">
            @foreach($productt->color as $key => $data1)
              <li class="{{ $loop->first ? 'active' : '' }} {{ $productt->IsSizeColor($productt->size[$key]) ? str_replace(' ','',$productt->size[$key]) : ''  }} {{ $productt->size[$key] == $productt->size[0] ? 'show-colors' : '' }}">
                <span class="box" data-color="{{ $productt->color[$key] }}" style="background-color: {{ $productt->color[$key] }}">

                  <input type="hidden" class="size" value="{{ $productt->size[$key] }}">
                  <input type="hidden" class="size_qty" value="{{ $productt->size_qty[$key] }}">
                  <input type="hidden" class="size_key" value="{{$key}}">
                  <input type="hidden" class="size_price" value="{{ round($productt->size_price[$key] * $curr->value,2) }}">

                </span>
              </li>
            @endforeach
          </ul>
       </div>

    @endif

      {{-- PRODUCT COLOR SECTION ENDS  --}}
      @else
      @if(!empty($productt->size_all))
      <div class="product-size" data-key="false">
       <p class="title">{{ __('Size :') }}</p>
       <ul class="siz-list">
             @foreach(array_unique(explode(',',$productt->size_all)) as $key => $data1)
             <li class="{{ $loop->first ? 'active' : '' }}" data-key="{{ str_replace(' ','',$data1) }}">
                <span class="box">
                {{ $data1 }}
                <input type="hidden" class="size" value="{{$data1}}">
                <input type="hidden" class="size_key" value="{{$key}}">
                </span>
             </li>
             @endforeach
       </ul>
      </div>
      @endif
      @if(!empty($productt->color_all))
       <div class="product-color" data-key="false">
       <div class="title">{{ __('Color :') }}</div>
          <ul class="color-list">

                @foreach(explode(',',$productt->color_all) as $key => $color1)

                <li class="{{ $loop->first ? 'active' : '' }} show-colors">
                   <span class="box" data-color="{{ $color1 }}" style="background-color: {{ $color1 }}">
                   <input type="hidden" class="size_price" value="0">
                   </span>
                </li>
                @endforeach
          </ul>
       </div>
       @endif
  @endif
              <input type="hidden" id="product_price" value="{{ round($productt->vendorPrice() * $curr->value,2) }}">
              <input type="hidden" id="product_id" value="{{ $productt->id }}">
              <input type="hidden" id="curr_pos" value="{{ $gs->currency_format }}">
              <input type="hidden" id="curr_sign" value="{{ $curr->sign }}">
                  {{-- PRODUCT STOCK CONDITION SECTION  --}}

      @if(!empty($productt->size))
      <input type="hidden" id="stock" value="{{ $productt->size_qty[0] }}">
      @else
      @if(!$productt->emptyStock())
        <input type="hidden" id="stock" value="{{ $productt->stock }}">
      @elseif($productt->type != 'Physical')
        <input type="hidden" id="stock" value="0">
      @else
        <input type="hidden" id="stock" value="">
      @endif
    @endif
    @if($ps->deal_of_the_day==1)
    @if($productt->is_discount==1 && $productt->discount_date >= date('Y-m-d') && $productt->user->is_vendor==2)
    <div class="time-count time-box text-center my-30 flex-between w-75" data-countdown="{{ $productt['discount_date']}}"></div>
    @endif
    @endif
    {{-- PRODUCT STOCK CONDITION SECTION ENDS --}}
                         <div class="d-flex flex-wrap mt-3">
                            @if($productt->product_type != "affiliate" && $productt->type == 'Physical')
                               <div class="multiple-item-price m-1 me-3">
                                  <div class="qty">
                                     <ul class="qty-buttons">
                                     <li>
                                        <span class="qtminus">
                                           <i class="icofont-minus"></i>
                                        </span>
                                     </li>
                                     <li>
                                      <input class="qttotal" type="text" id="order-qty" value="{{ $productt->minimum_qty == null ? '1' : (int)$productt->minimum_qty }}">
                                      <input type="hidden" id="affilate_user" value="{{ $affilate_user }}">
                                      <input type="hidden" id="product_minimum_qty" value="{{ $productt->minimum_qty == null ? '0' : $productt->minimum_qty }}">
                                    </li>
                                     <li>
                                        <span class="qtplus">
                                           <i class="icofont-plus"></i>
                                        </span>
                                     </li>
                                     </ul>
                                  </div>
                               </div>
                          @endif


                          {{-- PRODUCT QUANTITY SECTION ENDS --}}
                          <ul>
                          @if($productt->product_type == "affiliate")

                              <li class="addtocart m-1">
                                <a  href="javascript:;" class="affilate-btn"  data-href="{{ $productt->affiliate_link }}" target="_blank"> {{ __('Buy Now') }}</a>
                              </li>
                              @else
                              @if($productt->emptyStock())
                              <li class="addtocart m-1">
                                <a href="javascript:;" class="cart-out-of-stock">

                                  {{ __('Out Of Stock') }}</a>
                              </li>
                              @else
                              <li class="addtocart m-1">
                                <a href="javascript:;" id="addcrt">{{ __('Add to Cart')}}</a>
                              </li>

                              <li class="addtocart m-1">
                                <a id="qaddcrt" href="javascript:;">
                                  {{ __('Buy Now') }}
                                </a>
                              </li>
                              @endif
                            </ul>
                         @endif
                   </div>
                      <div class="yith-wcwl-add-to-wishlist wishlist-fragment mt-3">
                          @if(Auth::check())
                          <div class="wishlist-button">
                              <a class="add_to_wishlist new" id="add-to-wish" href="javascript:;" data-href="{{ route('user-wishlist-add',$productt->id) }}"data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Add to Wishlist" aria-label="Add to Wishlist">{{ __('Wishlist') }}</a>
                          </div>
                          @else
                          <div class="wishlist-button">
                              <a class="add_to_wishlist" href="{{ route('user.login') }}" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Add to Wishlist" aria-label="Add to Wishlist">{{ __('Wishlist') }}</a>
                          </div>
                          @endif
                          <div class="compare-button">
                              <a class="compare button" data-href="{{ route('product.compare.add',$productt->id) }}" href="javascrit:;" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Compare" aria-label="Compare">{{ __('Compare') }}</a>
                          </div>

                      </div>


                       @if($gs->is_report)

    {{-- PRODUCT REPORT SECTION --}}

                  @if(Auth::guard('web')->check())

                  <div class="report-area">
                      <a class="report-item" href="javascript:;" data-bs-toggle="modal" data-bs-target="#report-modal"><i class="fas fa-flag"></i> {{__('Report This Item')}}</a>
                  </div>
                  @else
                  <div class="report-area">
                      <a class="report-item" href="{{route('user.login')}}"><i class="fas fa-flag"></i> {{__('Report This Item')}} </a>
                  </div>
                  @endif

    {{-- PRODUCT REPORT SECTION ENDS --}}

    @endif

                          <div class="my-4 social-linkss social-sharing a2a_kit a2a_kit_size_32">
                              <h5 class="mb-2">{{ __('Share Now') }}</h5>
                              <ul class="social-icons py-1 share-product social-linkss py-md-0">
                                  <li>
                                  <a class="facebook a2a_button_facebook" href="">
                                      <i class="fab fa-facebook-f"></i>
                                  </a>
                                  </li>
                                  <li>
                                  <a class="twitter a2a_button_twitter" href="">
                                      <i class="fab fa-twitter"></i>
                                  </a>
                                  </li>
                                  <li>
                                  <a class="linkedin a2a_button_linkedin" href="">
                                      <i class="fab fa-linkedin-in"></i>
                                  </a>
                                  </li>
                                  <li>
                                  <a class="pinterest a2a_button_pinterest" href="">
                                      <i class="fab fa-pinterest-p"></i>
                                  </a>
                                  </li>
                                  <li>
                                      <a class="instagram a2a_button_whatsapp" href="">
                                      <i class="fab fa-whatsapp"></i>
                                      </a>
                                  </li>
                              </ul>

                          </div>
                          <script async src="https://static.addtoany.com/menu/page.js"></script>

                          @if (!empty($productt->attributes))
                      @php
                        $attrArr = json_decode($productt->attributes, true);
                      @endphp
                    @endif
                    @if (!empty($attrArr))
                      <div class="product-attributes my-4">
                        <div class="row gy-4">
                        @foreach ($attrArr as $attrKey => $attrVal)
                          @if (array_key_exists("details_status",$attrVal) && $attrVal['details_status'] == 1)

                        <div class="col-lg-6">
                            <div class="form-group">
                              <strong class="text-capitalize mb-2 d-block">{{ str_replace("_", " ", $attrKey) }} :</strong>
                              <div class="">
                              @foreach ($attrVal['values'] as $optionKey => $optionVal)
                                <div class="custom-control custom-radio form-check">
                                  <input type="hidden" class="keys" value="">
                                  <input type="hidden" class="values" value="">
                                  <input type="radio" id="{{$attrKey}}{{ $optionKey }}" name="{{ $attrKey }}" class="form-check-input custom-control-input product-attr"  data-key="{{ $attrKey }}" data-price = "{{ $attrVal['prices'][$optionKey] * $curr->value }}" value="{{ $optionVal }}" {{ $loop->first ? 'checked' : '' }}>
                                  <label class="form-check-label" for="{{$attrKey}}{{ $optionKey }}">{{ $optionVal }}

                                  @if (!empty($attrVal['prices'][$optionKey]))
                                    +
                                    {{$curr->sign}} {{$attrVal['prices'][$optionKey] * $curr->value}}
                                  @endif
                                  </label>
                                </div>
                              @endforeach
                              </div>
                            </div>
                        </div>
                          @endif
                        @endforeach
                        </div>
                      </div>
                    @endif

                  </div>
              </div>
          </div>
          <div class="col-lg-3 col-md-4">
             <div class="pro-details-sidebar-item mb-4">
                <span>{{ __('Sold By') }}</span>
                <h5>@if( $productt->user_id  != 0)

                  @if(isset($productt->user))
                    {{ $productt->user->shop_name }}
                  @endif
                  @if($productt->user->checkStatus())
                  <br>
                  <a class="verify-link" href="javascript:;" data-toggle="tooltip" data-placement="top" title=""
                    data-original-title="{{ __('Verified') }}">
                    <i class="fas fa-check-circle"></i>
                  </a>
                  @endif
                @else
                {{ App\Models\Admin::find(1)->shop_name }}
                @endif</h5>
                @if( $productt->user_id  != 0)
                <h3>{{ App\Models\Product::where('user_id','=',$productt->user_id)->get()->count() }}</h3>
                @else
                <h3>{{ App\Models\Product::where('user_id','=',0)->get()->count() }}</h3>
                @endif
                <h6>{{ __('Total Items') }}</h6>

                @if( $productt->user_id  != 0)
              <li class="{{ $gs->is_contact_seller == 0 ? 'contact_seller' : '' }} cnt-sell">
                <a href="{{ route('front.vendor',str_replace(' ', '-', $productt->user->shop_name)) }}" class="view-stor btn--base">
                  <i class="icofont-ui-travel"></i>
                  {{ __('Visit Store') }}
                </a>
            </li>
            @endif

            {{-- Visit Store Ends--}}

            @if($gs->is_contact_seller == 1)

              {{-- Contact Seller --}}

              @if(Auth::check())

                @if($productt->user_id != 0)


                  <a class="view-stor btn--base" href="javascript:;" data-bs-toggle="modal" data-bs-target="#vendorform">
                    <i class="icofont-ui-chat"></i>
                    {{ __('Contact Seller') }}
                  </a>


                @else


                  <a class="view-stor btn--base" href="javascript:;" data-bs-toggle="modal" data-bs-target="#sendMessage">
                    <i class="icofont-ui-chat"></i>
                    {{ __('Contact Seller') }}
                  </a>


                @endif

              @else


              <a class="view-stor btn--base" href="{{ route('user.login') }}" >
                  <i class="icofont-ui-chat"></i>
                  {{ __('Contact Seller') }}
                </a>


              @endif

              {{-- Contact Seller Ends --}}

            @endif

<br>
            @if($productt->user_id != 0)
              @if(Auth::check())
                  @if(Auth::user()->favorites()->where('vendor_id','=',$productt->user_id)->get()->count() > 0)

                  <a class="fvrt btn--base" href="javascript:;">
                      <i class="icofont-check"></i>
                      {{ __('Favorite') }}
                  </a>
                  @else
                  <a class="view-stor favorite-prod btn--base" href="javascript:;" data-href="{{ route('user-favorite',[Auth::user()->id,$productt->user_id]) }}">
                      <i class="icofont-plus"></i>
                      {{ __('Add To Favorite Seller') }}
                  </a>
                  @endif

              @else

              <a class="view-stor btn--base" href="{{ route('user.login') }}" >
                <i class="icofont-plus"></i>
                {{ __('Add To Favorite Seller') }}
              </a>

              @endif
            @endif

            {{-- Favorite Seller Ends--}}
             </div>
             @if(!empty($productt->whole_sell_qty))
             <div class="pro-summary mb-4">
                <div class="price-summary">
                   <div class="price-summary-content">
                      <h5 class="text-center">{{ __('Wholesell') }}</h5>
                      <ul class="price-summary-list">
                            <li class="regular-price"> <h6>{{ __('Quantity') }}</h6>
                               <span>
                                  <span class="woocommerce-Price-amount amount"><h6>{{ __('Discount') }}</h6>
                               </span>
                               </span>
                            </li>
                            @foreach($productt->whole_sell_qty as $key => $data1)
                            <li class="selling-price"> <label>{{ $productt->whole_sell_qty[$key] }}+</label> <span><span class="woocommerce-Price-amount amount">{{ $productt->whole_sell_discount[$key] }}% {{ __('Off') }}
                               </span>
                               </span>
                            </li>
                            @endforeach
                      </ul>
                   </div>
                </div>
             </div>
             @endif



          </div>
      </div>
  </div>
</div>


{{-- MESSAGE MODAL --}}
{{-- MESSAGE MODAL --}}
<div class="message-modal">
  <div class="modal" id="vendorform" tabindex="-1" role="dialog" aria-labelledby="vendorformLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="vendorformLabel">{{ __('Send Message') }}</h5>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
        </div>
      <div class="modal-body">
        <div class="container-fluid p-0">
          <div class="row">
            <div class="col-md-12">
              <div class="contact-form">
                <form id="emailreply">
                  {{csrf_field()}}
                  <ul>

                    <li>
                      <input type="email" class="form-control border mb-1" id="eml" name="email" placeholder="{{ __('Email *') }}" required="">
                    </li>


                    <li>
                      <input type="text" class="form-control border mb-1" id="subj" name="subject" placeholder="{{ __('Subject *') }}" required="">
                    </li>

                    <li>
                      <textarea class="form-control textarea border mb-1" name="message" id="msg" placeholder="{{ __('Your Message *') }}" required=""></textarea>
                    </li>

                    <input type="hidden" name="name" value="{{ Auth::user() ? Auth::user()->name:'' }}">
                    <input type="hidden" name="user_id" value="{{ Auth::user() ? Auth::user()->id:'' }}">

                  </ul>
                  <button class="submit-btn" id="emlsub" type="submit">{{ __('Send Message') }}</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
      </div>
    </div>
  </div>
</div>

{{-- MESSAGE MODAL ENDS --}}

{{-- MESSAGE MODAL ENDS --}}

<div class="message-modal">
  <div class="modal show" id="sendMessage" tabindex="-1" role="dialog" aria-labelledby="sendMessageLabel" aria-modal="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="sendMessageLabel">{{ __('Send Message') }}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="container-fluid p-0">
            <div class="row">
              <div class="col-md-12">
                <div class="contact-form">
                <form action="{{ route('user-send-message') }}" class="emailreply">
                    @csrf
                    <ul>
                      <li>
                        <input type="text" class="input-field" name="subject" placeholder="{{ __('Subject *') }}" required="">
                      </li>
                      <li>
                        <textarea class="input-field textarea" name="message" placeholder="{{ __('Your Message') }}" required=""></textarea>
                      </li>
                      <input type="hidden" name="type" value="Ticket">
                    </ul>
                    <button class="submit-btn" type="submit">{{ __('Send Message') }}</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
