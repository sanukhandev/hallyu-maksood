		<a class="clear">{{ __('New Order(s).') }}</a>
		@if(count($datas) > 0)
		<a id="order-notf-clear" data-href="{{ route('vendor-order-notf-clear',Auth::user()->id) }}" class="clear" href="javascript:;">
			{{ __('Clear All.') }}
		</a>
		<ul>
		@foreach($datas as $data)
			<li>
				<a href="{{ route('vendor-order-show',$data->order_number) }}"> <i class="fas fa-newspaper"></i> {{ __('You Have a new order.') }}</a>
			</li>
		@endforeach

		</ul>

		@else 

		<a class="clear" href="javascript:;">
			{{ __('No New Notifications.')}}
		</a>

		@endif