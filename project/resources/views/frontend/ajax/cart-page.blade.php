 <!--==================== Cart Section Start ====================-->
 <div class="full-row cartpage">
    <div class="container">
        <div class="row">
            @if(Session::has('cart'))
            <div class="col-xl-8 col-lg-12 col-md-12 col-12">
                <div class="cart-table">
                    <div class="gocover" style="position: absolute; background: url({{ asset('assets/images/xloading.gif') }}) center center no-repeat scroll rgba(255, 255, 255, 0.5); display: none;"></div>
                    <table class="shop_table cart">
                        <tr>
                            <th class="product-thumbnail">&nbsp;</th>
                            <th class="product-name">{{ __('Product') }}</th>
                            <th class="product-price">{{ __('Price') }}</th>
                            <th class="product-quantity">{{ __('Quantity') }}</th>
                            <th class="product-subtotal">{{ __('Subtotal') }}</th>
                            <th class="product-remove">&nbsp;</th>
                        </tr>

                        @foreach ($products as $product)
                        <tr class="woocommerce-cart-form__cart-item cart_item">
                            <td class="product-thumbnail">
                                <a href="{{ route('front.product', $product['item']['slug']) }}"><img src="{{ $product['item']['photo'] ? asset('assets/images/products/'.$product['item']['photo']) : asset('assets/images/noimage.png') }}" alt="Product image"></a>
                            </td>
                            <td class="product-name">
                                <a href="{{ route('front.product', $product['item']['slug']) }}">{{ mb_strlen($product['item']['name'],'UTF-8') > 35 ? mb_substr($product['item']['name'],0,35,'UTF-8').'...' : $product['item']['name']}}</a>
                                @if(!empty($product['color']))
                                <div class="d-flex mt-2 ml-1">

                                <b>{{ __('Colour') }}</b>:  <span id="color-bar" style="border: 10px solid #{{$product['color'] == "" ? "white" : $product['color']}};"></span>
                                </div>
                                @endif
                            </td>
                            <td class="product-price">
                                <span>{{ App\Models\Product::convertPrice($product['item_price']) }}
                                </span>
                            </td>


                            @if($product['item']['type'] == 'Physical' && $product['item']['type'] != 'affiliate')
                            <td class="product-quantity">
                                <input type="hidden" class="prodid" value="{{$product['item']['id']}}">
                                <input type="hidden" class="itemid"
                                    value="{{$product['item']['id'].$product['size'].$product['color'].str_replace(str_split(' ,'),'',$product['values'])}}">
                                <input type="hidden" class="size_qty" value="{{$product['size_qty']}}">
                                <input type="hidden" class="size_price" value="{{$product['size_price']}}">
                                <input type="hidden" class="minimum_qty" value="{{ $product['item']['minimum_qty'] == null ? '0' : $product['item']['minimum_qty'] }}">
                                <div class="quantity">
                                    <input id="qty{{$product['item']['id'].$product['size'].$product['color'].str_replace(str_split(' ,'),'',$product['values'])}}" class="qttotal1" type="number" name="quantity"  value="{{ $product['qty'] }}">
                                </div>
                            </td>
                            @else
                            <td class="product-quantity">
                                1
                            </td>
                            @endif
                            @if($product['size_qty'])
                            <input type="hidden"
                                id="stock{{$product['item']['id'].$product['size'].$product['color'].str_replace(str_split(' ,'),'',$product['values'])}}"
                                value="{{$product['size_qty']}}">
                            @elseif($product['item']['type'] != 'Physical')
                            <input type="hidden"
                                id="stock{{$product['item']['id'].$product['size'].$product['color'].str_replace(str_split(' ,'),'',$product['values'])}}"
                                value="1">
                            @else
                            <input type="hidden"
                                id="stock{{$product['item']['id'].$product['size'].$product['color'].str_replace(str_split(' ,'),'',$product['values'])}}"
                                value="{{$product['stock']}}">
                            @endif
                            <td class="product-subtotal">
                                <p class="d-inline-block"
                                id="prc{{$product['item']['id'].$product['size'].$product['color'].str_replace(str_split(' ,'),'',$product['values'])}}">
                                {{ App\Models\Product::convertPrice($product['price']) }}
                                </p>
                                @if ($product['discount'] != 0)
                                <strong>{{$product['discount']}} %{{__('off')}}</strong>
                                @endif

                            </td>
                            <td class="product-remove">
                                <a href="#" class="remove cart-remove" data-class="cremove{{ $product['item']['id'].$product['size'].$product['color'].str_replace(str_split(' ,'),'',$product['values']) }}" data-href="{{ route('product.cart.remove',$product['item']['id'].$product['size'].$product['color'].str_replace(str_split(' ,'),'',$product['values'])) }}">×</a>
                            </td>
                        </tr>
                       @endforeach
                    </table>
                </div>
            </div>
            <div class="col-xl-4 col-lg-12 col-md-12 col-12">
                <div class="cart-collaterals">
                    <div class="cart_totals ">
                        <h2>{{ __('Cart totals') }}</h2>
                        <table>
                            <tr>
                                <th>{{__('Subtotal')}}</th>
                                <td>
                                    <span><b
                                        class="cart-total">{{ Session::has('cart') ? App\Models\Product::convertPrice($totalPrice) : '0.00' }}</b>
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <th>{{ __('Discount') }}</th>
                                <td>
                                    <span>
                                        <b class="discount">{{ App\Models\Product::convertPrice(0)}}</b>
                                        <input type="hidden" id="d-val" value="{{ App\Models\Product::convertPrice(0)}}">
                                    </span>
                                </td>
                            </tr>

                            <tr class="order-total">
                                <th>Total</th>
                                <td><strong><span class="woocommerce-Price-amount amount main-total">{{ Session::has('cart') ? App\Models\Product::convertPrice($mainTotal) : '0.00' }}</span></strong> </td>
                            </tr>
                        </table>
                        <div class="wc-proceed-to-checkout">
                            <a href="{{ route('front.checkout') }}" class="checkout-button">{{ __('Proceed to checkout') }}</a>
                        </div>
                    </div>
                </div>
            </div>


            @else
            <div class="col-xl-12 col-lg-12 col-md-12 col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="text-center">{{ __('Cart is Empty!! Add some products in your Cart') }}</h4>
                    </div>
                </div>
            </div>



            @endif
        </div>
    </div>
</div>

<script src="{{ asset('assets/front/js/custom.js') }}"></script>
