@extends('layouts.vendor')
@section('styles')

    <link href="{{asset('assets/admin/css/product.css')}}" rel="stylesheet"/>
    <link href="{{asset('assets/admin/css/jquery.Jcrop.css')}}" rel="stylesheet"/>
    <link href="{{asset('assets/admin/css/Jcrop-style.css')}}" rel="stylesheet"/>

@endsection
@section('content')

    <div class="content-area">
        <div class="mr-breadcrumb">
            <div class="row">
                <div class="col-lg-12">
                    <h4 class="heading">{{ __('Physical Product') }} <a class="add-btn" href="{{ route('vendor-prod-types') }}"><i class="fas fa-arrow-left"></i> {{ __('Back') }}</a></h4>
                    <ul class="links">
                        <li>
                            <a href="{{ route('vendor.dashboard') }}">{{ __("Dashboard") }} </a>
                        </li>
                        <li>
                            <a href="javascript:;">{{ __("Product") }} </a>
                        </li>
                        <li>
                            <a href="{{ route('vendor-brand-index') }}">{{ __("All Brands") }}</a>
                        </li>
                        <li>
                            <a href="{{ route('vendor-brand-create') }}">{{ __("Create Brands") }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <form id="geniusform" action="{{route('vendor-brand-store')}}" method="POST" enctype="multipart/form-data">
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
                                                    <h4 class="heading">{{ __('Brand Name') }}* </h4>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <input type="text" class="input-field" placeholder="{{ __('Enter Brand Name') }}" name="brand_name" required>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="left-area">
                                                    <h4 class="heading">{{ __('Brand Description') }}* </h4>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <textarea class="input-field" placeholder="{{ __('Enter Brand Description') }}" name="brand_description" required></textarea>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="left-area">
                                                    <h4 class="heading">{{ __('Brand Country') }}* </h4>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <input type="text" class="input-field" placeholder="{{ __('Enter Brand Country') }}" name="brand_country" required>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="left-area">
                                                    <h4 class="heading">{{ __('Brand Website') }}* </h4>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <input type="url" class="input-field" placeholder="{{ __('Enter Brand Website') }}" name="brand_website" required>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="left-area">
                                                    <h4 class="heading">{{ __('Brand Active Status') }}* </h4>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <select class="input-field" name="brand_is_active" required>
                                                    <option value="1">{{ __('Active') }}</option>
                                                    <option value="0">{{ __('Inactive') }}</option>
                                                </select>
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
                                                    <h4 class="heading">{{ __('Logo Image') }} *</h4>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="panel panel-body">
                                                    <div class="span4 cropme text-center" id="landscape"
                                                         style="width: 100%; height: 285px; border: 1px dashed #ddd; background: #f1f1f1;">
                                                        <a href="javascript:;" id="crop-image" class=" mybtn1" style="">
                                                            <i class="icofont-upload-alt"></i> {{ __('Upload Image Here') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" id="feature_photo" name="brand_logo" value="">


                                        <div class="row text-center">
                                            <div class="col-6 offset-3">
                                                <button class="addProductSubmit-btn" type="submit">{{ __('Create Brand') }}</button>
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
        <div class="modal-dialog modal-dialog-centered  modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">{{ __('Image Gallery') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="top-area">
                        <div class="row">
                            <div class="col-sm-6 text-right">
                                <div class="upload-img-btn">
                                    <label for="image-upload" id="prod_gallery"><i class="icofont-upload-alt"></i>{{ __('Upload File') }}</label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <a href="javascript:;" class="upload-done" data-dismiss="modal"> <i class="fas fa-check"></i> {{ __('Done') }}</a>
                            </div>
                            <div class="col-sm-12 text-center">( <small>{{ __('You can upload multiple Images.') }}</small> )</div>
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

    <script src="{{asset('assets/admin/js/jquery.Jcrop.js')}}"></script>
    <script src="{{asset('assets/admin/js/jquery.SimpleCropper.js')}}"></script>

    <script type="text/javascript">

        (function($) {
            "use strict";

// Gallery Section Insert

            $(document).on('click', '.remove-img' ,function() {
                var id = $(this).find('input[type=hidden]').val();
                $('#galval'+id).remove();
                $(this).parent().parent().remove();
            });

            $(document).on('click', '#prod_gallery' ,function() {
                $('#uploadgallery').click();
                $('.selected-image .row').html('');
                $('#geniusform').find('.removegal').val(0);
            });


            $("#uploadgallery").change(function(){
                var total_file=document.getElementById("uploadgallery").files.length;
                for(var i=0;i<total_file;i++)
                {
                    $('.selected-image .row').append('<div class="col-sm-6">'+
                        '<div class="img gallery-img">'+
                        '<span class="remove-img"><i class="fas fa-times"></i>'+
                        '<input type="hidden" value="'+i+'">'+
                        '</span>'+
                        '<a href="'+URL.createObjectURL(event.target.files[i])+'" target="_blank">'+
                        '<img src="'+URL.createObjectURL(event.target.files[i])+'" alt="gallery image">'+
                        '</a>'+
                        '</div>'+
                        '</div> '
                    );
                    $('#geniusform').append('<input type="hidden" name="galval[]" id="galval'+i+'" class="removegal" value="'+i+'">')
                }

            });

// Gallery Section Insert Ends

        })(jQuery);

    </script>

    <script type="text/javascript">

        (function($) {
            "use strict";

            $('.cropme').simpleCropper();

        })(jQuery);


        $(document).on('click','#size-check',function(){
            if($(this).is(':checked')){
                $('#default_stock').addClass('d-none')
            }else{
                $('#default_stock').removeClass('d-none');
            }
        })

    </script>


    @include('partials.admin.product.product-scripts')
@endsection
