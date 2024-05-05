@extends('layouts.admin') 

@section('content')  
					<div class="content-area">

						<div class="mr-breadcrumb">
							<div class="row">
								<div class="col-lg-12">
										<h4 class="heading">{{ __('Completed Deposits') }}</h4>
										<ul class="links">
											<li>
												<a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }} </a>
											</li>
											<li>
												<a href="javascript:;">{{ __('Customer Deposits') }} </a>
											</li>
											<li>
												<a href="{{ route('admin-sociallink-index') }}">{{ __('Completed Deposits') }}</a>
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
									                        <th>{{ __('Customer Name') }}</th>
															<th>{{ __('Amount') }}</th>
									                        <th>{{ __('Payment Method') }}</th>
									                        <th>{{ __('Transaction ID') }}</th>
									                        <th>{{ __('Status') }}</th>
														</tr>
													</thead>
												</table>
										</div>
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

		var table = $('#geniustable').DataTable({
			   ordering: false,
               processing: true,
               serverSide: true,
               ajax: '{{ route('admin-user-deposit-datatables','1') }}',
               columns: [
                        { data: 'name', name: 'name' },
                        { data: 'amount', name: 'amount' },
                        { data: 'method', name: 'method' },
                        { data: 'txnid', name: 'txnid' },
            			{ data: 'action', searchable: false, orderable: false }

                     ],
               language: {
                	processing: '<img src="{{asset('assets/images/'.$gs->admin_loader)}}">'
                }
            });


	})(jQuery);		

	</script>
	
@endsection   