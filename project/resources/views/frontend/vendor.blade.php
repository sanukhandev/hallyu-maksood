@extends('layouts.front')

@section('content')
@includeIf('partials.global.common-header')

<!-- breadcrumb -->
<div class="full-row bg-light overlay-dark py-5" style="background-image: url({{ $gs->breadcrumb_banner ? asset('assets/images/'.$gs->breadcrumb_banner):asset('assets/images/noimage.png') }}); background-position: center center; background-size: cover;">
    <div class="container">
        <div class="row text-center text-white">
            <div class="col-12">
                <h3 class="mb-2 text-white">{{ __('Products') }}</h3>
            </div>
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 d-inline-flex bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="{{ route('front.index') }}">{{ __('Home') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('Products') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<!-- breadcrumb -->

{{-- There are two product page. you have to give condition here --}}

<div class="full-row">
    <div class="container">
        <div class="row">
            @includeIf('partials.catalog.vendor-catalog')


            @if (count($vprods) > 0)
            <div class="col-lg-9">

                @includeIf('frontend.category')

                <div class="showing-products pt-30 pb-50 border-2 border-bottom border-light" id="ajaxContent">
                @includeIf('partials.product.vendor-product-different-view')
                </div>
                @include('frontend.pagination.vendor')
            </div>

            @else

            <div class="col-lg-9">

                @includeIf('frontend.category')

                <div class="col-lg-9">
                    <div class="card">
                        <div class="card-body">
                            <div class="page-center">
                                <h4 class="text-center">{{ __('No Product Found.') }}</h4>
                           </div>
                        </div>
                    </div>
                </div>
            </div>


            @endif

        </div>
    </div>
</div>





{{-- @includeIf('partials.product.grid') --}}

@includeIf('partials.global.common-footer')
@endsection

@section('script')

<script>
    let check_view = '';
    $(document).on('click','.check_view',function(){
        check_view = $(this).attr('data-shopview');
        filter();

    });


    // when dynamic attribute changes
    $(".attribute-input, #sortby, #pageby").on('change', function() {
      $(".ajax-loader").show();
      filter();
    });


  function filter() {
    let filterlink = '';

    if ($("#prod_name").val() != '') {
      if (filterlink == '') {
        filterlink += '{{route('front.category', [Request::route('category'), Request::route('subcategory'), Request::route('childcategory')])}}' + '?search='+$("#prod_name").val();
      } else {
        filterlink += '&search='+$("#prod_name").val();
      }
    }



    $(".attribute-input").each(function() {
      if ($(this).is(':checked')) {

        if (filterlink == '') {
          filterlink += '{{route('front.category', [Request::route('category'), Request::route('subcategory'), Request::route('childcategory')])}}' + '?'+$(this).attr('name')+'='+$(this).val();
        } else {
          filterlink += '&'+encodeURI($(this).attr('name'))+'='+$(this).val();

        }
      }
    });

    if ($("#sortby").val() != '') {
      if (filterlink == '') {
        filterlink += '{{route('front.category', [Request::route('category'), Request::route('subcategory'), Request::route('childcategory')])}}' + '?'+$("#sortby").attr('name')+'='+$("#sortby").val();
      } else {
        filterlink += '&'+$("#sortby").attr('name')+'='+$("#sortby").val();
      }
    }


    if ($("#min_price").val() != '') {
      if (filterlink == '') {
        filterlink += '{{route('front.category', [Request::route('category'), Request::route('subcategory'), Request::route('childcategory')])}}' + '?'+$("#min_price").attr('name')+'='+$("#min_price").val();
      } else {
        filterlink += '&'+$("#min_price").attr('name')+'='+$("#min_price").val();
      }
    }

    if ($("#max_price").val() != '') {
      if (filterlink == '') {
        filterlink += '{{route('front.category', [Request::route('category'), Request::route('subcategory'), Request::route('childcategory')])}}' + '?'+$("#max_price").attr('name')+'='+$("#max_price").val();
      } else {
        filterlink += '&'+$("#max_price").attr('name')+'='+$("#max_price").val();
      }
    }


    if ($("#pageby").val() != '') {
      if (filterlink == '') {
        filterlink += '{{route('front.category', [Request::route('category'), Request::route('subcategory'), Request::route('childcategory')])}}' + '?'+$("#pageby").attr('name')+'='+$("#pageby").val();
      } else {
        filterlink += '&'+$("#pageby").attr('name')+'='+$("#pageby").val();
      }
    }

    if(check_view){

        filterlink+= '&view_check='+check_view;
    }
    $("#ajaxContent").load(encodeURI(filterlink), function(data) {
      // add query string to pagination
       addToPagination();
      $(".ajax-loader").fadeOut(1000);
    });
  }

//   append parameters to pagination links
  function addToPagination() {


    // add to attributes in pagination links
    $('ul.pagination li a').each(function() {
      let url = $(this).attr('href');
      let queryString = '?' + url.split('?')[1]; // "?page=1234...."

      let urlParams = new URLSearchParams(queryString);
      let page = urlParams.get('page'); // value of 'page' parameter

      let fullUrl = '{{route('front.category', [Request::route('category'),Request::route('subcategory'),Request::route('childcategory')])}}?page='+page+'&search='+'{{urlencode(request()->input('search'))}}';

      $(".attribute-input").each(function() {
        if ($(this).is(':checked')) {
          fullUrl += '&'+encodeURI($(this).attr('name'))+'='+encodeURI($(this).val());
        }
      });

      if ($("#sortby").val() != '') {
        fullUrl += '&sort='+encodeURI($("#sortby").val());
      }

      if ($("#min_price").val() != '') {
        fullUrl += '&min='+encodeURI($("#min_price").val());
      }

      if ($("#max_price").val() != '') {
        fullUrl += '&max='+encodeURI($("#max_price").val());
      }

      if ($("#pageby").val() != '') {
        fullUrl += '&pageby='+encodeURI($("#pageby").val());
      }


      $(this).attr('href', fullUrl);
    });
  }

</script>

<script type="text/javascript">

(function($) {
		"use strict";

  $(function () {
    $("#slider-range").slider({
    range: true,
    orientation: "horizontal",
    min: {{ $gs->min_price }},
    max: {{ $gs->max_price }},
    values: [{{ isset($_GET['min']) ? $_GET['min'] : $gs->min_price }}, {{ isset($_GET['max']) ? $_GET['max'] : $gs->max_price }}],
    step: 1,

    slide: function (event, ui) {
      if (ui.values[0] == ui.values[1]) {
        return false;
      }

      $("#min_price").val(ui.values[0]);
      $("#max_price").val(ui.values[1]);
    }
    });

    $("#min_price").val($("#slider-range").slider("values", 0));
    $("#max_price").val($("#slider-range").slider("values", 1));

  });

})(jQuery);

</script>

@endsection

