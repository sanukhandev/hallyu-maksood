<div class="dashboard-overlay">&nbsp;</div>
<div id="sidebar" class="sidebar-blog bg-light p-30">
  <div class="dashbaord-sidebar-close d-xl-none">
    <i class="fas fa-times"></i>
  </div>
    <div class="widget border-0 py-0 widget_categories">
        <h4 class="widget-title down-line">{{ __('Dashboard') }}</h4>
        <ul>
            <li class=""><a class="{{ Request::url() == route('user-dashboard') ? 'active':'' }}" href="{{ route('user-dashboard') }}">Dashboard</a></li>
            @if(Auth::user()->IsVendor())
            <li class=""><a class="" href="{{ route('vendor.dashboard') }}">{{ __('Vendor Dashboard') }}</a></li>
            @endif
            <li class=""><a class="{{ Request::url() == route('user-orders') ? 'active':'' }}" href="{{ route('user-orders') }}">{{ __('Purchased Items') }}</a></li>
            <li class=""><a class="{{ Request::url() == route('user-deposit-index') ? 'active':'' }}" href="{{route('user-deposit-index')}}">{{ __('Deposit') }}</a></li>
            <li class=""><a class="{{ Request::url() == route('user-transactions-index') ? 'active':'' }}" href="{{route('user-transactions-index')}}">{{ __('Transactions') }}</a></li>
            <li class=""><a class="{{ Request::url() == route('user-reward-index') ? 'active':'' }}" href="{{route('user-reward-index')}}">{{ __('Rewards') }}</a></li>
            <li class=""><a class="{{ Request::url() == route('user-affilate-program') ? 'active':'' }}" href="{{ route('user-affilate-program') }}">{{ __('Affiliate Program') }}</a></li>
            <li class=""><a class="{{ Request::url() == route('user-wwt-index') ? 'active':'' }}" href="{{route('user-wwt-index')}}">{{ __('Withdraw') }}</a></li>
            <li class=""><a class="{{ Request::url() == route('user-order-track') ? 'active':'' }}" href="{{route('user-order-track')}}">{{ __('Order Tracking') }}</a></li>
            <li class=""><a class="{{ Request::url() == route('user-favorites') ? 'active':'' }}" href="{{route('user-favorites')}}">{{ __('Favourite Sellers') }}</a></li>
            <li class=""><a class="{{ Request::url() == route('user-messages') ? 'active':'' }}" href="{{route('user-messages')}}">{{ __('Messages') }}</a></li>
            <li class=""><a class="{{ Request::url() == route('user-message-index') ? 'active':'' }}" href="{{route('user-message-index')}}">{{ __('Tickets') }}</a></li>
            <li class=""><a class="{{ Request::url() == route('user-dmessage-index') ? 'active':'' }}" href="{{ route('user-dmessage-index') }}">{{ __('Disputes') }}</a></li>
            <li class=""><a class="{{ Request::url() == route('user-profile') ? 'active':'' }}" href="{{ route('user-profile') }}">{{ __('Edit Profile') }}</a></li>
            <li class=""><a class="{{ Request::url() == route('user-reset') ? 'active':'' }}" href="{{ route('user-reset') }}">{{ __('Reset Password') }}</a></li>
            <li class=""><a class="" href="{{ route('user-logout') }}">{{ __('Logout') }}</a></li>
        </ul>
    </div>
    @if($gs->reg_vendor == 1)
            <div class="row mt-4">
              <div class="col-lg-12 text-center">
                <a href="{{ route('user-package') }}" class="mybtn1 lg">
                  <i class="fas fa-dollar-sign"></i> {{ Auth::user()->is_vendor == 1 ? __('Start Selling') : (Auth::user()->is_vendor == 0 ? __('Start Selling') : __('Pricing Plans')) }}
                </a>
              </div>
            </div>
          @endif
</div>
