@extends('layouts.admin')

@section('content')

    <div class="content-area">

        <div class="mr-breadcrumb">
            <div class="row">
                <div class="col-lg-12">
                <h4 class="heading">{{ __("Install New Addon") }} <a class="add-btn" href="{{ route('admin-addon-index') }}">{{ __('Back') }}</a> </h4>
                        <ul class="links">
                            <li>
                                <a href="{{ route('admin.dashboard') }}">{{ __("Dashboard") }} </a>
                            </li>
                            <li>
                                <a href="{{ route('admin-addon-index') }}">{{ __("Manage Addons") }} </a>
                            </li>
                            <li>
                                <a href="{{ route('admin-addon-create') }}">{{ __("Install New Addon") }}</a>
                            </li>
                        </ul>
                </div>
            </div>
        </div>
        
        <div class="add-product-content">
            <div class="row">
                <div class="col-lg-12 p-5">

                    <div class="gocover" style="background: url({{asset('assets/images/'.$gs->admin_loader)}}) no-repeat scroll center center rgba(45, 45, 45, 0.5);"></div> 
                    <form id="geniusform" action="{{route('admin-addon-install')}}" method="POST" enctype="multipart/form-data">
                        
                        {{csrf_field()}}

                        @include('alerts.admin.form-both') 

                        <div class="row">
                            
                            <div class="col-md-12">
                              <input type="text" class="input-field" name="purchase_key" placeholder="{{ __('Enter Purchase key') }}" required="" value="">
                            </div>
                          </div>

                        <div class="row justify-content-center">

                            <div class="col-lg-12 d-flex justify-content-center text-center">
                                <div class="csv-icon">
                                    <i class="fas fa-download"></i>
                                </div>
                            </div>
                            
                            <div class="col-lg-12 d-flex justify-content-center text-center">
                                <div class="left-area mr-4">
                                    <h4 class="heading">{{ __("Upload File") }} *</h4>
                                </div>
                                <span class="file-btn">
                                    <input type="file" id="file" name="file" accept=".zip" required>
                                </span>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-lg-12 mt-4 text-center">
                                <button class="mybtn1 mr-5" type="submit">{{ __("Install") }}</button>
                            </div>
                        </div>
                            
                    </form>
                </div>
            </div>
        </div>
        
    </div>



@endsection
