<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        // CHECKOUT
        'checkout/payment/paytm-notify',
        'checkout/payment/razorpay-notify',
        'cflutter/notify',
        'checkout/payment/ssl-notify',
        // SUBSCRIPTION
        'user/paytm-notify',
        'user/razorpay-notify',
        'uflutter/notify',
        'user/ssl-notify',
        // DEPOSIT
        'user/deposit/paytm-notify',
        'user/deposit/razorpay-notify',
        'dflutter/notify',
        'user/deposit/ssl-notify'
    ];
}
