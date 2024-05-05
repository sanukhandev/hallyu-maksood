<div class="top-header font-400 d-none d-lg-block py-1 text-general">
    <div class="container">
       <div class="row align-items-center">
          <div class="col-lg-4 sm-mx-none">
             <div class="d-flex align-items-center text-general">
                <i class="flaticon-phone-call flat-mini me-2 text-general"></i>
                <span class="text-dark"> {{ $ps->phone }}</span>
             </div>
          </div>
          <div class="col-lg-8 ">
             <ul class="top-links text-general ms-auto  d-flex justify-content-end">
                <li class="my-account-dropdown">
                   <div class="language-selector nice-select">
                      <i class="fas fa-globe-americas text-dark"></i>
                      <select name="language" class="language selectors nice">
                      @foreach(DB::table('languages')->get() as $language)
                      <option value="{{route('front.language',$language->id)}}" {{ Session::has('language') ? ( Session::get('language') == $language->id ? 'selected' : '' ) : (DB::table('languages')->where('is_default','=',1)->first()->id == $language->id ? 'selected' : '') }} >
                      {{$language->language}}
                      </option>
                      @endforeach
                      </select>
                   </div>
                </li>
                <li class="my-account-dropdown">
                   <div class="currency-selector nice-select">
                      <span class="text-dark">{{ Session::has('currency') ? DB::table('currencies')->where('id','=',Session::get('currency'))->first()->sign   : DB::table('currencies')->where('is_default','=',1)->first()->sign }}</span>
                      <select name="currency" class="currency selectors nice">
                      @foreach(DB::table('currencies')->get() as $currency)
                      <option value="{{route('front.currency',$currency->id)}}" {{ Session::has('currency') ? ( Session::get('currency') == $currency->id ? 'selected' : '' ) : (DB::table('currencies')->where('is_default','=',1)->first()->id == $currency->id ? 'selected' : '') }}>
                      {{$currency->name}}
                      </option>
                      @endforeach
                      </select>
                   </div>
                </li>
                @if($gs->reg_vendor == 1)
                <div class=" align-items-center text-general sell">
                   @if(Auth::check())
                   @if(Auth::guard('web')->user()->is_vendor == 2)
                   <a href="{{ route('vendor.dashboard') }}" class="sell-btn "> {{ __('Sell') }}</a>
                   @else
                   <a href="{{ route('user-package') }}" class="sell-btn "> {{ __('Sell') }}</a>
                   @endif
                </div>
                @else
                <div class=" align-items-center text-general">
                   <a href="{{ route('vendor.login') }}" class="sell-btn "> {{ __('Sell') }}</a>
                </div>
                @endif
                @endif
             </ul>
          </div>
       </div>
    </div>
 </div>
