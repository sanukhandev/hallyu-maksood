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

                            <h4 class="widget-title down-line mb-30">{{ __('Deposits') }}
                                <a class="mybtn1" href="{{ route('user-deposit-create') }}"> <i class="fas fa-plus"></i> {{ __('Add Deposit') }}</a>
                            </h4>

                            <div class="mr-table allproduct mt-4">
                                <div class="table-responsive">
                                        <table id="example" class="table" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Deposit Date') }}</th>
                                                    <th>{{ __('Method') }}</th>
                                                    <th>{{ __('Amount')}}</th>
                                                    <th>{{ __('Status') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach(Auth::user()->deposits as $data)
                                                    <tr>
                                                        <td data-label="{{ __('Deposit Date') }}">
                                                            <div>
                                                                <div>
                                                                    {{date('d-M-Y',strtotime($data->created_at))}}
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td data-label="{{ __('Method') }}">
                                                            <div>
                                                                <div>
                                                                    {{$data->method}}
                                                                </div>
                                                            </div>        
                                                        </td>
                                                        <td data-label="{{ __('Amount')}}">
                                                            <div>
                                                                <div>
                                                                    {{ \PriceHelper::showOrderCurrencyPrice(($data->amount * $data->currency_value),$data->currency_code) }}
                                                                </div>
                                                            </div>        
                                                        </td>
                                                        <td data-label="{{ __('Status') }}">
                                                            <div>
                                                                <span class="badge {{$data->status == 1 ? ' bg-success': 'bg-primary'}}">
                                                                    {{ $data->status == 1 ? 'Completed' : 'Pending'}}
                                                                </span>
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
<script src = "{{ asset('assets/front/js/dataTables.min.js') }}" defer ></script>
<script src = "{{ asset('assets/front/js/user.js') }}" defer ></script>
@endsection
