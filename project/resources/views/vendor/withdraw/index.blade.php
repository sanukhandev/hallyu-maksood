@extends('layouts.vendor')

@section('content')

					<div class="content-area">
						<div class="mr-breadcrumb">
							<div class="row">
								<div class="col-lg-12">
										<h4 class="heading">{{ __('My Withdraws') }}</h4>
										<ul class="links">
											<li>
												<a href="{{ route('vendor.dashboard') }}">{{ __('Dashboard') }} </a>
											</li>
											<li>
												<a href="{{ route('vendor-wt-index') }}">{{ __('My Withdraws') }}</a>
											</li>
										</ul>
								</div>
							</div>
						</div>
						<div class="product-area">
							<div class="row">
								<div class="col-lg-12">
									<div class="mr-table allproduct">
										@include('alerts.admin.form-success')
										<div class="table-responsive">
												<table id="geniustable" class="table table-hover dt-responsive" cellspacing="0" width="100%">
													<thead>
														<tr>
						                                <th>{{ __('Withdraw Date') }}</th>
						                                <th>{{ __('Method') }}</th>
						                                <th>{{ __('Account') }}</th>
						                                <th>{{ __('Amount') }}</th>
						                                <th>{{ __('Status') }}</th>
														</tr>
													</thead>

												<tbody>
													@foreach($withdraws as $withdraw)
														<tr>
															<td>{{date('d-M-Y',strtotime($withdraw->created_at))}}</td>
															<td>{{$withdraw->method}}</td>
															@if($withdraw->method != "Bank")
																<td>{{$withdraw->acc_email}}</td>
															@else
																<td>{{$withdraw->iban}}</td>
															@endif
															<td>{{$sign->sign}}{{ round($withdraw->amount * $sign->value , 2) }}</td>
															<td>{{ucfirst($withdraw->status)}}</td>
														</tr>
													@endforeach
												</tbody>

												</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

@endsection



@section('scripts')

{{-- DATA TABLE --}}


    <script type="text/javascript">

(function($) {
		"use strict";

		var table = $('#geniustable').DataTable({
			ordering:false
		});

      	$(function() {
        $(".btn-area").append('<div class="col-sm-4 text-right">'+
        	'<a class="add-btn" href="{{route('vendor-wt-create')}}">'+
          '<i class="fas fa-plus"></i> {{ __('Withdraw Now') }}'+
          '</a>'+
          '</div>');
      });

	})(jQuery);

    </script>

{{-- DATA TABLE --}}

@endsection
