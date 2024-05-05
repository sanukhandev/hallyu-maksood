@extends('layouts.front')
@section('css')
    <link rel="stylesheet" href="{{asset('assets/front/css/datatables.css')}}">
@endsection
@section('content')
@include('partials.global.common-header')
<!-- breadcrumb -->
<div class="full-row bg-light overlay-dark py-5" style="background-image: url({{ $gs->breadcrumb_banner ? asset('assets/images/'.$gs->breadcrumb_banner):asset('assets/images/noimage.png') }}); background-position: center center; background-size: cover;">
   <div class="container">
      <div class="row text-center text-white">
         <div class="col-12">
            <h3 class="mb-2 text-white">{{ __('Favorite Sellers') }}
            </h3>
         </div>
         <div class="col-12">
            <nav aria-label="breadcrumb">
               <ol class="breadcrumb mb-0 d-inline-flex bg-transparent p-0">
                  <li class="breadcrumb-item"><a href="{{ route('user-dashboard') }}">{{ __('Dashboard') }}</a></li>
                  <li class="breadcrumb-item active" aria-current="page">{{ __('Favorite Sellers') }}</li>
               </ol>
            </nav>
         </div>
      </div>
   </div>
</div>
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
                     <h4 class="widget-title down-line mb-30">{{ __('Favorite Sellers') }}
                     </h4>
                     <div class="mr-table allproduct message-area  mt-4">
                        @include('alerts.form-success')
                        <div class="table-responsive">
                           <table id="example" class="table" cellspacing="0" width="100%">
                              <thead>
                                 <tr>
                                    <th>{{ __('Shop Name') }}</th>
                                    <th>{{ __('Owner Name') }}</th>
                                    <th>{{ __('Address') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 @foreach($favorites as $vendor)
                                 @php
                                 $seller = App\Models\User::findOrFail($vendor->vendor_id);
                                 @endphp
                                 <tr class="conv">
                                    <td data-label="{{ __('Shop Name') }}">
                                       <div>
                                          {{$seller->shop_name}}
                                       </div>
                                    </td>
                                    <td data-label="{{ __('Owner Name') }}">
                                       <div>
                                          {{$seller->owner_name}}
                                       </div>
                                    </td>
                                    <td data-label="{{ __('Address') }}">
                                       <div>
                                          {{$seller->shop_address}}
                                       </div>
                                    </td>
                                    <td data-label="{{ __('Actions') }}">
                                       <div>
                                          <a target="_blank" href="{{route('front.vendor',str_replace(' ', '-',($seller->shop_name)))}}" class="link view mybtn1"><i class="fa fa-eye"></i></a>

                                          <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#confirm-delete" data-href="{{route('user-favorite-delete',$vendor->id)}}" class="link remove mybtn1 "><i class="fa fa-trash"></i></a>
                                       </div>
                                    </td>
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
      </div>
   </div>
</div>
<!--==================== Blog Section End ====================-->
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="modal1" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header d-block text-center">
            <h4 class="modal-title d-inline-block">{{ __('Confirm Delete ?') }}</h4>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <p class="text-center">{{ __('You are about to delete this Seller.') }}</p>
            <p class="text-center">{{ __('Do you want to proceed?') }}</p>
         </div>
         <div class="modal-footer justify-content-center">
            <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Cancel') }}</button>
            <a class="btn btn-danger btn-ok">{{ __('Delete') }}</a>
         </div>
      </div>
   </div>
</div>
@includeIf('partials.global.common-footer')
{{-- Modal --}}
@endsection
@section('script')
<script type="text/javascript">
   (function($) {
           "use strict";

         $('#confirm-delete').on('show.bs.modal', function(e) {
             $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
         });

   })(jQuery);

</script>
@endsection
