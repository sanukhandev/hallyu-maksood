@extends('layouts.admin')

@section('content')

            <div class="content-area">

              <div class="mr-breadcrumb">
                <div class="row">
                  <div class="col-lg-12">
                      <h4 class="heading">{{ __('Rewards') }} </h4>
                      <ul class="links">
                        <li>
                          <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }} </a>
                        </li>
                      
                        <li>
                          <a href="{{ route('admin-reward-index') }}">{{ __('Rewards') }}</a>
                        </li>
                        
                       
                      </ul>
                  </div>
                </div>
              </div>

              <div class="add-product-content1 add-product-content2">
			
                <div class="row">
                  <div class="col-lg-12">
					<div class="heading-area">
						@include('includes.admin.form-both') 
						<form id="geniusform" action="{{route('admin-reward-info-update')}}" method="POST">
							@csrf
						<h4 class="title">
							{{ __('Rewards') }} :
						</h4>
						
						<div class="action-list mr-2">
							<select class="process select droplinks {{ $gs->is_reward  == 1 ? 'drop-success' : 'drop-danger' }}">
							  <option data-val="1" value="{{route('admin-gs-is_reward',1)}}" {{ $gs->is_reward == 1 ? 'selected' : '' }}>{{ __('Activated') }}</option>
							  <option data-val="0" value="{{route('admin-gs-is_reward',0)}}" {{ $gs->is_reward == 0 ? 'selected' : '' }}>{{ __('Deactivated') }}</option>
							</select>
						  </div>
						  
						  <span>{{__('Reward Point Number')}}</span>
						  
						<div class="action-list ml-2 mr-2 d-inline-block">
							<input type="number" min="1" name="reward_point" class="form-control" value="{{$gs->reward_point}}" placeholder="{{__('Reward point')}}">
						</div>
						{{__('To (USD) Dolar ($)')}}
						<div class="action-list ml-2">
							<input type="number" min="0" name="reward_dolar" class="form-control" value="{{$gs->reward_dolar}}" placeholder="{{__('USD')}}">
						</div>
						<div class="action-list ml-2">
							<button class="mybtn1" type="submit">Save</button>
						</div>
					</form>
					</div>
                    <div class="product-description">
                      <div class="body-area">

                        <div class="gocover" style="background: url({{asset('assets/images/'.$gs->admin_loader)}}) no-repeat scroll center center rgba(45, 45, 45, 0.5);"></div>

                        
						@include('includes.admin.form-both') 
                      <form id="geniusform" action="{{route('admin-reward-update')}}" method="POST">

                        @csrf

                        <div class="featured-keyword-area">
													<div class="feature-tag-top-filds" id="whole-section">
														@foreach ($datas as $data)
														<div class="feature-area">
															<span class="remove whole-remove"><i
																	class="fas fa-times"></i></span>
															<div class="row">
																
																<div class="col-lg-6">
																	
																	<input type="number" name="order_amount[]"
																		class="input-field"
																		placeholder="{{ __('Order Amount (USD)') }}" min="0" value="{{$data->order_amount}}">
																</div>
																<div class="col-lg-6">
																	<input type="number" name="reward[]"
																		class="input-field"
																		placeholder="{{ __('Reward') }}"
																		min="0" value="{{$data->reward}}" />
																</div>
															</div>
														</div>
														@endforeach
													</div>
													<a href="javascript:;" id="whole-btn" class="add-fild-btn"><i
															class="icofont-plus"></i> {{ __('Add More Field') }}</a>
												</div>


                        <div class="row text-center">
                          <div class="col-lg-12">
                            <div class="left-area">
                              
                            </div>
                          </div>
                          <div class="col-lg-12">
                            <button class="addProductSubmit-btn" type="submit">{{ __('Update Reward') }}</button>
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
<script type="text/javascript">

// Whole Sell Section

$("#whole-btn").on('click', function(){


$("#whole-section").append(''+
						'<div class="feature-area">'+
							'<span class="remove whole-remove"><i class="fas fa-times"></i></span>'+
								'<div  class="row">'+
									'<div class="col-lg-6">'+
										'<input type="number" name="order_amount[]" class="input-field" placeholder="Order Amount (USD)" min="0" required>'+
									'</div>'+
									'<div class="col-lg-6">'+
										'<input type="number" name="reward[]" class="input-field" placeholder="Reward" min="0" required>'+
									'</div>'+
								'</div>'+
						'</div>'
						+'');        

});

$(document).on('click','.whole-remove', function(){

$(this.parentNode).remove();
if (isEmpty($('#whole-section'))) {

$("#whole-section").append(''+
						'<div class="feature-area">'+
							'<span class="remove whole-remove"><i class="fas fa-times"></i></span>'+
								'<div  class="row">'+
									'<div class="col-lg-4">'+
										'<input type="number" name="order_amount[]" class="input-field" placeholder="Order Amount" min="0" required>'+
									'</div>'+
									
									'<div class="col-lg-4">'+
										'<input type="number" name="reward[]" class="input-field" placeholder="Reward" min="0" required>'+
									'</div>'+
								'</div>'+
						'</div>'
						+'');
}

});

// Whole Sell Section Ends

</script>
@endsection