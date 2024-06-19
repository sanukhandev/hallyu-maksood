<div class="top-header font-400 d-none d-lg-block py-1 ">
    <div class="container">
       
          <!-- <div class="col-lg-4 sm-mx-none">
             <div class="d-flex align-items-center ">
                <i class="flaticon-phone-call flat-mini me-2 "></i>
                <span class="text-dark"> {{ $ps->phone }}</span>
             </div>
          </div> -->
          
             <ul class=" ms-auto  d-flex justify-content-end flost">
               
                <li class="my-account-dropdown">
                   <div class="currency-selector nice-select">
                     <select name="currency" class="currency selectors nice">
                      @foreach(DB::table('currencies')->get() as $currency)
                      <option value="{{route('front.currency',$currency->id)}}" {{ Session::has('currency') ? ( Session::get('currency') == $currency->id ? 'selected' : '' ) : (DB::table('currencies')->where('is_default','=',1)->first()->id == $currency->id ? 'selected' : '') }}>
                      {{$currency->name}}
                      </option>
                      @endforeach
                      </select>
                   </div>
                </li>
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
                <li class="nav-item dropdown {{ request()->path()=='faq' ? 'active' : '' }}">
                                <a class="nav-link " href="{{ route('front.faq') }}">{{ __('FAQ') }}</a>
                            </li>
               
             </ul>
          
       
    </div>
 </div>
