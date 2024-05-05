@extends('layouts.front')
@section('content')
@include('partials.global.common-header')
<!-- breadcrumb -->
<div class="full-row bg-light overlay-dark py-5" style="background-image: url({{ $gs->breadcrumb_banner ? asset('assets/images/'.$gs->breadcrumb_banner):asset('assets/images/noimage.png') }}); background-position: center center; background-size: cover;">
   <div class="container">
      <div class="row text-center text-white">
         <div class="col-12">
            <h3 class="mb-2 text-white">{{ __('Dispute') }}</h3>
         </div>
         <div class="col-12">
            <nav aria-label="breadcrumb">
               <ol class="breadcrumb mb-0 d-inline-flex bg-transparent p-0">
                  <li class="breadcrumb-item"><a href="{{ route('user-dashboard') }}">{{ __('Dashboard') }}</a></li>
                  <li class="breadcrumb-item active" aria-current="page">{{ __('Dispute') }}</li>
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
                     <div class="header-area">
                        @if( $conv->order_number != null )
                        <h4 class="title">
                           {{ __('Order Number:') }} {{ $conv->order_number }}
                        </h4>
                        @endif
                        <h4 class="title">
                           {{ __('Conversation with') }} {{$conv->subject}} <a  class="mybtn1" href="{{ url()->previous() }}"> <i class="fas fa-arrow-left"></i> {{ __('Back') }}</a>
                        </h4>
                     </div>
                     <div class="support-ticket-wrapper ">
                        <div class="panel-primary">
                           <div class="gocover" style="background: url({{ asset('assets/images/'.$gs->loader) }}) no-repeat scroll center center rgba(45, 45, 45, 0.5);"></div>
                           <div class="panel-body mt-4" id="messages">
                              @foreach($conv->messages as $message)
                              @if($message->user_id != 0)
                              <div class="single-reply-area user">
                                 <div class="row">
                                    <div class="col-lg-12">
                                       <div class="reply-area">
                                          <div class="left">
                                             <p>{{$message->message}}</p>
                                          </div>
                                          <div class="right">
                                             @if($message->conversation->user->is_provider == 1)
                                             <img class="img-circle" src="{{$message->conversation->user->photo != null ? $message->conversation->user->photo : asset('assets/images/noimage.png')}}" alt="">
                                             @else
                                             <img class="img-circle" src="{{$message->conversation->user->photo != null ? asset('assets/images/users/'.$message->conversation->user->photo) : asset('assets/images/noimage.png')}}" alt="">
                                             @endif
                                             <p class="ticket-date">{{$message->conversation->user->name}}</p>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <br>
                              @else
                              <div class="single-reply-area admin">
                                 <div class="row">
                                    <div class="col-lg-12">
                                       <div class="reply-area">
                                          <div class="left">
                                             <img class="img-circle" src="{{ asset('assets/images/admin.jpg')}}" alt="">
                                             <p class="ticket-date">{{ __('Admin') }}</p>
                                          </div>
                                          <div class="right">
                                             <p>{{$message->message}}</p>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <br>
                              @endif
                              @endforeach
                           </div>
                           <form id="messageform" data-href="{{ route('user-message-load',$conv->id) }}" action="{{route('user-message-store')}}" method="POST">
                              <div class="panel-footer">
                                 {{csrf_field()}}
                                 <div class="form-group">
                                    <input type="hidden" name="conversation_id" value="{{$conv->id}}">
                                    <input type="hidden" name="user_id" value="{{$conv->user->id}}">
                                    <textarea class="form-control" name="message" id="wrong-invoice" rows="5" style="resize: vertical;" required="" placeholder="{{ __('Message') }}"></textarea>
                                 </div>
                              </div>
                              <div class="form-group mt-3">
                                 <button class="mybtn1">
                                 {{ __('Add Reply') }}
                                 </button>
                              </div>
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
<!--==================== Blog Section End ====================-->
{{-- MESSAGE MODAL --}}
<div class="message-modal">
   <div class="modal" id="vendorform" tabindex="-1" role="dialog" aria-labelledby="vendorformLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="vendorformLabel">{{ __('Add Dispute') }}</h5>
               <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
               <div class="container-fluid p-0">
                  <div class="row">
                     <div class="col-md-12">
                        <div class="contact-form">
                           <form id="emailreply1">
                              {{csrf_field()}}
                              <ul>
                                 <li>
                                    <input type="text" class="input-field form-control border mb-4" id="order" name="order_numkber" placeholder="{{ __('Order Number *') }}" required="">
                                 </li>
                                 <li>
                                    <input type="text" class="input-field form-control border mb-4" id="subj1" name="subject" placeholder="{{ __('Subject *') }}" required="">
                                 </li>
                                 <li>
                                    <textarea class="input-field textarea form-control border mb-4" name="message" id="msg1" placeholder="{{ __('Your Message *') }}" required=""></textarea>
                                 </li>
                              </ul>
                              <input type="hidden"  name="type" value="Dispute">
                              <button class="submit-btn" id="emlsub1" type="submit">{{ __('Send') }}</button>
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
</div>
</div>
{{-- MESSAGE MODAL ENDS --}}
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="modal1" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header d-block text-center">
            <h4 class="modal-title d-inline-block">{{ __('Confirm Delete ?') }}</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <p class="text-center">{{ __('You are about to delete this Dispute.') }}</p>
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
@endsection
@section('script')
<script type="text/javascript">
   (function($) {
   "use strict";

         $(document).on("submit", "#emailreply1" , function(){
         var token = $(this).find('input[name=_token]').val();
         var subject = $(this).find('input[name=subject]').val();
         var message =  $(this).find('textarea[name=message]').val();
         var $type  = $(this).find('input[name=type]').val();
         var order = $('#order').val();
         $('#subj1').prop('disabled', true);
         $('#msg1').prop('disabled', true);
         $('#emlsub1').prop('disabled', true);
         $.ajax({
           type: 'post',
           url: "{{URL::to('/user/admin/user/send/message')}}",
           data: {
               '_token': token,
               'subject'   : subject,
               'message'  : message,
               'type'   : $type,
               'order'  : order
                 },
           success: function( data) {
           $('#subj1').prop('disabled', false);
           $('#msg1').prop('disabled', false);
           $('#subj1').val('');
           $('#msg1').val('');
           $('#emlsub1').prop('disabled', false);
           if(data == 0)
             toastr.error("Oops Something Went Wrong !");
           else
             toastr.success("Message Sent");
             $('#vendorform').modal('hide');
           }
       });
         return false;
       });

       })(jQuery);

</script>
<script type="text/javascript">
   (function($) {
   		"use strict";

         $('#confirm-delete').on('show.bs.modal', function(e) {
             $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
         });

   })(jQuery);

</script>
@endsection
