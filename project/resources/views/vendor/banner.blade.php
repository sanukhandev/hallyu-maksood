@extends('layouts.vendor')
@section('content')

<div class="content-area">
	<div class="mr-breadcrumb">
		<div class="row">
			<div class="col-lg-12">
				<h4 class="heading">{{ __('Banner') }}</h4>

				<ul class="links">
					<li>
						<a href="{{ route('vendor.dashboard') }}">{{ __('Dashboard') }} </a>
					</li>
					<li>
						<a href="javascript:;">{{ __('Settings') }}</a>
					</li>
					<li>
						<a href="{{ route('vendor-banner') }}">{{ __('Banner') }}</a>
					</li>
				</ul>

			</div>
		</div>
	</div>
	<div class="add-product-content1">
		<div class="row">
			<div class="col-lg-12">
				<div class="product-description">
					<div class="body-area">

						<div class="gocover"
							style="background: url({{asset('assets/images/'.$gs->admin_loader)}}) no-repeat scroll center center rgba(45, 45, 45, 0.5);">
						</div>
						<form id="geniusform" action="{{ route('vendor-profile-update') }}" method="POST"
							enctype="multipart/form-data">
							{{csrf_field()}}


							@include('alerts.vendor.form-both')

							<div class="row">
								<div class="col-lg-4">
									<div class="left-area">
										<h4 class="heading">{{ __('Current Banner') }} *</h4>
									</div>
								</div>
								<div class="col-lg-7">
									<div class="img-upload full-width-img">
										<div id="image-preview" class="img-preview"
											style="background: url({{ $data->shop_image ? asset('assets/images/vendorbanner/'.$data->shop_image):asset('assets/images/noimage.png') }});">
											<label for="image-upload" class="img-label" id="image-label"><i
													class="icofont-upload-alt"></i>{{ __('Upload Banner') }}</label>
											<input type="file" name="shop_image" class="img-upload" id="image-upload">
										</div>
										<p class="text">{{ __('Prefered Size: (1920x220) or Square Sized Image') }}</p>
									</div>

								</div>
							</div>



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