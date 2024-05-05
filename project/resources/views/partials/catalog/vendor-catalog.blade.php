<div class="col-lg-3">
    <div id="sidebar" class="widget-title-bordered-full">
        <form id="catalogForm" action="{{ route('front.category', [Request::route('category'), Request::route('subcategory'), Request::route('childcategory')]) }}" method="GET">

        <div id="woocommerce_product_categories-4" class="widget woocommerce widget_product_categories widget-toggle">
            <h2 class="widget-title">{{ __('Product categories') }}</h2>
            <ul class="product-categories">
                @foreach (App\Models\Category::where('language_id',$langg->id)->where('status',1)->get() as $category)

                <li class="cat-item cat-parent">
                    <a href="{{route('front.category', $category->slug)}}{{!empty(request()->input('search')) ? '?search='.request()->input('search') : ''}}" class="category-link" id="cat">{{ $category->name }} <span class="count"></span></a>

                    @if($category->subs->count() > 0)
                        <span class="has-child"></span>
                        <ul class="children">
                            @foreach (App\Models\Subcategory::where('category_id',$category->id)->get() as $subcategory)
                            <li class="cat-item cat-parent">
                                <a href="{{route('front.category', [$category->slug, $subcategory->slug])}}{{!empty(request()->input('search')) ? '?search='.request()->input('search') : ''}}" class="category-link {{ isset($subcat) ? ($subcat->id == $subcategory->id ? 'active' : '') : '' }}">{{$subcategory->name}} <span class="count"></span></a>


                                @if($subcategory->childs->count()!=0)
                                    <span class="has-child"></span>
                                    <ul class="children">
                                        @foreach (DB::table('childcategories')->where('subcategory_id',$subcategory->id)->get() as $key => $childelement)
                                        <li class="cat-item ">
                                            <a href="{{route('front.category', [$category->slug, $subcategory->slug, $childelement->slug])}}{{!empty(request()->input('search')) ? '?search='.request()->input('search') : ''}}" class="category-link {{ isset($childcat) ? ($childcat->id == $childelement->id ? 'active' : '') : '' }}"> {{$childelement->name}} <span class="count"></span></a>
                                        </li>
                                        @endforeach
                                    </ul>

                                @endif
                            </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
                @endforeach
            </ul>
        </div>

        <div id="bigbazar-price-filter-list-1" class="widget bigbazar_widget_price_filter_list widget_layered_nav widget-toggle">
            <h2 class="widget-title">{{ __('Filter by Price') }}</h2>
            <ul class="price-filter-list">
                <div class="price-range-block">
                    <div id="slider-range" class="price-filter-range" name="rangeInput"></div>
                    <div class="livecount">
                        <input type="number" name="min"  oninput="" id="min_price" class="price-range-field" />
                        <span>
                            {{ __('To') }}
                        </span>
                        <input type="number" name="max"  oninput="" id="max_price" class="price-range-field" />
                    </div>
                </div>

                <button class="filter-btn btn btn-primary mt-3 mb-4" type="submit">{{ __('Search') }}</button>
            </ul>
        </div>

    </form>


    @if ((!empty($cat) && !empty(json_decode($cat->attributes, true))) || (!empty($subcat) && !empty(json_decode($subcat->attributes, true))) || (!empty($childcat) && !empty(json_decode($childcat->attributes, true))))

    <form id="attrForm" action="{{ route('front.category',[Request::route('category'),Request::route('subcategory'),Request::route('childcategory')]) }}" method="post">

        @if (!empty($cat) && !empty(json_decode($cat->attributes, true)))
        @foreach ($cat->attributes as $key => $attr)

        <div id="bigbazar-attributes-filter-{{$attr->name}}" class="widget woocommerce bigbazar-attributes-filter widget_layered_nav widget-toggle">
            <h2 class="widget-title">{{$attr->name}}</h2>
            <ul class="swatch-filter-pa_color">
                @if (!empty($attr->attribute_options))
                      @foreach ($attr->attribute_options as $key => $option)
                      <div class="form-check ml-0 pl-0">
                        <input name="{{$attr->input_name}}[]" class="form-check-input attribute-input" type="checkbox" id="{{$attr->input_name}}{{$option->id}}" value="{{$option->name}}">
                        <label class="form-check-label" for="{{$attr->input_name}}{{$option->id}}">{{$option->name}}</label>
                      </div>
                      @endforeach
                    @endif
            </ul>
        </div>
        @endforeach
        @endif

        @if (!empty($subcat) && !empty(json_decode($subcat->attributes, true)))
            @foreach ($subcat->attributes as $key => $attr)
                <div id="bigbazar-attributes-filter-{{$attr->name}}" class="widget woocommerce bigbazar-attributes-filter widget_layered_nav widget-toggle">
                    <h2 class="widget-title">{{$attr->name}}</h2>
                    <ul class="swatch-filter-pa_color">
                        @if (!empty($attr->attribute_options))
                              @foreach ($attr->attribute_options as $key => $option)
                              <div class="form-check ml-0 pl-0">
                                <input name="{{$attr->input_name}}[]" class="form-check-input attribute-input" type="checkbox" id="{{$attr->input_name}}{{$option->id}}" value="{{$option->name}}">
                                <label class="form-check-label" for="{{$attr->input_name}}{{$option->id}}">{{$option->name}}</label>
                              </div>
                              @endforeach
                            @endif
                    </ul>
                </div>
            @endforeach
        @endif

    @if (!empty($childcat) && !empty(json_decode($childcat->attributes, true)))
        @foreach ($childcat->attributes as $key => $attr)
            <div id="bigbazar-attributes-filter-{{$attr->name}}" class="widget woocommerce bigbazar-attributes-filter widget_layered_nav widget-toggle">
                <h2 class="widget-title">{{$attr->name}}</h2>
                <ul class="swatch-filter-pa_color">
                    @if (!empty($attr->attribute_options))
                          @foreach ($attr->attribute_options as $key => $option)
                          <div class="form-check ml-0 pl-0">
                            <input name="{{$attr->input_name}}[]" class="form-check-input attribute-input" type="checkbox" id="{{$attr->input_name}}{{$option->id}}" value="{{$option->name}}">
                            <label class="form-check-label" for="{{$attr->input_name}}{{$option->id}}">{{$option->name}}</label>
                          </div>
                          @endforeach
                        @endif
                </ul>
            </div>
        @endforeach
     @endif



    </form>
    @endif
        <div class="row">
            <div class="col-12">
                <div class="section-head border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex section-head-side-title">
                        <h5 class="font-700 text-dark mb-0">{{ __('Recent Product') }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="product-style-2 owl-carousel owl-nav-hover-primary nav-top-right single-carousel dot-disable product-list e-bg-white">

                    @foreach (array_chunk($latest_products->toArray(),3) as $item)

                    <div class="item">
                        <div class="row row-cols-1">
                            @foreach ($item as $prod)

                            <div class="col mb-1">
                                <div class="product type-product">
                                    <div class="product-wrapper">
                                        <div class="product-image">
                                            <a href="{{ route('front.product', $prod['slug']) }}" class="woocommerce-LoopProduct-link"><img class="lazy" data-src="{{ $prod['thumbnail'] ? asset('assets/images/thumbnails/'.$prod['thumbnail'] ):asset('assets/images/noimage.png') }}" alt="Product Image"></a>
                                            <div class="wishlist-view">
                                                <div class="quickview-button">
                                                    <a class="quickview-btn" href="{{ route('front.product', $prod['slug']) }}" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Quick View" aria-label="Quick View">{{ __('Quick View') }}</a>
                                                </div>
                                                <div class="whishlist-button">
                                                    <a class="add_to_wishlist" href="#" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Add to Wishlist" aria-label="Add to Wishlist">{{ __('Wishlist') }}</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="product-info">
                                            <h3 class="product-title"><a href="{{ route('front.product', $prod['slug']) }}">{{ App\Models\Product::whereId($prod['id'])->first()->showName() }}</a></h3>
                                            <div class="product-price">
                                                <div class="price">
                                                    <ins>{{ App\Models\Product::whereId($prod['id'])->first()->showPrice() }}</ins>
                                                    <del>{{ App\Models\Product::whereId($prod['id'])->first()->showPreviousPrice() }}</del>
                                                </div>
                                                <div class="on-sale"><span>{{ round(App\Models\Product::whereId($prod['id'])->first()->offPercentage())}}</span><span>% off</span></div>
                                            </div>
                                            <div class="shipping-feed-back">
                                                <div class="star-rating">
                                                    <div class="rating-wrap">
                                                        <p><i class="fas fa-star"></i><span> {{ App\Models\Rating::ratings($prod['id']) }}</span></p>
                                                    </div>
                                                    <div class="rating-counts-wrap">
                                                        <p>({{ App\Models\Rating::ratingCount($prod['id']) }})</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    @endforeach



                </div>
            </div>
        </div>
    </div>
</div>
