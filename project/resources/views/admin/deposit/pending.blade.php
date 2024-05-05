@extends('layouts.admin') 

@section('content')  
					<div class="content-area">

						<div class="mr-breadcrumb">
							<div class="row">
								<div class="col-lg-12">
										<h4 class="heading">{{ __('Pending Deposits') }}</h4>
										<ul class="links">
											<li>
												<a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }} </a>
											</li>
											<li>
												<a href="javascript:;">{{ __('Customer Deposits') }} </a>
											</li>
											<li>
												<a href="{{ route('admin-sociallink-index') }}">{{ __('Pending Deposits') }}</a>
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

{{-- STATUS MODAL --}}

<div class="modal fade" id="status-modal" tabindex="-1" role="dialog" aria-labelledby="modal1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
  
      <div class="modal-header d-block text-center">
          <h4 class="modal-title d-inline-block">{{ __("Update Status") }}</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
      </div>
  
        <!-- Modal body -->
        <div class="modal-body">
              <p class="text-center">{{ __("You are about to change the status of this deposit. If you select completed, you won't be able to change it again.") }}</p>
              <p class="text-center">{{ __("Do you want to proceed?") }}</p>
        </div>
  
        <!-- Modal footer -->
        <div class="modal-footer justify-content-center">
              <button type="button" class="btn btn-default" data-dismiss="modal">{{ __("Cancel") }}</button>
              <a class="btn btn-success btn-ok">{{ __("Update") }}</a>
        </div>
  
      </div>
    </div>
</div>
  
  {{-- STATUS MODAL ENDS --}}

@endsection    

@section('scripts')

    <script type="text/javascript">



		var table = $('#geniustable').DataTable({
			   ordering: false,
               processing: true,
               serverSide: true,
               ajax: '{{ route('admin-user-deposit-datatables','0') }}',
               columns: [
                        { data: 'name', name: 'name' },
                        { data: 'amount', name: 'amount' },
                        { data: 'method', name: 'method' },
                        { data: 'txnid', name: 'txnid' },
            			{ data: 'action', searchable: false, orderable: false }

                     ],
               language: {
                	processing: '<img src="{{asset('assets/images/'.$gs->admin_loader)}}">'
                },
			   drawCallback : function( settings ) {
	    				$('.select').niceSelect();	
			   }
            });



	</script>
	
@endsection   