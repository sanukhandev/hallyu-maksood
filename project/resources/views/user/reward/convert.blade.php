@extends('layouts.front')

@section('content')
@include('partials.global.common-header')

 <!-- breadcrumb -->
 <div class="full-row bg-light overlay-dark py-5" style="background-image: url({{ $gs->breadcrumb_banner ? asset('assets/images/'.$gs->breadcrumb_banner):asset('assets/images/noimage.png') }}); background-position: center center; background-size: cover;">
    <div class="container">
        <div class="row text-center text-white">
            <div class="col-12">
                <h3 class="mb-2 text-white">{{ __('Reward') }}

                </h3>
            </div>
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 d-inline-flex bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="{{ route('user-dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('Reward ') }}</li>
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

                            <h4 class="widget-title down-line mb-30">{{ __('Reward Point') }}
                                <a class="mybtn1" href="{{ url()->previous() }}"> <i class="fas fa-arrow-left"></i> {{ __('Back')}}</a>
                            </h4>
                            <div class="gocover" style="background: url({{ asset('assets/images/'.$gs->loader) }}) no-repeat scroll center center rgba(45, 45, 45, 0.5);"></div>
                            <form id="userform" action="{{route('user-reward-convert-submit')}}" class="pay-form" class="form-horizontal" action="" method="POST" enctype="multipart/form-data">

                                   {{ csrf_field() }}

                                   @include('includes.admin.form-both')
                                   <div class="form-group mb-3">
                                       <label class="control-label col-sm-4">{{ __('Current Point') }} : {{ Auth::user()->reward }}</label>
                                   </div>
                                   <div class="form-group mb-4">
                                       <label class="control-label col-sm-4">{{$gs->reward_point}} {{__('Reward Point To (USD)')}} ${{$gs->reward_dolar}}</label>
                                   </div>

                                     <div class="form-group mt-2">
                                       <div class="row">
                                         <div class="col-md-6">
                                           <label class="control-label col-sm-12" for="reward">{{ __('Reward Point') }} *  </label>
                                             <div class="input-group mb-3">
                                               <input type="text" id="reward" name="reward_point" class="form-control border" placeholder="{{ __('Reward Point') }}" value="{{ old('reward_point') }}" required>

                                             </div>
                                         </div>
                                       </div>
                                     </div>

                                     <div class="form-group mt-2">
                                       <div class="row">
                                         <div class="col-md-6">
                                           <label class="control-label col-sm-12" for="name">{{ __('Convert Total') }} *  </label>
                                             <div class="input-group mb-3">
                                               <input type="text" id="convert_total" class="form-control border" placeholder="{{ __('Convert Total') }}" value="" readonly>
                                               <div class="input-group-append d-flex">
                                                 <span class="input-group-text" id="basic-addon2">{{ $curr->name }}</span>
                                               </div>
                                             </div>
                                         </div>
                                       </div>
                                     </div>
                               <hr>
                               <div class="add-product-footer">
                                   <button type="button" id="check" class="mybtn1">{{ __('Check') }} </button>
                                   <button id="final-btn" type="submit" class="mybtn1">{{ __('Convert') }} </button>
                               </div>
                           </form>



                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!--==================== Blog Section End ====================-->

@includeIf('partials.global.common-footer')

@endsection
@section('script')


<script type="text/javascript">

  $(document).on('click','#check',function(){
    let point = parseInt($('#reward').val());
    if(!isNaN(point)) {
      if(point <'{{$gs->reward_point}}'){
        toastr.error('Minimum Convert Point is {{$gs->reward_point}}');
    }else if(point >'{{$user->reward}}'){
        toastr.error('Your reward point is ' + '{{$user->reward}}');
    }else{
        let amount = (point / '{{$gs->reward_point}}' )* '{{$gs->reward_dolar}}';
        $('#convert_total').val(amount);
    }
    }
  })

</script>

@endsection
