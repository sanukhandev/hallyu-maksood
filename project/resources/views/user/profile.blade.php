@extends('layouts.front')
@section('content')
@include('partials.global.common-header')
<!-- breadcrumb -->
<div class="full-row bg-light overlay-dark py-5" style="background-image: url({{ $gs->breadcrumb_banner ? asset('assets/images/'.$gs->breadcrumb_banner):asset('assets/images/noimage.png') }}); background-position: center center; background-size: cover;">
   <div class="container">
      <div class="row text-center text-white">
         <div class="col-12">
            <h3 class="mb-2 text-white">{{ __('Edit Profile') }}</h3>
         </div>
         <div class="col-12">
            <nav aria-label="breadcrumb">
               <ol class="breadcrumb mb-0 d-inline-flex bg-transparent p-0">
                  <li class="breadcrumb-item"><a href="{{ route('user-dashboard') }}">{{ __('Dashboard') }}</a></li>
                  <li class="breadcrumb-item active" aria-current="page">{{ __('Edit Profile') }}</li>
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
                     <h4 class="widget-title down-line mb-30">{{ __('Edit Profile') }}
                     </h4>
                     <div class="edit-info-area">
                        <div class="body">
                           <div class="edit-info-area-form">
                              <div class="gocover"
                                 style="background: url({{ asset('assets/images/'.$gs->loader) }}) no-repeat scroll center center rgba(45, 45, 45, 0.5);">
                              </div>
                              <form id="userform" action="{{route('user-profile-update')}}" method="POST"
                                 enctype="multipart/form-data">
                                 @csrf
                                 <div class="upload-img">
                                    @if($user->is_provider == 1)
                                    <div class="img"><img
                                       src="{{ $user->photo ? asset($user->photo):asset('assets/images/'.$gs->user_image) }}">
                                    </div>
                                    @else
                                    <div class="img"><img
                                       src="{{ $user->photo ? asset('assets/images/users/'.$user->photo):asset('assets/images/'.$gs->user_image) }}">
                                    </div>
                                    @endif
                                    @if($user->is_provider != 1)
                                    <div class="file-upload-area">
                                       <div class="upload-file">
                                          <label>{{ __('Upload') }}
                                          <input type="file" size="60" name="photo" class="upload form-control">
                                          </label>
                                       </div>
                                    </div>
                                    @endif
                                 </div>
                                 <div class="row mb-4">
                                    <div class="col-lg-6">
                                       <input name="name" type="text" class="input-field form-control border"
                                          placeholder="{{ __('User Name') }}" required=""
                                          value="{{ $user->name }}">
                                    </div>
                                    <div class="col-lg-6">
                                       <input name="email" type="email" class="input-field form-control border"
                                          placeholder="{{ __('Email Address') }}" required=""
                                          value="{{ $user->email }}" disabled>
                                    </div>
                                 </div>
                                 <div class="row mb-4">
                                    <div class="col-lg-6">
                                       <input name="phone" type="text" class="input-field form-control border"
                                          placeholder="{{ __('Phone Number') }}" required=""
                                          value="{{ $user->phone }}">
                                    </div>
                                    <div class="col-lg-6">
                                       <input name="fax" type="text" class="input-field form-control border"
                                          placeholder="{{ __('Fax') }}" value="{{ $user->fax }}">
                                    </div>
                                 </div>
                                 <div class="row mb-4">
                                    <div class="col-lg-6">
                                       <input name="city" type="text" class="input-field form-control border"
                                          placeholder="{{ __('City') }}" value="{{ $user->city }}">
                                    </div>
                                    <div class="col-lg-6">
                                       <select class="input-field form-control border" name="country">
                                          <option value="">{{ __('Select Country') }}</option>
                                          @foreach (DB::table('countries')->get() as $data)
                                          <option value="{{ $data->country_name }}" {{ $user->country == $data->country_name ? 'selected' : '' }}>
                                          {{ $data->country_name }}
                                          </option>
                                          @endforeach
                                       </select>
                                    </div>
                                 </div>
                                 <div class="row mb-4">
                                    <div class="col-lg-6">
                                       <input name="zip" type="text" class="input-field form-control border"
                                          placeholder="{{ __('Zip') }}" value="{{ $user->zip }}">
                                    </div>
                                    <div class="col-lg-6">
                                       <input name="state" type="text" class="input-field form-control border"
                                          placeholder="{{ __('State') }}" value="{{ $user->state }}">
                                    </div>
                                 </div>
                                 <div class="row mb-4">
                                    <div class="col-lg-12">
                                       <textarea class="input-field form-control border" name="address" placeholder="{{ __('Address') }}" cols="30" rows="10" required>{{ $user->address }}</textarea>
                                    </div>
                                 </div>
                                 <div class="form-links">
                                    <button class="submit-btn btn btn-primary" type="submit">{{ __('Save') }}</button>
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
@endsection
