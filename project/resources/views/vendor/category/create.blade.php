@extends('layouts.load')

@section('content')

            <div class="content-area">

              <div class="add-product-content1">
                <div class="row">
                  <div class="col-lg-12">
                    <div class="product-description">
                      <div class="body-area">
                        @include('alerts.vendor.form-error')
                        <form id="geniusformdata" action="{{route('vendor-cat-create')}}" method="POST" enctype="multipart/form-data">
                          {{csrf_field()}}

                          <div class="row">
                            <div class="col-lg-4">
                              <div class="left-area">
                                <h4 class="heading">{{ __('Select Language') }}*</h4>
                              </div>
                            </div>
                            <div class="col-lg-7">
                              <select name="language_id" required="">
                                  @foreach(DB::table('languages')->get() as $ldata)
                                  <option value="{{ $ldata->id }}">{{ $ldata->language }}</option>
                                  @endforeach
                                </select>
                            </div>
                            </div>


                          <div class="row">
                            <div class="col-lg-4">
                              <div class="left-area">
                                  <h4 class="heading">{{ __('Name') }} *</h4>
                                  <p class="sub-heading">{{ __('(In Any Language)') }}</p>
                              </div>
                            </div>
                            <div class="col-lg-7">
                              <input type="text" class="input-field" name="name" placeholder="{{ __('Enter Name') }}" required="" value="">
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-lg-4">
                              <div class="left-area">
                                  <h4 class="heading">{{ __('Slug') }} *</h4>
                                  <p class="sub-heading">{{ __('In English') }}</p>
                              </div>
                            </div>
                            <div class="col-lg-7">
                              <input type="text" class="input-field" name="slug" placeholder="{{ __('Enter Slug') }}" required="" value="">
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-lg-4">
                              <div class="left-area">
                                  <h4 class="heading">{{ __('Set Icon') }} *</h4>
                              </div>
                            </div>
                            <div class="col-lg-7">
                              <div class="img-upload">
                                  <div id="image-preview" class="img-preview" style="background: url({{ asset('assets/admin/images/upload.png') }});">
                                      <label for="image-upload" class="img-label" id="image-label"><i class="icofont-upload-alt"></i>{{ __('Upload Icon') }}</label>
                                      <input type="file" name="photo" class="img-upload" id="image-upload">
                                    </div>
                              </div>

                            </div>
                          </div>


                            <div class="row">
                              <div class="col-lg-4">
                                <div class="left-area">
                                  <h4 class="heading">{{ __('Set Banner') }} *</h4>
                                </div>
                              </div>
                              <div class="col-lg-7">
                                <div class="img-upload full-width-img">
                                  <div id="image-preview" class="img-preview" style="background: url({{ asset('assets/admin/images/upload.png') }});">
                                    <label for="image-upload" class="img-label"><i class="icofont-upload-alt"></i>{{ __('Upload Banner') }}</label>
                                    <input type="file" name="image" class="img-upload">
                                  </div>
                                  <p class="text">{{__('Prefered Size: (1230x267) or Square Sized Image')}}</p>
                                </div>
                              </div>
                            </div>



                          <br>
                          <div class="row">
                            <div class="col-lg-4">
                              <div class="left-area">

                              </div>
                            </div>
                            <div class="col-lg-7">
                              <button class="addProductSubmit-btn" type="submit">{{ __('Create Category') }}</button>
                            </div>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

@endsection
