@extends('layouts.vendor')

@section('content')
                    <div class="content-area">
                            @if($user->checkWarning())
                                <div class="alert alert-danger validation text-center">
                                    <h3>{{ $user->displayWarning() }} </h3> <a href="{{ route('vendor-warning',$user->verifies()->where('admin_warning','=','1')->latest('id')->first()->id) }}"> {{ __('Verify Now') }} </a>
                                </div>
                            @endif
                        @include('alerts.form-success')
                        <div class="row row-cards-one">


                                <div class="col-md-12 col-lg-6 col-xl-4">
                                    <div class="mycard bg1">
                                        <div class="left">
                                            <h5 class="title">{{ __('Orders Pending!') }} </h5>
                                            <span class="number">{{ count($pending) }}</span>
{{--                                            <a href="{{route('vendor-order-index')}}" class="link">{{ __('View All') }}</a>--}}
                                        </div>
                                        <div class="right d-flex align-self-center">
                                            <div class="icon">
                                                <i class="icofont-dollar"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 col-lg-6 col-xl-4">
                                    <div class="mycard bg2">
                                        <div class="left">
                                            <h5 class="title">{{ __('Orders Processing!') }}</h5>
                                            <span class="number">{{ count($processing) }}</span>
{{--                                            <a href="{{route('vendor-order-index')}}" class="link">{{ __('View All') }}</a>--}}
                                        </div>
                                        <div class="right d-flex align-self-center">
                                            <div class="icon">
                                                <i class="icofont-truck-alt"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 col-lg-6 col-xl-4">
                                    <div class="mycard bg3">
                                        <div class="left">
                                            <h5 class="title">{{ __('Orders Completed!') }}</h5>
                                            <span class="number">{{ count($completed) }}</span>
{{--                                            <a href="{{route('vendor-order-index')}}" class="link">{{ __('View All') }}</a>--}}
                                        </div>
                                        <div class="right d-flex align-self-center">
                                            <div class="icon">
                                                <i class="icofont-check-circled"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 col-lg-6 col-xl-4">
                                    <div class="mycard bg4">
                                        <div class="left">
                                            <h5 class="title">{{ __('Total Products!') }}</h5>
                                            <span class="number">{{ count($user->products) }}</span>
{{--                                            <a href="{{route('vendor-prod-index')}}" class="link">{{ __('View All') }}</a>--}}
                                        </div>
                                        <div class="right d-flex align-self-center">
                                            <div class="icon">
                                                <i class="icofont-cart-alt"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-12 col-lg-6 col-xl-4">
                                    <div class="mycard bg5">
                                        <div class="left">
                                            <h5 class="title">{{ __('Total Item Sold!') }}</h5>
                                            <span class="number">{{ App\Models\VendorOrder::where('user_id','=',$user->id)->where('status','=','completed')->sum('qty') }}</span>
                                        </div>
                                        <div class="right d-flex align-self-center">
                                            <div class="icon">
                                                <i class="icofont-shopify"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-6 col-xl-4">
                                    <div class="mycard bg6">
                                        <div class="left">
                                            <h5 class="title">{{ __('Total Earnings!') }}</h5>
                                            <span class="number">{{ App\Models\Product::vendorConvertPrice( App\Models\VendorOrder::where('user_id','=',$user->id)->where('status','=','completed')->sum('price') ) }}</span>
                                        </div>
                                        <div class="right d-flex align-self-center">
                                            <div class="icon">
                                               <i class="icofont-dollar-true"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        <div class="row row-cards-one">

                            <div class="col-md-12 col-lg-12 col-xl-12">
                                <div class="card">
                                    <h5 class="card-header">{{ __('Recent Order(s)') }}</h5>
                                    <div class="card-body">

                                        <div class="my-table-responsiv">
                                            <table class="table table-hover dt-responsive" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>

                                                        <th>{{ __('Order Number') }}</th>
                                                        <th>{{ __('Order Date') }}</th>
                                                        <th>{{__('Order Status')}}</th>
                                                        <th>{{ __('Action') }}</th>
                                                    </tr>
                                                    @foreach($rorders as $data)
                                                    <tr>

                                                        <td>{{ $data->order_number }}</td>
                                                        <td>{{ date('Y-m-d',strtotime($data->created_at)) }}</td>
                                                        <td>
                                                            <div>
                                                                @if($data->status == 'completed')
                                                                    <span class="badge badge-success">{{ __('Completed') }}</span>
                                                                @elseif($data->status == 'processing')
                                                                    <span class="badge badge-info">{{ __('Processing') }}</span>
                                                                @elseif($data->status == 'pending')
                                                                    <span class="badge badge-warning">{{ __('Pending') }}</span>
                                                                @elseif($data->status == 'declined')
                                                                    <span class="badge badge-danger">{{ __('Declined') }}</span>
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="action-list"><a href="{{ route('admin-order-show',$data->id) }}"><i
                                                                        class="fas fa-eye"></i> {{ __('Details') }}</a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="row row-cards-one">

                            <div class="col-md-12 col-lg-12 col-xl-12">
                                <div class="card">
                                    <h5 class="card-header">{{ __('Total Sales in Last 30 Days') }}</h5>
                                    <div class="card-body">

                                        <canvas id="lineChart"></canvas>

                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

@endsection

@section('scripts')

<script type="text/javascript">

    (function($) {
		"use strict";

    displayLineChart();

    function displayLineChart() {
        var data = {
            labels: [
            {!!$days!!}
            ],
            datasets: [{
                label: "Prime and Fibonacci",
                fillColor: "#3dbcff",
                strokeColor: "#0099ff",
                pointColor: "rgba(220,220,220,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(220,220,220,1)",
                data: [
                {!!$sales!!}
                ]
            }]
        };
        var ctx = document.getElementById("lineChart").getContext("2d");
        var options = {
            responsive: true
        };
        var lineChart = new Chart(ctx).Line(data, options);
    }

    $('#poproducts').dataTable( {
      "ordering": false,
          'lengthChange': false,
          'searching'   : false,
          'ordering'    : false,
          'info'        : false,
          'autoWidth'   : false,
          'responsive'  : true,
          'paging'  : false
    } );

    $('#pproducts').dataTable( {
      "ordering": false,
      'lengthChange': false,
          'searching'   : false,
          'ordering'    : false,
          'info'        : false,
          'autoWidth'   : false,
          'responsive'  : true,
          'paging'  : false
    } );



    })(jQuery);

</script>

@endsection
