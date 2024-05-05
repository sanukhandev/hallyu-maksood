@if($order->status == 'pending')

                                    <ul class="process-steps">
                                            <li class="active">
                                                <div class="icon">1</div>
                                                <div class="title">{{ __('Order Placed') }}</div>
                                            </li>
                                            <li class="">
                                                <div class="icon">2</div>
                                                <div class="title">{{ __('On Review') }}</div>
                                            </li>
                                            <li class="">
                                                <div class="icon">3</div>
                                                <div class="title">{{ __('On Delivery') }}</div>
                                            </li>
                                            <li class="">
                                                <div class="icon">4</div>
                                                <div class="title">{{ __('Delivered') }}</div>
                                            </li>
                                    </ul>

@elseif($order->status == 'processing')

                                    <ul class="process-steps">
                                            <li class="active">
                                                <div class="icon">1</div>
                                                <div class="title">{{__('Order Placed')}}</div>
                                            </li>
                                            <li class="active">
                                                <div class="icon">2</div>
                                                <div class="title">{{__('On Review')}}</div>
                                            </li>
                                            <li class="">
                                                <div class="icon">3</div>
                                                <div class="title">{{__('On Delivery')}}</div>
                                            </li>
                                            <li class="">
                                                <div class="icon">4</div>
                                                <div class="title">{{__('Delivered')}}</div>
                                            </li>
                                    </ul>


@elseif($order->status == 'on delivery')


                                    <ul class="process-steps">
                                            <li class="active">
                                                <div class="icon">1</div>
                                                <div class="title">{{__('Order Placed')}}</div>
                                            </li>
                                            <li class="active">
                                                <div class="icon">2</div>
                                                <div class="title">{{__('On Review')}}</div>
                                            </li>
                                            <li class="active">
                                                <div class="icon">3</div>
                                                <div class="title">{{__('On Delivery')}}</div>
                                            </li>
                                            <li class="">
                                                <div class="icon">4</div>
                                                <div class="title">{{__('Delivered')}}</div>
                                            </li>
                                    </ul>

@elseif($order->status == 'completed')

                                    <ul class="process-steps">
                                            <li class="active">
                                                <div class="icon">1</div>
                                                <div class="title">{{__('Order Placed')}}</div>
                                            </li>
                                            <li class="active">
                                                <div class="icon">2</div>
                                                <div class="title">{{__('On Review')}}</div>
                                            </li>
                                            <li class="active">
                                                <div class="icon">3</div>
                                                <div class="title">{{__('On Delivery')}}</div>
                                            </li>
                                            <li class="active">
                                                <div class="icon">4</div>
                                                <div class="title">{{__('Delivered')}}</div>
                                            </li>
                                    </ul>

@endif
