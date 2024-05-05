@extends('layouts.admin') 

@section('content')  

					<div class="content-area">

						<div class="mr-breadcrumb">
							<div class="row">
								<div class="col-lg-12">
									<h4 class="heading">{{ __("Addons") }}</h4>
									<ul class="links">
										<li>
											<a href="{{ route('admin.dashboard') }}">{{ __("Dashboard") }} </a>
										</li>
										<li>
											<a href="{{ route('admin-addon-index') }}">{{ __("Addons") }}</a>
										</li>
									</ul>
								</div>
							</div>
						</div>

						<div class="product-area">
							<div class="row">
								<div class="col-lg-12">
									<div class="mr-table allproduct">
                        				@include('alerts.form-success')  
										<div class="table-responsive">
												<table id="geniustable" class="table table-hover dt-responsive" cellspacing="0" width="100%">
													<thead>
														<tr>
                                                            <th class="pl-2">{{ __("Name") }}</th>
                                                            <th class="pl-2">{{ __("Keyword") }}</th>
                                                            <th class="pl-2">{{ __("Installation Date") }}</th>
														</tr>
													</thead>
												</table>
										</div>
									</div>
								</div>
							</div>
						</div>
						
					</div>


			{{-- DELETE MODAL --}}

			<div class="modal fade" id="confirm-status">
				<div class="modal-dialog">
				<div class="modal-content">
			
					<!-- Modal Header -->
					<div class="modal-header text-center">
					<h4 class="modal-title w-100">{{ __('Confirm Uninstall') }}</h4>
					</div>
			
					<!-- Modal body -->
					<div class="modal-body">
						<p class="text-center">{{ __('You are about to uninstall this Addon.') }}</p>
						<p class="text-center">{{ __('Do you want to proceed?') }}</p>
					</div>
			
					<!-- Modal footer -->
					<div class="modal-footer justify-content-center">
						<button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Cancel') }}</button>
						<a  class="btn btn-success btn-ok">{{ __('Uninstall') }}</a>
					</div>
			
				</div>
				</div>
			</div>
			
			{{-- DELETE MODAL ENDS --}}

@endsection    

@section('scripts')

    <script type="text/javascript">

(function($) {
		"use strict";

		$('#geniustable').DataTable({
			ordering: false,
			processing: true,
			serverSide: true,
			ajax: '{{ route('admin-addon-datatables') }}',
			columns: [
					{ data: 'name', name: 'name' },
					{ data: 'uninstall_files', name: 'uninstall_files' },
					{ data: 'created_at', name: 'created_at' }
					],
			language : {
				processing: '<img src="{{asset('assets/images/'.$gs->admin_loader)}}">'
			}
        });								

		$(function() {
			$(".btn-area").append('<div class="col-sm-4 table-contents">'+
				'<a class="add-btn" href="{{route('admin-addon-create')}}">'+
			'<i class="fas fa-upload"></i> {{ __('Install New Addon') }}'+
			'</a>'+
			'</div>');
      	});	

})(jQuery);

	</script>
	
@endsection   