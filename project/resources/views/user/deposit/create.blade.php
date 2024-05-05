@extends('layouts.front')

@section('content')
@include('partials.global.common-header')

 <!-- breadcrumb -->
 <div class="full-row bg-light overlay-dark py-5" style="background-image: url({{ $gs->breadcrumb_banner ? asset('assets/images/'.$gs->breadcrumb_banner):asset('assets/images/noimage.png') }}); background-position: center center; background-size: cover;">
    <div class="container">
        <div class="row text-center text-white">
            <div class="col-12">
                <h3 class="mb-2 text-white">{{ __('Deposite') }}</h3>
            </div>
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 d-inline-flex bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="{{ route('user-dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('Deposite') }}</li>
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

                            <h4 class="widget-title down-line mb-30">{{ __('Deposit') }}
                                <a class="mybtn1" href="{{ route('user-deposit-index') }}">
                                    <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                                </a>
                            </h4>
                            <div class="pack-details">
                                <div class="row">

                                    <div class="col-lg-4">
                                        <h5 class="title">
                                          {{ __('Current Balance') }}
                                        </h5>
                                    </div>
                                    <div class="col-lg-8">
                                        <p class="value">
                                          {{ App\Models\Product::vendorConvertPrice(Auth::user()->balance) }}
                                        </p>
                                    </div>
                                </div>

                                <form id="deposit-form" class="pay-form" action="" method="POST">

                                    @include('alerts.form-success')
                                    @include('alerts.form-error')

                                    @csrf


                                    <div class="row mb-3">
                                        <div class="col-lg-4">
                                            <h5 class="title pt-1">
                                              {{ __('Deposit Amount') }} *
                                            </h5>
                                        </div>
                                        <div class="col-lg-8">
                                        <input type="number" class="option form-control border" min="1" id="amount"  name="amount" placeholder="{{ $curr->name }}" step="0.01" required="" value="{{ old('amount') }}">
                                        </div>
                                      </div>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <h5 class="title pt-1">
                                                {{ __('Select Payment Method') }} *
                                            </h5>
                                        </div>
                                        <div class="col-lg-8">
                                            <select name="method" id="method" class="option form-control border" required="">
                                                <option value="" data-form="" data-show="no" data-val="" data-href="">
                                                  {{ __('Select an option') }}
                                                </option>
                                                @foreach($gateway as $paydata)
                                                  @if($paydata->type == 'manual')
                                                  <option value="{{ $paydata->title }}" data-form="{{ $paydata->showDepositLink() }}" data-show="{{ $paydata->showForm() }}" data-href="{{ route('user.load.payment',['slug1' => $paydata->showKeyword(),'slug2' => $paydata->id]) }}" data-val="{{ $paydata->keyword }}">
                                                    {{ $paydata->title }}
                                                  </option>
                                                  @else
                                                  <option value="{{ $paydata->name }}" data-form="{{ $paydata->showDepositLink() }}" data-show="{{ $paydata->showForm() }}" data-href="{{ route('user.load.payment',['slug1' => $paydata->showKeyword(),'slug2' => $paydata->id]) }}" data-val="{{ $paydata->keyword }}">
                                                    {{ $paydata->name }}
                                                  </option>
                                                  @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div id="payments" class="d-none">
                                    </div>
                                    <input type="hidden" name="sub" id="sub" value="0">
                                    <div class="row">
                                        <div class="col-lg-4">
                                        </div>
                                        <div class="col-lg-8 mt-4">
                                            <button type="submit" id="final-btn" class="mybtn1">{{ __('Submit') }}</button>
                                        </div>
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
<script type="text/javascript" src="{{ asset('assets/front/js/payvalid.js') }}"></script>

<script type="text/javascript" src="{{ asset('assets/front/js/paymin.js') }}"></script>

<script type="text/javascript" src="{{ asset('assets/front/js/payform.js') }}"></script>

<script src="https://js.paystack.co/v1/inline.js"></script>

<script src="//voguepay.com/js/voguepay.js"></script>

<script src="https://www.2checkout.com/checkout/api/2co.min.js"></script>

<script type="text/javascript" src="https://js.stripe.com/v2/"></script>

<script src="https://secure.mlstatic.com/sdk/javascript/v1/mercadopago.js"></script>

<script type="text/javascript">

(function($) {
		"use strict";

$('#method').on('change',function(){
    var val  = $(this).find(':selected').attr('data-val');
    var form = $(this).find(':selected').attr('data-form');
    var show = $(this).find(':selected').attr('data-show');
    var href = $(this).find(':selected').attr('data-href');

    if(show == "yes"){
        $('#payments').removeClass('d-none');
    }else{
        $('#payments').addClass('d-none');
    }

    if(val == 'paystack'){
			$('.pay-form').prop('id','paystack');
      $('#amount').prop('name','amount');
		}
		else if(val == 'voguepay'){
			$('.pay-form').prop('id','voguepay');
      $('#amount').prop('name','amount');
		}
		else if(val == 'mercadopago'){
			$('.pay-form').prop('id','mercadopago');
      $('#amount').prop('name','deposit_amount');
		}
		else if(val == '2checkout'){
			$('.pay-form').prop('id','twocheckout');
      $('#amount').prop('name','amount');
		}
		else {
			$('.pay-form').prop('id','deposit-form');
      $('#amount').prop('name','amount');
		}


    $('#payments').load(href);
    $('.pay-form').attr('action',form);
});


    $(document).on('submit','#paystack',function(){
            var val = $('#sub').val();
            if(val == 0){
                var total = $('#amount').val();
                total = Math.round(total);
                var handler = PaystackPop.setup({
                key: '{{ $paystack["key"] }}',
                email: '{{ Auth::user()->email }}',
                amount: total * 100,
                currency: "{{ $curr->name }}",
                ref: ''+Math.floor((Math.random() * 1000000000) + 1),
                    callback: function(response){
                        $('#ref_id').val(response.reference);
                        $('#sub').val('1');
                        $('#final-btn').click();
                    },
                    onClose: function(){
                        window.location.reload();
                    }
                });
                handler.openIframe();
                 return false;

            }
            else {
                return true;
            }
		});

})(jQuery);

</script>


@endsection

