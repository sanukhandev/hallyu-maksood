@extends('layouts.admin') 

@section('content')  
                    <input type="hidden" id="headerdata" value="{{ __("SUBSCRIPTIONS") }}">
                    <div class="content-area">
                        <div class="mr-breadcrumb">
                            <div class="row">
                                <div class="col-lg-12">
                                        <h4 class="heading">{{ __("Pending Vendor Subscriptions") }}</h4>
                                        <ul class="links">
                                            <li>
                                                <a href="{{ route('admin.dashboard') }}">{{ __("Dashboard") }} </a>
                                            </li>
                                            <li>
                                                <a href="javascript:;">{{ __("Vendors") }}</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('admin-vendor-subs','pending') }}">{{ __("Pending Vendor Subscriptions") }}</a>
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
                                                            <th width="15%">{{ __("Vendor Name") }}</th>
                                                            <th width="15%">{{ __("Plan") }}</th>
                                                            <th width="15%">{{ __("Method") }}</th>
                                                            <th width="15%">{{ __("Transcation ID") }}</th>
                                                            <th width="15%">{{ __("Purchase Time") }}</th>
                                                            <th>{{ __("Actions") }}</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

{{-- ADD / EDIT MODAL --}}

            <div class="modal fade" id="modal1" tabindex="-1" role="dialog" aria-labelledby="modal1" aria-hidden="true">
                                        
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="submit-loader">
                                <img  src="{{asset('assets/images/'.$gs->admin_loader)}}" alt="">
                            </div>
                            <div class="modal-header">
                                <h5 class="modal-title"></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __("Close") }}</button>
                            </div>
                        </div>
                    </div>

                </div>

{{-- ADD / EDIT MODAL ENDS --}}


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
              <p class="text-center">{{ __("You are about to change the status of this subscription. If you select completed, you won't be able to change it again.") }}</p>
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

{{-- DATA TABLE --}}

    <script type="text/javascript">

(function($) {
		"use strict";

        var table = $('#geniustable').DataTable({
               ordering: false,
               processing: true,
               serverSide: true,
               ajax: '{{ route('admin-vendor-subs-datatables','0') }}',
               columns: [
                        { data: 'name', searchable: false, orderable: false },
                        { data: 'title', name: 'title' },
                        { data: 'method', name: 'method' },
                        { data: 'txnid', name: 'txnid' },
                        { data: 'created_at', name: 'created_at' },
                        { data: 'action', searchable: false, orderable: false }
                     ],
               language : {
                    processing: '<img src="{{asset('assets/images/'.$gs->admin_loader)}}">'
                },
                drawCallback : function( settings ) {
	    			$('.select').niceSelect();	
			   }
            });

})(jQuery);

    </script>

{{-- DATA TABLE --}}
    
@endsection   