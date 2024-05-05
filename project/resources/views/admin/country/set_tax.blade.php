@extends('layouts.admin')

@section('content')
<input type="hidden" id="headerdata" value="{{strtoupper($country->country_name)}} / {{ __('STATE TAX') }}">
            <div class="content-area">
              <div class="mr-breadcrumb">
                <div class="row">
                  <div class="col-lg-12">
                      <h4 class="heading"><u>{{ __($country->country_name) }}</u> / {{ __('Tax') }} <a class="add-btn" href="{{route('admin-country-tax')}}"><i class="fas fa-arrow-left"></i> {{ __('Back') }}</a></h4>
                      <ul class="links">
                        <li>
                          <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }} </a>
                        </li>
                        <li>
                          <a href="javascript:;">{{ __('Country') }} </a>
                        </li>
                        <li>
                          <a href="{{route('admin-country-index')}}">{{ __('Manage Tax') }} </a>
                        </li>
                        <li>
                          <a href="javascript:;">{{ __('Tax') }}</a>
                        </li>
                        
                      </ul>
                  </div>
                </div>
              </div>
              <div class="add-product-content">
                <div class="row">
                  <div class="col-lg-12">
                    <div class="product-description">
                      <div class="body-area">
                      <div class="gocover" style="background: url({{asset('assets/images/'.$gs->admin_loader)}}) no-repeat scroll center center rgba(45, 45, 45, 0.5);"></div>
                      <form id="geniusform" action="{{route('admin-tax-update',$country->id)}}" method="POST" enctype="multipart/form-data">
                        {{csrf_field()}}
                      @include('includes.admin.form-both') 

                      <div class="row">
                        <div class="col-lg-4">
                          <div class="left-area">
                              <h4 class="heading">{{ __('Country') }} *</h4>
                              <p class="sub-heading">{{ __('(In Any Language)') }}</p>
                          </div>
                        </div>
                        <div class="col-lg-7">
                          <input type="text" readonly class="input-field"  value="{{$country->country_name}}">
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-lg-4">
                          <div class="left-area">
                              <h4 class="heading">{{ __('Tax') }} (%)  *</h4>
                             
                          </div>
                        </div>
                        <div class="col-lg-7">
                          <input type="text" name="tax" class="input-field" placeholder="{{__('Enter Tax')}}"  value="{{$country->tax}}">
                        </div>
                      </div>

                      
                      <div class="row">
                        <div class="col-lg-4">
                          <div class="left-area">
                              <h4 class="heading">{{ __('Allow State Tax') }}</h4>
                          </div>
                        </div>
                        <div class="col-lg-7">
                            <ul class="list">
                                <li>
                                    <input type="checkbox" name="is_state_tax" id="allow_state_tax" value="1" id="check1">
                                    <label for="check1">{{__('Allow State Tax')}} </label>
                                </li>
                            </ul>
                        </div>
                      </div>

                      <div class="show_state d-none">
                        <hr>
                        <u><h4 class="text-center mb-3">{{$country->country_name}} / {{__('State List')}}</h4></u>
                        <br>
                      @forelse ($country->states as $state)
                        <div class="row">
                            <div class="col-lg-4">
                              <div class="left-area">
                                  <h4 class="heading">{{ __($state->state) }} (%)  *</h4>
                              </div>
                            </div>
                            <div class="col-lg-7">
                              <input type="text"  class="input-field" name="state_tax[]" placeholder="Enter Tax"  value="{{$state->tax }}">
                            </div>
                          </div>
                    @empty
                    <div class="text-center">
                        {{__('State Not Found Please')}}  <a class="mybtn1" href="{{route('admin-state-index',$country->id)}}">{{__('Insert State')}}</a>
                    </div>
                    @endforelse
                </div>
                      
                      <br>
                        <div class="row">
                          <div class="col-lg-4">
                            <div class="left-area">
                              
                            </div>
                          </div>
                          <div class="col-lg-7">
                            <button class="addProductSubmit-btn" type="submit">{{ __('Save') }}</button>
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


@section('scripts')
    <script>
        $(document).on('click','#allow_state_tax',function(){
            if($(this).is(':checked')){
                $('.show_state').removeClass('d-none');
            }else{
                $('.show_state').addClass('d-none');
            }
        })

    </script>
@endsection