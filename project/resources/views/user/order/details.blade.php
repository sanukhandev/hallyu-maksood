@extends('layouts.front')

@section('content')
@include('partials.global.common-header')

 <!-- breadcrumb -->
 <!-- <div class="full-row bg-light overlay-dark py-5" style="background-image: url({{ $gs->breadcrumb_banner ? asset('assets/images/'.$gs->breadcrumb_banner):asset('assets/images/noimage.png') }}); background-position: center center; background-size: cover;">
    <div class="container">
        <div class="row text-center text-white">
            <div class="col-12">
                <h3 class="mb-2 text-white">{{ __('Purchased Items') }}</h3>
            </div>
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 d-inline-flex bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="{{ ('user-dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('Purchased Items') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div> -->
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
                <div class="row">
                    <div class="col-lg-12">
                        <div class="widget border-0 p-40 widget_categories bg-light account-info">
                             <div class="process-steps-area">
                                @include('partials.user.order-process')

                            </div>

                            <h4 class="widget-title down-line mb-30">{{ __('Purchased Items') }}</h4>
                            <div class="view-order-page">
                                <h3 class="order-code">{{ __('Order#') }} {{$order->order_number}} [{{$order->status}}]
                                </h3>
                                <div class="print-order text-right">
                                    <a href="{{route('user-order-print',$order->id)}}" target="_blank"
                                        class="print-order-btn">
                                        <i class="fa fa-print"></i> {{ __('Print Order') }}
                                    </a>
                                </div>
                                <p class="order-date">{{ __('Order Date') }} {{date('d-M-Y',strtotime($order->created_at))}}
                                </p>

                                @if($order->dp == 1)

                                <div class="billing-add-area">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>{{ __('Shipping Address') }}</h5>
                                            <address>
                                                {{ __('Name:') }} {{$order->customer_name}}<br>
                                                {{ __('Email:') }} {{$order->customer_email}}<br>
                                                {{ __('Phone:') }} {{$order->customer_phone}}<br>
                                                {{ __('Address:') }} {{$order->customer_address}}<br>
                                                {{$order->customer_city}}-{{$order->customer_zip}}
                                            </address>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>{{ __('Shipping Method') }}</h5>

                                            <p>{{ __('Payment Status:') }}
                                                @if($order->payment_status == 'Pending')
                                                    <span class='badge badge-danger'>{{ __('Unpaid') }}</span>
                                                @else
                                                    <span class='badge badge-success'>{{ __('Paid') }}</span>
                                                @endif
                                            </p>

                                            <p>{{ __('Tax :') }}
                                                {{ \PriceHelper::showOrderCurrencyPrice((($order->tax) / $order->currency_value),$order->currency_sign) }}
                                            </p>
                                            <p>{{ __('Paid Amount:') }}
                                                {{ \PriceHelper::showOrderCurrencyPrice(($order->pay_amount  * $order->currency_value),$order->currency_sign) }}
                                            </p>
                                            <p>{{ __('Payment Method:') }} {{$order->method}}</p>

                                            @if($order->method != "Cash On Delivery")
                                            @if($order->method=="Stripe")
                                            {{ $order->method }} {{ __('Charge ID:') }} <p>{{$order->charge_id}}</p>
                                            @endif
                                            {{ $order->method }} {{ __('Transaction ID:') }} <p id="ttn">{{ $order->txnid }}</p>
                                            <a id="tid" style="cursor: pointer;" class="mybtn2">{{ __('Edit Transaction ID')}}</a>

                                            <form id="tform">
                                                <input style="display: none; width: 100%;" type="text" id="tin" placeholder="{{ __('Enter Transaction ID & Press Enter') }}" required="" class="mb-3">
                                                <input type="hidden" id="oid" value="{{ $order->id }}">

                                                <button style="display: none; padding: 5px 15px; height: auto; width: auto; line-height: unset;" id="tbtn" type="submit" class="mybtn1">{{ __('Submit') }}</button>
                                                <a style="display: none; cursor: pointer;  padding: 5px 15px; height: auto; width: auto; line-height: unset;" id="tc"  class="mybtn1">{{ __('Cancel') }}</a>
                                                    {{-- Change 1 --}}
                                            </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @else
                                <div class="shipping-add-area">
                                    <div class="row">
                                        <div class="col-md-6">
                                            @if($order->shipping == "shipto")
                                            <h5>{{ __('Shipping Address') }}</h5>
                                            <address>
                                                {{ __('Name:') }}
                                                {{$order->shipping_name == null ? $order->customer_name : $order->shipping_name}}<br>
                                                {{ __('Email:') }}
                                                {{$order->shipping_email == null ? $order->customer_email : $order->shipping_email}}<br>
                                                {{ __('Phone:') }}
                                                {{$order->shipping_phone == null ? $order->customer_phone : $order->shipping_phone}}<br>
                                                {{ __('Address:') }}
                                                {{$order->shipping_address == null ? $order->customer_address : $order->shipping_address}}<br>
                                                {{$order->shipping_city == null ? $order->customer_city : $order->shipping_city}}-{{$order->shipping_zip == null ? $order->customer_zip : $order->shipping_zip}}
                                            </address>
                                            @else
                                            <h5>{{ __('PickUp Location') }}</h5>
                                            <address>
                                                {{ __('Address:') }} {{$order->pickup_location}}<br>
                                            </address>
                                            @endif

                                        </div>
                                        <div class="col-md-6">
                                            <h5>{{ __('Shipping Method') }}</h5>
                                            @if($order->shipping == "shipto")
                                            <p>{{ __('Ship To Address') }}</p>
                                            @else
                                            <p>{{ __('Pick Up') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="billing-add-area">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>{{ __('Billing Address') }}</h5>
                                            <address>
                                                {{ __('Name:') }} {{$order->customer_name}}<br>
                                                {{ __('Email:') }} {{$order->customer_email}}<br>
                                                {{ __('Phone:') }} {{$order->customer_phone}}<br>
                                                {{ __('Address:') }} {{$order->customer_address}}<br>
                                                {{$order->customer_city}}-{{$order->customer_zip}}
                                            </address>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>{{ __('Payment Information') }}</h5>

                                            <p>{{ __('Payment Status') }}
                                                @if($order->payment_status == 'Pending')
                                                    <span class='badge badge-danger'>{{ __('Unpaid') }}</span>
                                                @else
                                                    <span class='badge badge-success'>{{ __('Paid') }}</span>
                                                @endif
                                            </p>

                                            <p>{{ __('Tax :') }}
                                                {{ \PriceHelper::showOrderCurrencyPrice((($order->tax) / $order->currency_value),$order->currency_sign) }}
                                            </p>
                                            <p>{{ __('Paid Amount:') }}
                                                {{ \PriceHelper::showOrderCurrencyPrice(($order->pay_amount  * $order->currency_value),$order->currency_sign) }}
                                            </p>
                                            <p>{{ __('Payment Method:') }} {{$order->method}}</p>

                                            @if($order->method != "Cash On Delivery")
                                            @if($order->method=="Stripe")
                                            {{$order->method}} {{ __('Charge ID:') }} <p>{{$order->charge_id}}</p>
                                            @endif
                                            {{$order->method}} {{ __('Transaction ID:') }} <p id="ttn"> {{$order->txnid}}</p>

                                            <a id="tid" style="cursor: pointer;" class="mybtn2">{{ __('Edit Transaction ID') }}</a>

                                            <form id="tform">
                                                <input style="display: none; width: 100%;" type="text" id="tin" placeholder="{{ __('Enter Transaction ID & Press Enter') }}" required="" class="mb-3">
                                                <input type="hidden" id="oid" value="{{$order->id}}">

                                                <button style="display: none; padding: 5px 15px; height: auto; width: auto; line-height: unset;" id="tbtn" type="submit" class="mybtn1">{{ __('Submit') }}</button>

                                                    <a style="display: none; cursor: pointer;  padding: 5px 15px; height: auto; width: auto; line-height: unset;" id="tc"  class="mybtn1">{{ __('Cancel') }}</a>

                                                    {{-- Change 1 --}}
                                            </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <br>




                                <div class="table-responsive">
                                    <h5>{{ __('Ordered Products:') }}</h5>
                                    <table class="table veiw-details-table">
                                        <thead>
                                            <tr>
                                                <th>{{ __('ID#') }}</th>
                                                <th>{{ __('Name') }}</th>
                                                <th>{{ __('Details') }}</th>
                                                <th>{{ __('Price') }}</th>
                                                <th>{{ __('Total') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($cart['items'] as $product)
                                            <tr>
                                                <td data-label="{{ __('ID#') }}">
                                                    <div>
                                                        {{ $product['item']['id'] }}
                                                    </div>
                                                </td>
                                                <td data-label="{{ __('Name') }}">
                                                    <div>
                                                        
                                                    <input type="hidden" value="{{ $product['license'] }}">

                                                    @if($product['item']['user_id'] != 0)
                                                    @php
                                                    $user = App\Models\User::find($product['item']['user_id']);
                                                    @endphp
                                                    @if(isset($user))
                                                    <a target="_blank"
                                                        href="{{ route('front.product', $product['item']['slug']) }}">{{mb_strlen($product['item']['name'],'UTF-8') > 50 ? mb_substr($product['item']['name'],0,50,'UTF-8').'...' : $product['item']['name']}}</a>
                                                    @else
                                                    <a target="_blank"
                                                        href="{{ route('front.product', $product['item']['slug']) }}">
                                                        {{mb_strlen($product['item']['name'],'UTF-8') > 50 ? mb_substr($product['item']['name'],0,50,'UTF-8').'...' : $product['item']['name']}}
                                                    </a>
                                                    @endif
                                                    @else

                                                    <a target="_blank"
                                                        href="{{ route('front.product', $product['item']['slug']) }}">
                                                        {{mb_strlen($product['item']['name'],'UTF-8') > 50 ? mb_substr($product['item']['name'],0,50,'UTF-8').'...' : $product['item']['name']}}
                                                    </a>

                                                    @endif
                                                    @if($product['item']['type'] != 'Physical' && $product['item']['type'] != 'License')
                                                    @if($order->payment_status == 'Completed')
                                                    
                                                    @if($product['item']['file'] != null)
                                                    <a href="{{ route('user-order-download',['slug' => $order->order_number , 'id' => $product['item']['id']]) }}"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="fa fa-download"></i> {{ __('Download') }}
                                                    </a>
                                                    @else
                                                    <a target="_blank" href="{{ $product['item']['link'] }}"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="fa fa-download"></i> {{ __('Download') }}
                                                    </a>
                                                    @endif
                                                    @endif
                                                    @endif
                                                    @if($product['license'] != '')
                                                    <a href="javascript:;" data-toggle="modal" data-target="#confirm-delete"
                                                       class="btn btn-sm mybtn1 product-btn" id="license"><i
                                                           class="fa fa-eye"></i> Your License : {{$product['license']}}</a>
                                                    @endif
                                                    </div>
                                                </td>
                                                <td data-label="{{ __('Details') }}">
                                                    <div>
                                                            
                                                        <b>{{ __('Quantity') }}</b>: {{$product['qty']}} <br>
                                                        @if(!empty($product['size']))
                                                        <b>{{ __('Size') }}</b>: {{ $product['item']['measure'] }}{{str_replace('-',' ',$product['size'])}} <br>
                                                        @endif
                                                        @if(!empty($product['color']))
                                                        <div class="d-flex mt-2">
                                                        <b>{{ __('Color') }}</b>:  <span id="color-bar" style="width: 20px; height: 20px; display: inline-block; vertical-align: middle; border-radius: 50%; background: #{{$product['color']}};"></span>
                                                        </div>
                                                        @endif

                                                            @if(!empty($product['keys']))

                                                            @foreach( array_combine(explode(',', $product['keys']), explode(',', $product['values']))  as $key => $value)

                                                                <b>{{ ucwords(str_replace('_', ' ', $key))  }} : </b> {{ $value }} <br>
                                                            @endforeach

                                                            @endif
                                                    </div>

                                                      </td>
                                                <td data-label="{{ __('Price') }}">
                                                    <div>
                                                        {{ \PriceHelper::showCurrencyPrice(($product['item_price'] ) * $order->currency_value) }}
                                                    </div>
                                                </td>
                                                <td data-label="{{ __('Total') }}">
                                                    <div>
                                                        {{ \PriceHelper::showCurrencyPrice(($product['item_price'] ) * $order->currency_value) }} <small>{{ $product['discount'] == 0 ? '' : '('.$product['discount'].'% '.__('Off').')' }}</small></small>
                                                    </div>
                                                </td>

                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>


                            <a class="back-btn theme-bg" href="{{ route('user-orders') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!--==================== Blog Section End ====================-->

{{-- Modal --}}
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="modal1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header d-block text-center">
                <h4 class="modal-title d-inline-block">{{ __('License Key') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <p class="text-center">{{ __('The Licenes Key is :') }} <span id="key"></span></p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

@includeIf('partials.global.common-footer')

@endsection
@section('script')
<script type="text/javascript">

    (function($) {
            "use strict";

        $('#example').dataTable({
            "ordering": false,
            'paging': false,
            'lengthChange': false,
            'searching': false,
            'ordering': false,
            'info': false,
            'autoWidth': false,
            'responsive': true
        });

    })(jQuery);

    </script>
    <script>

    (function($) {
            "use strict";

        $(document).on("click", "#tid", function (e) {
            $(this).hide();
            $("#tc").show();
            $("#tin").show();
            $("#tbtn").show();
        });
        $(document).on("click", "#tc", function (e) {
            $(this).hide();
            $("#tid").show();
            $("#tin").hide();
            $("#tbtn").hide();
        });
        $(document).on("submit", "#tform", function (e) {
            var oid = $("#oid").val();
            var tin = $("#tin").val();
            $.ajax({
                type: "GET",
                url: "{{URL::to('user/json/trans')}}",
                data: {
                    id: oid,
                    tin: tin
                },
                success: function (data) {
                    $("#ttn").html(data);
                    $("#tin").val("");
                    $("#tid").show();
                    $("#tin").hide();
                    $("#tbtn").hide();
                    $("#tc").hide();
                }
            });
            return false;
        });

    })(jQuery);

    </script>
    <script type="text/javascript">

    (function($) {
            "use strict";

        $(document).on('click', '#license', function (e) {
            var id = $(this).parent().find('input[type=hidden]').val();
            $('#key').html(id);
        });

    })(jQuery);
</script>
@endsection

