@extends('layouts.vendor')
@section('styles')

<link href="{{asset('assets/admin/css/product.css')}}" rel="stylesheet" />
<link href="{{asset('assets/admin/css/jquery.Jcrop.css')}}" rel="stylesheet" />
<link href="{{asset('assets/admin/css/Jcrop-style.css')}}" rel="stylesheet" />

@endsection
@section('content')

<div class="content-area">
	<div class="mr-breadcrumb">
		<div class="row">
			<div class="col-lg-12">
					<h4 class="heading"> {{ __("Edit Product") }}<a class="add-btn ml-2" href="{{ url()->previous() }}"><i class="fas fa-arrow-left"></i> {{ __("Back") }}</a></h4>
					<ul class="links">
						<li>
							<a href="{{ route('vendor.dashboard') }}">{{ ("Dashboard") }} </a>
						</li>
					<li>
						<a href="javascript:;">{{ __("Affiliate Products") }} </a>
					</li>
					<li>
						<a href="{{ route('vendor-import-index') }}">{{ __("All Products") }}</a>
					</li>
						<li>
							<a href="javascript:;">{{ __("Edit") }}</a>
						</li>
					</ul>
			</div>
		</div>
	</div>
	<div class="gocover" style="background: url({{asset('assets/images/'.$gs->admin_loader)}}) no-repeat scroll center center rgba(45, 45, 45, 0.5);"></div>
	<form id="geniusform" action="{{route('vendor-import-update',$data->id)}}" method="POST" enctype="multipart/form-data">
		{{csrf_field()}}
		@include('alerts.admin.form-both') 
	<div class="row">
		<div class="col-lg-8">
			<div class="add-product-content">
				<div class="row">
					<div class="col-lg-12">
						<div class="product-description">
							<div class="body-area">
								<div class="row">
									<div class="col-lg-12">
									  <div class="left-area">
										  <h4 class="heading">{{ __('Select Language') }}*</h4>
									  </div>
									</div>
									<div class="col-lg-12">
										<select name="language_id" required="">
											  @foreach(DB::table('languages')->get() as $ldata)
												<option value="{{ $ldata->id }}" {{ $ldata->id == $data->language_id ? 'selected' : '' }}>{{ $ldata->language }}</option>
											  @endforeach
										  </select>
									</div>
								</div>

								<div class="row">
									<div class="col-lg-12">
										<div class="left-area">
											<h4 class="heading">{{ __('Product Name') }}* </h4>
											<p class="sub-heading">{{ __('(In Any Language)') }}</p>
										</div>
									</div>
									<div class="col-lg-12">
										<input type="text" class="input-field" placeholder="{{ __('Enter Product Name') }}" name="name" required="" value="{{ $data->name }}">
									</div>
								</div>
		
								<div class="row">
									<div class="col-lg-12">
										<div class="left-area">
											<h4 class="heading">{{ __('Product Sku') }}* </h4>
										</div>
									</div>
									<div class="col-lg-12">
										<input type="text" class="input-field" placeholder="{{ __('Enter Product Sku') }}" name="sku" required="" value="{{ $data->sku }}">
									</div>
								</div>

								<div class="row">
									<div class="col-lg-12">
										<div class="left-area">
												<h4 class="heading">{{ __("Product Affiliate Link") }}* </h4>
												<p class="sub-heading">{{ __("(External Link)") }}</p>
										</div>
									</div>
									<div class="col-lg-12">
										<input type="text" class="input-field" placeholder="{{ __("Enter Product Link") }}" name="affiliate_link" required="" value="{{ $data->affiliate_link }}">
									</div>
								</div>

								<div class="row">
									<div class="col-lg-12">
										<div class="left-area">
												<h4 class="heading">{{ __("Category") }}*</h4>
										</div>
									</div>
									<div class="col-lg-12">
										<select id="cat" name="category_id" required="">
												<option>{{ __("Select Category") }}</option>
												@foreach($cats as $cat)
													<option data-href="{{ route('vendor-subcat-load',$cat->id) }}" value="{{$cat->id}}" {{$cat->id == $data->category_id ? "selected":""}} >{{$cat->name}}</option>
												@endforeach
										</select>
									</div>
								</div>

								<div class="row">
									<div class="col-lg-12">
										<div class="left-area">
											<h4 class="heading">{{ __("Sub Category") }}*</h4>
										</div>
									</div>
									<div class="col-lg-12">
											<select id="subcat" name="subcategory_id">
												<option value="">{{ __("Select Sub Category") }}</option>
												@if($data->subcategory_id == null)
													@foreach($data->category->subs as $sub)
													<option data-href="{{ route('vendor-childcat-load',$sub->id) }}" value="{{$sub->id}}" >{{$sub->name}}</option>
													@endforeach
												@else
													@foreach($data->category->subs as $sub)
													<option data-href="{{ route('vendor-childcat-load',$sub->id) }}" value="{{$sub->id}}" {{$sub->id == $data->subcategory_id ? "selected":""}} >{{$sub->name}}</option>
													@endforeach                                                  
												@endif
											</select>
									</div>
								</div>

								<div class="row">
									<div class="col-lg-12">
										<div class="left-area">
											<h4 class="heading">{{ __("Child Category") }}*</h4>
										</div>
									</div>
									<div class="col-lg-12">
											<select id="childcat" name="childcategory_id" {{$data->subcategory_id == null ? "disabled":""}}>
												  <option value="">{{ __("Select Child Category") }}</option>
													@if($data->subcategory_id != null)
														@if($data->childcategory_id == null)
															@foreach($data->subcategory->childs as $child)
															<option value="{{$child->id}}" >{{$child->name}}</option>
															@endforeach
														@else
															@foreach($data->subcategory->childs as $child)
															<option value="{{$child->id}} " {{$child->id == $data->childcategory_id ? "selected":""}}>{{$child->name}}</option>
															@endforeach
														@endif                                               
													@endif
											</select>
									</div>
								</div>

								<div class="{{ !empty($data->size) ? "showbox":"" }}" id="stckprod">
									<div class="row">
										<div class="col-lg-12">
											<div class="left-area">
												<h4 class="heading">{{ __("Product Stock") }}*</h4>
												<p class="sub-heading">{{ __("(Leave Empty will Show Always Available)") }}</p>
											</div>
										</div>
										<div class="col-lg-12">
											<input name="stock" type="text" class="input-field" placeholder="{{ __("e.g 20") }}" value="{{ $data->stock }}">
											<div class="checkbox-wrapper">
												<input type="checkbox" name="measure_check" class="checkclick" id="allowProductMeasurement" value="1" {{ $data->measure == null ? '' : 'checked' }}>
												<label for="allowProductMeasurement">{{ __("Allow Product Measurement") }}</label>
											</div>
										</div>
									</div>
								</div>

								<div class="{{ $data->measure == null ? 'showbox' : '' }}">
									<div class="row">
										<div class="col-lg-6">
											<div class="left-area">
												<h4 class="heading">{{ __("Product Measurement") }}*</h4>
											</div>
										</div>
										<div class="col-lg-12">
												<select id="product_measure">
												  <option value="" {{$data->measure == null ? 'selected':''}}>{{ __("None") }}</option>
												  <option value="Gram" {{$data->measure == 'Gram' ? 'selected':''}}>{{ __("Gram") }}</option>
												  <option value="Kilogram" {{$data->measure == 'Kilogram' ? 'selected':''}}>{{ __("Kilogram") }}</option>
												  <option value="Litre" {{$data->measure == 'Litre' ? 'selected':''}}>{{ __("Litre") }}</option>
												  <option value="Pound" {{$data->measure == 'Pound' ? 'selected':''}}>{{ __("Pound") }}</option>
												  <option value="Custom" {{ in_array($data->measure,explode(',', 'Gram,Kilogram,Litre,Pound')) ? '' : 'selected' }}>{{ __("Custom") }}</option>
												</select>
										</div>
										<div class="col-lg-12 {{ in_array($data->measure,explode(',', 'Gram,Kilogram,Litre,Pound')) ? 'hidden' : '' }}" id="measure">
											<input name="measure" type="text" id="measurement" class="input-field" placeholder="Enter Unit" value="{{$data->measure}}">
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-lg-12">
										<div class="left-area">

										</div>
									</div>
									<div class="col-lg-12">
										<ul class="list">
											<li>
												<input class="checkclick1" name="product_condition_check" type="checkbox" id="conditionCheck" value="1" {{ $data->product_condition != 0 ? "checked":"" }}>
												<label for="conditionCheck">{{ __('Allow Product Condition') }}</label>
											</li>
										</ul>
									</div>
								</div>


								<div class="{{ $data->product_condition == 0 ? "showbox":"" }}">
									<div class="row">
										<div class="col-lg-12">
											<div class="left-area">
												<h4 class="heading">{{ __('Product Condition') }}*</h4>
											</div>
										</div>
										<div class="col-lg-12">
											<select name="product_condition">
												<option value="2" {{$data->product_condition == 2 ? "selected":""}}>{{ __('New') }}</option>
												<option value="1" {{$data->product_condition == 1 ? "selected":""}}>{{ __('Used') }}</option>
											</select>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-lg-12">
										<div class="left-area">

										</div>
									</div>
									<div class="col-lg-12">
										<ul class="list">
											<li>
												<input class="checkclick1" name="shipping_time_check" type="checkbox" id="check1" value="1" {{$data->ship != null ? "checked":""}}>
												<label for="check1">{{ __("Allow Estimated Shipping Time") }}</label>
											</li>
										</ul>
									</div>
								</div>


								<div class="{{ $data->ship != null ? "":"showbox" }}">
									<div class="row">
										<div class="col-lg-12">
											<div class="left-area">
												<h4 class="heading">{{ __("Product Estimated Shipping Time") }}* </h4>
											</div>
										</div>
										<div class="col-lg-12">
											<input type="text" class="input-field" placeholder="Estimated Shipping Time" name="ship" value="{{ $data->ship == null ? "" : $data->ship }}">
										</div>
									</div>
								</div>

								
								<div class="row">
									<div class="col-lg-12">
										<div class="left-area">

										</div>
									</div>
									<div class="col-lg-12">
										<ul class="list">
											<li>
												<input class="checkclickc" name="color_check" type="checkbox" id="check3" value="1" {{ !empty($data->color_all) ? "checked":"" }}>
												<label for="check3">{{ __('Allow Product Colors') }}</label>
											</li>
										</ul>
									</div>
								</div>

								<div class="{{ !empty($data->color_all) ? "":"showbox" }}">
									<div class="row">
										@if(!empty($data->color_all)) 
										<div  class="col-lg-12">
												<div class="left-area">
													<h4 class="heading">
														{{ __('Product Colors') }}*
													</h4>
													<p class="sub-heading">
														{{ __('(Choose Your Favorite Colors)') }}
													</p>
												</div>
											</div>
											<div  class="col-lg-12">
													<div class="select-input-color" id="color-section">
														@foreach(array_unique(explode(',',$data->color_all)) as $ct)
														<div class="color-area">
															<span class="remove color-remove"><i class="fas fa-times"></i></span>
															<div class="input-group colorpicker-component cp">
															  <input type="text" name="color_all[]" value="{{ $ct }}"  class="input-field cp tcolor"/>
															  <span class="input-group-addon"><i></i></span>
															</div>
														 </div>
														 @endforeach
													</div>
												<a href="javascript:;" id="color-btn" class="add-more mt-4 mb-3"><i class="fas fa-plus"></i>{{ __('Add More Color') }} </a>
											</div>

										@else

											<div  class="col-lg-12">
												<div class="left-area">
													<h4 class="heading">
														{{ __('Product Colors') }}*
													</h4>
													<p class="sub-heading">
														{{ __('(Choose Your Favorite Colors)') }}
													</p>
												</div>
											</div>
											<div  class="col-lg-12">
													<div class="select-input-color" id="color-section">
														<div class="color-area">
															<span class="remove color-remove"><i class="fas fa-times"></i></span>
															<div class="input-group colorpicker-component cp">
															  <input type="text"  name="color_all[]"  class="input-field cp tcolor"/>
															  <span class="input-group-addon"><i></i></span>
															</div>
														 </div>
													</div>
												<a href="javascript:;" id="color-btn" class="add-more mt-4 mb-3"><i class="fas fa-plus"></i>{{ __('Add More Color') }} </a>
											</div>
										@endif
									</div>
								</div>
								<div class="row">
									<div class="col-lg-12">
										<div class="left-area">

										</div>
									</div>
									<div class="col-lg-12">
										<ul class="list">
											<li>
												<input class="checkclicks" name="size_check" type="checkbox" id="tcheck" value="1" {{ !empty($data->size_all) ? "checked":"" }}>
												<label for="tcheck">{{ __('Allow Product Sizes') }}</label>
											</li>
										</ul>
									</div>
								</div>

								<div class="{{ !empty($data->size_all) ? "":"showbox" }}">
									@if(!empty($data->size_all))
									<div class="row">
											<div  class="col-lg-12">
												<div class="left-area">
													<h4 class="heading">
														{{ __('Product Size') }}*
													</h4>
													<p class="sub-heading">
														{{ __('(eg. S,M,L,XL,XXL,3XL,4XL)') }}
													</p>
												</div>
											</div>
											<div  class="col-lg-12">
													<div class="select-input-tsize" id="tsize-section">
														@foreach(array_unique(explode(',',$data->size_all)) as $dt)
														<div class="tsize-area">
															<span class="remove tsize-remove"><i class="fas fa-times"></i></span>
															<input  type="text" name="size_all[]" class="input-field tsize" placeholder="{{ __('Enter Product Size') }}" value="{{ $dt }}"  required="" >
														 </div>
														@endforeach
													</div>
												<a href="javascript:;" id="tsize-btn" class="add-more mt-4 mb-3"><i class="fas fa-plus"></i>{{ __('Add More Size') }} </a>
											</div>
									</div>
									@else
									<div class="row">
											<div  class="col-lg-12">
												<div class="left-area">
													<h4 class="heading">
														{{ __('Product Size') }}*
													</h4>
													<p class="sub-heading">
														{{ __('(eg. S,M,L,XL,XXL,3XL,4XL)') }}
													</p>
												</div>
											</div>
											<div  class="col-lg-12">
													<div class="select-input-tsize" id="tsize-section">
														<div class="tsize-area">
															<span class="remove tsize-remove"><i class="fas fa-times"></i></span>
															<input  type="text" name="size_all[]" class="input-field tsize" placeholder="{{ __('Enter Product Size') }}"  >
															
														 </div>
													</div>
												<a href="javascript:;" id="tsize-btn" class="add-more mt-4 mb-3"><i class="fas fa-plus"></i>{{ __('Add More Size') }} </a>
											</div>
									</div>
									@endif
								</div>


								<div class="row">
									<div class="col-lg-12">
										<div class="left-area">
											<h4 class="heading">
												{{ __("Product Description") }}*
											</h4>
										</div>
									</div>
									<div class="col-lg-12">
										<div class="text-editor">
											<textarea name="details" class="nic-edit-p">{{$data->details}}</textarea> 
										</div>
									</div>
								</div>
								
								<div class="row">
									<div class="col-lg-12">
										<div class="left-area">
											<h4 class="heading">
												{{ __("Product Buy/Return Policy") }}*
											</h4>
										</div>
									</div>
									<div class="col-lg-12">
										<div class="text-editor">
											<textarea name="policy" class="nic-edit-p">{{$data->policy}}</textarea> 
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-lg-12">
										<div class="checkbox-wrapper">
											<input type="checkbox" name="seo_check" value="1" class="checkclick" id="allowProductSEO" {{ ($data->meta_tag != null || strip_tags($data->meta_description) != null) ? 'checked':'' }}>
						                    <label for="allowProductSEO">{{ __("Allow Product SEO") }}</label>
										</div>
									</div>
								</div>


								<div class="{{ ($data->meta_tag == null && strip_tags($data->meta_description) == null) ? "showbox":"" }}">
									<div class="row">
									  <div class="col-lg-12">
										<div class="left-area">
											<h4 class="heading">{{ __("Meta Tags") }} *</h4>
										</div>
									  </div>
									  <div class="col-lg-12">
										<ul id="metatags" class="myTags">
											@if(!empty($data->meta_tag))
											  @foreach ($data->meta_tag as $element)
												<li>{{  $element }}</li>
											  @endforeach
										  @endif
										</ul>
									  </div>
									</div>  

									<div class="row">
									  <div class="col-lg-12">
										<div class="left-area">
										  <h4 class="heading">
											  {{ __("Meta Description") }} *
										  </h4>
										</div>
									  </div>
									  <div class="col-lg-12">
										<div class="text-editor">
										  <textarea name="meta_description" class="input-field" placeholder="{{ __("Meta Description") }}">{{ $data->meta_description }}</textarea> 
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
		<div class="col-lg-4">
			<div class="add-product-content">
				<div class="row">
					<div class="col-lg-12">
						<div class="product-description">
							<div class="body-area">
								<div class="row">
									<div class="col-lg-12">
										<div class="left-area">
											<h4 class="heading">{{ __("Feature Image Source") }}*</h4>
										</div>
									</div>
									<div class="col-lg-12">
										<select id="imageSource" name="image_source">
											<option value="file" {{ !filter_var($data->photo,FILTER_VALIDATE_URL) ? 'selected' : '' }}>{{ __("File") }}</option>
											<option value="link" {{ filter_var($data->photo,FILTER_VALIDATE_URL) ? 'selected' : '' }}>{{ __("Link") }}</option>
										</select>
									</div>
								</div>


							<div id="f-file" style="{{ filter_var($data->photo,FILTER_VALIDATE_URL) ? 'display:none' : '' }}">
								 <div class="row">
									<div class="col-lg-12">
									  <div class="left-area">
										  <h4 class="heading">{{ __("Feature Image") }} *</h4>
									  </div>
									</div>

									<div class="col-lg-12">
										<div class="row">
											<div class="panel panel-body">
												<div class="span4 cropme text-center" id="landscape" style="width: 100%; height: 285px; border: 1px dashed #ddd; background: #f1f1f1;">
													<a href="javascript:;" id="crop-image" class=" mybtn1" style="">
														<i class="icofont-upload-alt"></i> {{ __('Upload Image Here') }}
													</a>
												</div>
											</div>
										</div>
									</div>
								  </div>

								  <input type="hidden" id="feature_photo" name="photo" value="{{ $data->photo }}" accept="image/*">
							 </div>

							  <div id="f-link" style="{{ !filter_var($data->photo,FILTER_VALIDATE_URL) ? 'display:none' : '' }}">
								  <div class="row">
									  <div class="col-lg-12">
										  <div class="left-area">
											  <h4 class="heading">{{ __("Feature Image Link ") }}*</h4>
										  </div>
									  </div>
									  <div class="col-lg-12">
										  <input type="text" name="photolink" value="{{ $data->photo }}" class="input-field">
									  </div>
								  </div>
							  </div>
							 
								<div class="row">
									<div class="col-lg-5">
										<div class="left-area">
											<h4 class="heading">
												{{ __("Product Gallery Images") }} *
											</h4>
										</div>
									</div>
									<div class="col-lg-7">
										<a href="javascript" class="set-gallery"  data-toggle="modal" data-target="#setgallery">
											<input type="hidden" value="{{$data->id}}">
											<i class="icofont-plus"></i> {{ __("Set Gallery") }}
										</a>
									</div>
								</div>

								<div class="row">
									<div class="col-lg-12">
										<div class="left-area">
											<h4 class="heading">
												{{ __("Product Current Price") }}*
											</h4>
											<p class="sub-heading">
												({{ __("In") }} {{$sign->name}})
											</p>
										</div>
									</div>
									<div class="col-lg-12">
										<input name="price" step="0.1" type="number" class="input-field" placeholder="{{ __("e.g 20") }}" value="{{round($data->price * $sign->value , 2)}}" required="" min="0">
									</div>
								</div>

								<div class="row">
									<div class="col-lg-12">
										<div class="left-area">
											<h4 class="heading">{{ __("Product Discount Price") }}*</h4>
											<p class="sub-heading">{{ __("(Optional)") }}</p>
										</div>
									</div>
									<div class="col-lg-12">
										<input name="previous_price" step="0.1" type="number" class="input-field" placeholder="{{ __("e.g 20") }}" value="{{round($data->previous_price * $sign->value , 2)}}" min="0">
									</div>
								</div>

								<div class="row">
									<div class="col-lg-12">
										<div class="left-area">
											<h4 class="heading">{{ __('Youtube Video URL') }}*</h4>
											<p class="sub-heading">{{ __('(Optional)') }}</p>
										</div>
									</div>
									<div class="col-lg-12">
										<input  name="youtube" type="text" class="input-field" placeholder="{{ __("Enter Youtube Video URL") }}" value="{{$data->youtube}}">
									</div>
								</div>


								
								<div class="row">
									<div class="col-lg-12">
										<div class="left-area">

										</div>
									</div>
									<div class="col-lg-12">
										<div class="featured-keyword-area">
											<div class="heading-area">
												<h4 class="title">{{ __("Feature Tags") }}</h4>
											</div>

											<div class="feature-tag-top-filds" id="feature-section">
												@if(!empty($data->features))
													 @foreach($data->features as $key => $data1)
														<div class="feature-area">
															<span class="remove feature-remove"><i class="fas fa-times"></i></span>
															<div class="row">
																<div class="col-lg-6">
																<input type="text" name="features[]" class="input-field" placeholder="{{ __("Enter Your Keyword") }}" value="{{ $data->features[$key] }}">
																</div>

																<div class="col-lg-6">
																	<div class="input-group colorpicker-component cp">
																	<input type="text" name="colors[]" value="{{ $data->colors[$key] }}" class="input-field cp"/>
																	<span class="input-group-addon"><i></i></span>
																	</div>
																</div>
															</div>
														</div>
													@endforeach
												@else 
													<div class="feature-area">
														<span class="remove feature-remove"><i class="fas fa-times"></i></span>
														<div class="row">
															<div class="col-lg-6">
															<input type="text" name="features[]" class="input-field" placeholder="{{ __("Enter Your Keyword") }}">
															</div>

															<div class="col-lg-6">
																<div class="input-group colorpicker-component cp">
																<input type="text" name="colors[]" value="#000000" class="input-field cp"/>
																<span class="input-group-addon"><i></i></span>
																</div>
															</div>
														</div>
													</div>
												@endif
											</div>
											<a href="javascript:;" id="feature-btn" class="add-fild-btn"><i class="icofont-plus"></i> {{ __("Add More Field") }}</a>
										</div>
									</div>
								</div>


								<div class="row">
								  <div class="col-lg-12">
									<div class="left-area">
										<h4 class="heading">{{ __("Tags") }} *</h4>
									</div>
								  </div>
								  <div class="col-lg-12">
									<ul id="tags" class="myTags">
										@if(!empty($data->tags))
											@foreach ($data->tags as $element)
											  <li>{{  $element }}</li>
											@endforeach
										@endif
									</ul>
								  </div>
								</div>

								<div class="row">
									<div class="col-lg-3">
										<div class="left-area">
											
										</div>
									</div>
									<div class="col-lg-8 text-center">
										<button class="addProductSubmit-btn" type="submit">{{ __("Save") }}</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
</div>


<div class="modal fade" id="setgallery" tabindex="-1" role="dialog" aria-labelledby="setgallery" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalCenterTitle">{{ __("Image Gallery") }}</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="top-area">
					<div class="row">
						<div class="col-sm-6 text-right">
							<div class="upload-img-btn">
								<form method="POST" enctype="multipart/form-data" id="form-gallery">
									@csrf
									<input type="hidden" id="pid" name="product_id" value="">
									<input type="file" name="gallery[]" class="hidden" id="uploadgallery"
										accept="image/*" multiple>
									<label for="image-upload" id="prod_gallery"><i
											class="icofont-upload-alt"></i>{{ __("Upload File") }}</label>
								</form>
							</div>
						</div>
						<div class="col-sm-6">
							<a href="javascript:;" class="upload-done" data-dismiss="modal"> <i
									class="fas fa-check"></i> {{ __("Done") }}</a>
						</div>
						<div class="col-sm-12 text-center">( <small>{{ __("You can upload multiple Images.") }}</small> )</div>
					</div>
				</div>
				<div class="gallery-images">
					<div class="selected-image">
						<div class="row">


						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection

@section('scripts')

<script type="text/javascript">
	// Gallery Section Update

	(function($) {
		"use strict";

	$(document).on("click", ".set-gallery", function () {
		var pid = $(this).find('input[type=hidden]').val();
		$('#pid').val(pid);
		$('.selected-image .row').html('');
		$.ajax({
			type: "GET",
			url: "{{ route('vendor-gallery-show') }}",
			data: {
				id: pid
			},
			success: function (data) {
				if (data[0] == 0) {
					$('.selected-image .row').addClass('justify-content-center');
					$('.selected-image .row').html('<h3>{{ __("No Images Found.") }}</h3>');
				} else {
					$('.selected-image .row').removeClass('justify-content-center');
					$('.selected-image .row h3').remove();
					var arr = $.map(data[1], function (el) {
						return el
					});

					for (var k in arr) {
						$('.selected-image .row').append('<div class="col-sm-6">' +
							'<div class="img gallery-img">' +
							'<span class="remove-img"><i class="fas fa-times"></i>' +
							'<input type="hidden" value="' + arr[k]['id'] + '">' +
							'</span>' +
							'<a href="' + '{{asset('
							assets / images / galleries ').' / '}}' + arr[k]['photo'] +
							'" target="_blank">' +
							'<img src="' + '{{asset('
							assets / images / galleries ').' / '}}' + arr[k]['photo'] +
							'" alt="gallery image">' +
							'</a>' +
							'</div>' +
							'</div>');
					}
				}

			}
		});
	});


	$(document).on('click', '.remove-img', function () {
		var id = $(this).find('input[type=hidden]').val();
		$(this).parent().parent().remove();
		$.ajax({
			type: "GET",
			url: "{{ route('vendor-gallery-delete') }}",
			data: {
				id: id
			}
		});
	});

	$(document).on('click', '#prod_gallery', function () {
		$('#uploadgallery').click();
	});


	$("#uploadgallery").change(function () {
		$("#form-gallery").submit();
	});

	$(document).on('submit', '#form-gallery', function () {
		$.ajax({
			url: "{{ route('vendor-gallery-store') }}",
			method: "POST",
			data: new FormData(this),
			dataType: 'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success: function (data) {
				if (data != 0) {
					$('.selected-image .row').removeClass('justify-content-center');
					$('.selected-image .row h3').remove();
					var arr = $.map(data, function (el) {
						return el
					});
					for (var k in arr) {
						$('.selected-image .row').append('<div class="col-sm-6">' +
							'<div class="img gallery-img">' +
							'<span class="remove-img"><i class="fas fa-times"></i>' +
							'<input type="hidden" value="' + arr[k]['id'] + '">' +
							'</span>' +
							'<a href="' + '{{asset('
							assets / images / galleries ').' / '}}' + arr[k]['photo'] +
							'" target="_blank">' +
							'<img src="' + '{{asset('
							assets / images / galleries ').' / '}}' + arr[k]['photo'] +
							'" alt="gallery image">' +
							'</a>' +
							'</div>' +
							'</div>');
					}
				}

			}

		});
		return false;

})(jQuery);

	});


	// Gallery Section Update Ends	
</script>

<script src="{{asset('assets/admin/js/jquery.Jcrop.js')}}"></script>

<script src="{{asset('assets/admin/js/jquery.SimpleCropper.js')}}"></script>

<script type="text/javascript">

(function($) {
		"use strict";

	$('.cropme').simpleCropper();

})(jQuery);


</script>

<script type="text/javascript">

(function($) {
		"use strict";

	$(document).ready(function () {

		let html =
			`<img src="{{ empty($data->photo) ? asset('assets/images/noimage.png') : (filter_var($data->photo, FILTER_VALIDATE_URL) ? $data->photo : asset('assets/images/products/'.$data->photo)) }}" alt="">`;
		$(".span4.cropme").html(html);

		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

	});

	$('.ok').on('click', function () {

		setTimeout(
			function () {

				var img = $('#feature_photo').val();

				$.ajax({
					url: "{{route('vendor-prod-upload-update',$data->id)}}",
					type: "POST",
					data: {
						"image": img
					},
					success: function (data) {
						if (data.status) {
							$('#feature_photo').val(data.file_name);
						}
						if ((data.errors)) {
							for (var error in data.errors) {
								$.notify(data.errors[error], "danger");
							}
						}
					}
				});

			}, 1000);

	});

})(jQuery);

</script>

<script type="text/javascript">

(function($) {
		"use strict";

	$('#imageSource').on('change', function () {
		var file = this.value;
		if (file == "file") {
			$('#f-file').show();
			$('#f-link').hide();
			$('#f-link').find('input').prop('required', false);
		}
		if (file == "link") {
			$('#f-file').hide();
			$('#f-link').show();
			$('#f-link').find('input').prop('required', true);
		}
	});

})(jQuery);

</script>

@include('partials.admin.product.product-scripts')
@endsection