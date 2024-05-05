@extends('layouts.load')
@section('content')

						<div class="content-area">
							<div class="add-product-content1">
								<div class="row">
									<div class="col-lg-12">
										<div class="product-description">
											<div class="body-area">

                                            @include('alerts.admin.form-error') 
                                            
                                            <form id="geniusformdata" action="{{route('admin-user-vendor-update',$data->id)}}" method="POST" enctype="multipart/form-data">
                                                
                                                {{csrf_field()}}
                                                
                                                <div class="row">
													<div class="col-lg-4">
														<div class="left-area">
																<h4 class="heading">{{ __('Shop Name') }} *</h4>
														</div>
													</div>
													<div class="col-lg-7">
														<input type="text" class="input-field" name="shop_name" placeholder="{{ __('Shop Name') }}" required="" value="">
													</div>
												</div>

                                                <div class="row">
													<div class="col-lg-4">
														<div class="left-area">
																<h4 class="heading">{{ __('Owner Name') }} *</h4>
														</div>
													</div>
													<div class="col-lg-7">
														<input type="text" class="input-field" name="owner_name" placeholder="{{ __('Owner Name') }}" required="" value="">
													</div>
												</div>

                                                <div class="row">
													<div class="col-lg-4">
														<div class="left-area">
																<h4 class="heading">{{ __('Shop Number') }} *</h4>
														</div>
													</div>
													<div class="col-lg-7">
														<input type="text" class="input-field" name="shop_number" placeholder="{{ __('Shop Number') }}" required="" value="">
													</div>
												</div>

                                                <div class="row">
													<div class="col-lg-4">
														<div class="left-area">
																<h4 class="heading">{{ __('Shop Address') }} *</h4>
														</div>
													</div>
													<div class="col-lg-7">
														<input type="text" class="input-field" name="shop_address" placeholder="{{ __('Shop Address') }}" required="" value="">
													</div>
												</div>

                                                <div class="row">
													<div class="col-lg-4">
														<div class="left-area">
                                                                <h4 class="heading">{{ __('Registration Number') }} *</h4>
                                                                <p class="sub-heading">{{ __('(Optional)') }}</p>
														</div>
													</div>
													<div class="col-lg-7">
														<input type="text" class="input-field" name="reg_number" placeholder="{{ __('Registration Number') }}" value="">
													</div>
												</div>


                                                <div class="row">
													<div class="col-lg-4">
														<div class="left-area">
																<h4 class="heading">{{ __('Shop Details') }} *</h4>
														</div>
													</div>
													<div class="col-lg-7">
                                                        <textarea name="shop_address" class="input-field" placeholder="{{ __('Shop Details') }}" required></textarea>
													</div>
												</div>

												<div class="row">
													<div class="col-lg-4">
														<div class="left-area">
																<h4 class="heading">{{ __('Choose Plan') }} :</h4>
														</div>
													</div>
                                                    <div class="col-lg-7">
                                                        <select name="subs_id" required="">
                                                            @foreach(DB::table('subscriptions')->get() as $subdata)
                                                                <option value="{{ $subdata->id }}">{{ $subdata->title }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
												</div>

												<div class="row">
													<div class="col-lg-4">
														<div class="left-area">
															
														</div>
													</div>
													<div class="col-lg-7">
														<button class="addProductSubmit-btn" type="submit">{{ __('Submit') }}</button>
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