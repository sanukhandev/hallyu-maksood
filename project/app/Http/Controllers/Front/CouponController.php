<?php

namespace App\Http\Controllers\Front;

use App\{
    Models\Cart,
    Models\Coupon
};
use App\Models\Currency;
use App\Models\Product;
use Session;

class CouponController extends FrontBaseController
{

    public function coupon()
    {
        $gs = $this->gs;
        $code = $_GET['code'];
        $total = (float)preg_replace('/[^0-9\.]/ui', '', $_GET['total']);;
        $fnd = Coupon::where('code', '=', $code)->get()->count();
        $coupon = Coupon::where('code', '=', $code)->first();


        $cart = Session::get('cart');
        foreach ($cart->items as $item) {
            $product = Product::findOrFail($item['item']['id']);

            if ($coupon->coupon_type == 'category') {

                if ($product->category_id == $coupon->category) {
                    $coupon_check_type[] = 1;
                } else {

                    $coupon_check_type[] = 0;
                }
            } elseif ($coupon->coupon_type == 'sub_category') {
                if ($product->subcategory_id == $coupon->sub_category) {
                    $coupon_check_type[] = 1;
                } else {
                    $coupon_check_type[] = 0;
                }
            } elseif ($coupon->coupon_type == 'child_category') {
                if ($product->childcategory_id == $coupon->child_category) {
                    $coupon_check_type[] = 1;
                } else {
                    $coupon_check_type[] = 0;
                }
            } else {

                $coupon_check_type[] = 0;
            }
        }



        if (in_array(0, $coupon_check_type)) {
            return response()->json(0);
        }




        if ($fnd < 1) {
            return response()->json(0);
        } else {
            $coupon = Coupon::where('code', '=', $code)->first();
            $curr = $this->curr;
            if ($coupon->times != null) {
                if ($coupon->times == "0") {
                    return response()->json(0);
                }
            }
            $today = date('Y-m-d');
            $from = date('Y-m-d', strtotime($coupon->start_date));
            $to = date('Y-m-d', strtotime($coupon->end_date));
            if ($from <= $today && $to >= $today) {
                if ($coupon->status == 1) {
                    $oldCart = Session::has('cart') ? Session::get('cart') : null;
                    $val = Session::has('already') ? Session::get('already') : null;
                    if ($val == $code) {
                        return response()->json(2);
                    }
                    $cart = new Cart($oldCart);
                    if ($coupon->type == 0) {
                        if ($coupon->price >= $total) {
                            return response()->json(3);
                        }
                        Session::put('already', $code);
                        $coupon->price = (int)$coupon->price;
                        $val = $total / 100;
                        $sub = $val * $coupon->price;
                        $total = $total - $sub;
                        $data[0] = \PriceHelper::showCurrencyPrice($total);
                        $data[1] = $code;
                        $data[2] = round($sub, 2);
                        Session::put('coupon', $data[2]);
                        Session::put('coupon_code', $code);
                        Session::put('coupon_id', $coupon->id);
                        Session::put('coupon_total', $data[0]);
                        $data[3] = $coupon->id;
                        $data[4] = $coupon->price . "%";
                        $data[5] = 1;

                        Session::put('coupon_percentage', $data[4]);

                        return response()->json($data);
                    } else {
                        if ($coupon->price >= $total) {
                            return response()->json(3);
                        }
                        Session::put('already', $code);
                        $total = $total - round($coupon->price * $curr->value, 2);
                        $data[0] = $total;
                        $data[1] = $code;
                        $data[2] = $coupon->price * $curr->value;
                        Session::put('coupon', $data[2]);
                        Session::put('coupon_code', $code);
                        Session::put('coupon_id', $coupon->id);
                        Session::put('coupon_total', $data[0]);
                        $data[3] = $coupon->id;
                        $data[4] = \PriceHelper::showCurrencyPrice($data[2]);
                        $data[0] = \PriceHelper::showCurrencyPrice($data[0]);
                        Session::put('coupon_percentage', 0);
                        $data[5] = 1;
                        return response()->json($data);
                    }
                } else {
                    return response()->json(0);
                }
            } else {
                return response()->json(0);
            }
        }
    }

    public function couponcheck()
    {
        $gs = $this->gs;
        $code = $_GET['code'];
        $coupon = Coupon::where('code', '=', $code)->first();
        if (!$coupon) {
            return response()->json(0);
        }

        $cart = Session::get('cart');
        $discount_items = [];
        foreach ($cart->items as $key => $item) {
            $product = Product::findOrFail($item['item']['id']);

            if ($coupon->coupon_type == 'category') {
                if ($product->category_id == $coupon->category) {
                    $discount_items[] = $key;
                }
            } elseif ($coupon->coupon_type == 'sub_category') {
                if ($product->sub_category == $coupon->sub_category) {
                    $discount_items[] = $key;
                }
            } elseif ($coupon->coupon_type == 'child_category') {

                if ($product->child_category == $coupon->child_category) {
                    $discount_items[] = $key;
                }
            }
        }


        if (count($discount_items) == 0) {
            return 0;
        }

        // dd($discount_items);
        $main_discount_price = 0;
        foreach ($cart->items as $ckey => $cproduct) {
            if (in_array($ckey, $discount_items)) {
                $main_discount_price += $cproduct['price'];
            }
        }


        $total = (float)preg_replace('/[^0-9\.]/ui', '', $main_discount_price);
         if (Session::has('currency')) {
                $curr= Currency::find(Session::get('currency'));
            } else {
                $curr= Currency::where('is_default', '=', 1)->first();
            }




        $fnd = Coupon::where('code', '=', $code)->get()->count();
        if (Session::has('is_tax')) {
            $xtotal = ($total * Session::get('is_tax')) / 100;
            $total = $total + $xtotal;
        }
        if ($fnd < 1) {
            return response()->json(0);
        } else {
            $coupon = Coupon::where('code', '=', $code)->first();
            $curr = $this->curr;

            if ($coupon->times != null) {
                if ($coupon->times == "0") {
                    return response()->json(0);
                }
            }
            $today = date('Y-m-d');
            $from = date('Y-m-d', strtotime($coupon->start_date));
            $to = date('Y-m-d', strtotime($coupon->end_date));
            if ($from <= $today && $to >= $today) {
                if ($coupon->status == 1) {
                    $oldCart = Session::has('cart') ? Session::get('cart') : null;
                    $val = Session::has('already') ? Session::get('already') : null;
                    if ($val == $code) {
                        return response()->json(2);
                    }
                    $cart = new Cart($oldCart);
                    if ($coupon->type == 0) {
                        // dd('hi');

                        $total= $total* $curr->value;

                        $cou=

                        // if ($coupon->price >= $total) {
                        //     return response()->json(3);
                        // }
                        Session::put('already', $code);
                        $coupon->price = (int)$coupon->price;
                        //  dd($coupon->price);
                        // dd('hi');

                        $oldCart = Session::get('cart');
                        $cart = new Cart($oldCart);

                        $total = $total - $_GET['shipping_cost'];

                        $val = $total / 100;
                        $sub = $val * $coupon->price;
                        $total = $total - $sub;
                        $total = $total + $_GET['shipping_cost'];
                        $data[0] = \PriceHelper::showCurrencyPrice($total);
                        $data[1] = $code;
                        $data[2] = round($sub, 2);

                        Session::put('coupon', $data[2]);
                        Session::put('coupon_code', $code);
                        Session::put('coupon_id', $coupon->id);
                        Session::put('coupon_total1', round($total, 2));
                        Session::forget('coupon_total');

                        $data[3] = $coupon->id;
                        $data[4] = $coupon->price . "%";
                        $data[5] = 1;
                        $data[6] = round($total, 2);
                        Session::put('coupon_percentage', $data[4]);

                        return response()->json($data);
                    } else {

                        if ($coupon->price >= $total) {
                            return response()->json(3);
                        }
                        Session::put('already', $code);
                        $total = $total - round($coupon->price * $curr->value, 2);
                        $data[0] = $total;
                        $data[1] = $code;
                        $data[2] = $coupon->price * $curr->value;
                        $data[3] = $coupon->id;
                        $data[4] = \PriceHelper::showCurrencyPrice($data[2]);
                        $data[0] = \PriceHelper::showCurrencyPrice($data[0]);
                        Session::put('coupon', $data[2]);
                        Session::put('coupon_code', $code);
                        Session::put('coupon_id', $coupon->id);
                        Session::put('coupon_total1', round($total, 2));
                        Session::forget('coupon_total');
                        $data[1] = $code;
                        $data[2] = round($coupon->price * $curr->value, 2);
                        $data[3] = $coupon->id;
                        $data[5] = 1;
                        $data[6] = round($total, 2);
                        Session::put('coupon_percentage', $data[4]);

                        return response()->json($data);
                    }
                } else {
                    return response()->json(0);
                }
            } else {
                return response()->json("hurrey");
            }
        }
    }
}
