<div class="products-header d-flex justify-content-between align-items-center py-10 px-20 bg-light md-mt-30">
    <div class="products-header-left d-flex align-items-center">
       <h6 class="woocommerce-products-header__title page-title"> <strong> {{ __('Products')  }}</strong>  </h6>
       <div class="woocommerce-result-count"></div> 
    </div>
    <div class="products-header-right">
       <form class="woocommerce-ordering" method="get">
          <select name="sort" class="orderby short-item" aria-label="Shop order" id="sortby">
             <option value="date_desc">{{ __('Latest Product') }}</option>
             <option value="date_asc">{{ __('Oldest Product') }}</option>
             <option value="price_asc">{{ __('Lowest Price') }}</option>
             <option value="price_desc">{{ __('Highest Price') }}</option>
          </select>
          @if($gs->product_page != null)
          <select id="pageby" name="pageby" class="short-itemby-no">
             @foreach (explode(',',$gs->product_page) as $element)
             <option value="{{ $element }}">{{ $element }}</option>
             @endforeach
          </select>
          @else
          <input type="hidden" id="pageby" name="paged" value="{{ $gs->page_count }}">
          <input type="hidden" name="shop-page-layout" value="left-sidebar">
          @endif
       </form>
       <div class="products-view">
          <a  class="grid-view check_view" data-shopview="grid-view" href="javascript:;"><i class="flaticon-menu-1 flat-mini"></i></a>
          <a class="list-view check_view" data-shopview="list-view" href="javascript:;"><i class="flaticon-list flat-mini"></i></a>
       </div>
    </div>
 </div>
