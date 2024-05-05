<?php

namespace App\Http\Controllers\Front;

use App\{
    Models\Cart,
    Models\Product
};
use App\Models\Country;
use App\Models\Currency;
use App\Models\Generalsetting;
use App\Models\State;
use Illuminate\Http\Request;
use Session;

class CartController extends FrontBaseController
{

    public function cart(Request $request)
    {

        if (!Session::has('cart')) {
            return view('frontend.cart');
        }
        if (Session::has('already')) {
            Session::forget('already');
        }
        if (Session::has('coupon')) {
            Session::forget('coupon');
        }
        if (Session::has('coupon_total')) {
            Session::forget('coupon_total');
        }
        if (Session::has('coupon_total1')) {
            Session::forget('coupon_total1');
        }
        if (Session::has('coupon_percentage')) {
            Session::forget('coupon_percentage');
        }
        $oldCart = Session::get('cart');
        $cart = new Cart($oldCart);
        $products = $cart->items;
        $totalPrice = $cart->totalPrice;
        $mainTotal = $totalPrice;

        if($request->ajax()){
            return view('frontend.ajax.cart-page', compact('products','totalPrice','mainTotal'));
        }
        return view('frontend.cart', compact('products','totalPrice','mainTotal'));
    }

    public function cartview()
    {
        return view('load.cart');
    }
    public function view_cart(){
        if (!Session::has('cart')) {
            return view('frontend.cart');
        }
        if (Session::has('already')) {
            Session::forget('already');
        }
        if (Session::has('coupon')) {
            Session::forget('coupon');
        }
        if (Session::has('coupon_total')) {
            Session::forget('coupon_total');
        }
        if (Session::has('coupon_total1')) {
            Session::forget('coupon_total1');
        }
        if (Session::has('coupon_percentage')) {
            Session::forget('coupon_percentage');
        }
        $oldCart = Session::get('cart');
        $cart = new Cart($oldCart);
        $products = $cart->items;
        $totalPrice = $cart->totalPrice;
        $mainTotal = $totalPrice;
        return view('frontend.ajax.cart-page', compact('products','totalPrice','mainTotal'));

    }

    public function addcart($id)
    {

        $prod = Product::where('id','=',$id)->first(['id','user_id','slug','name','photo','size','size_qty','size_price','color','price','stock','type','file','link','license','license_qty','measure','whole_sell_qty','whole_sell_discount','attributes','size_all','color_all']);

        // Set Attrubutes

        $keys = '';
        $values = '';
        if(!empty($prod->license_qty))
        {
        $lcheck = 1;
            foreach($prod->license_qty as $ttl => $dtl)
            {
                if($dtl < 1)
                {
                    $lcheck = 0;
                }
                else
                {
                    $lcheck = 1;
                    break;
                }
            }
                if($lcheck == 0)
                {
                    return 0;
                }
        }

        // Set Size

        $size = '';
        if(!empty($prod->size))
        {
        $size = trim($prod->size[0]);
        }
        $size = str_replace(' ','-',$size);

        // Set Color

        $color = '';
        if(!empty($prod->color))
        {
        $color = $prod->color[0];
        $color = str_replace('#','',$color);
        }

        if($prod->stock_check == 0){
            if(empty($size))
            {

                if(!empty($prod->size_all))
                {
                $size = trim(explode(',',$prod->size_all)[0]);
                }
                $size = str_replace(' ','-',$size);
            }

            if(empty($color))
            {
                if(!empty($prod->color_all))
                {
                    $color =str_replace('#','',explode(',',$prod->color_all)[0]) ;
                }
            }
        }

        // Vendor Comission

        if($prod->user_id != 0){
        $gs = Generalsetting::findOrFail(1);
        $prc = $prod->price + $gs->fixed_commission + ($prod->price/100) * $gs->percentage_commission;
        $prod->price = round($prc,2);
        }


        // Set Attribute


            if (!empty($prod->attributes))
            {
                $attrArr = json_decode($prod->attributes, true);

                $count = count($attrArr);
                $i = 0;
                $j = 0;
                      if (!empty($attrArr))
                      {
                          foreach ($attrArr as $attrKey => $attrVal)
                          {

                            if (is_array($attrVal) && array_key_exists("details_status",$attrVal) && $attrVal['details_status'] == 1) {
                                if($j == $count - 1){
                                    $keys .= $attrKey;
                                }else{
                                    $keys .= $attrKey.',';
                                }
                                $j++;

                                foreach($attrVal['values'] as $optionKey => $optionVal)
                                {

                                    $values .= $optionVal . ',';
                                    $prod->price += $attrVal['prices'][$optionKey];
                                    break;
                                }

                            }
                          }

                      }

                }
                $keys = rtrim($keys, ',');
                $values = rtrim($values, ',');





        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = new Cart($oldCart);

        $cart->add($prod, $prod->id,$size,$color,$keys,$values);
        if($cart->items[$id.$size.$color.str_replace(str_split(' ,'),'',$values)]['dp'] == 1)
        {
            return 'digital';
        }
        if($cart->items[$id.$size.$color.str_replace(str_split(' ,'),'',$values)]['stock'] < 0)
        {
            return 0;
        }
        if($cart->items[$id.$size.$color.str_replace(str_split(' ,'),'',$values)]['size_qty'])
        {
            if($cart->items[$id.$size.$color.str_replace(str_split(' ,'),'',$values)]['qty'] > $cart->items[$id.$size.$color.str_replace(str_split(' ,'),'',$values)]['size_qty'])
            {
                return 0;
            }
        }
        $cart->totalPrice = 0;
        foreach($cart->items as $data)
        $cart->totalPrice += $data['price'];
        Session::put('cart',$cart);
        $data[0] = count($cart->items);
        return response()->json($data);
    }

    public function addtocart($id)
    {

        $prod = Product::where('id','=',$id)->first(['id','user_id','slug','name','photo','size','size_qty','size_price','color','price','stock','type','file','link','license','license_qty','measure','whole_sell_qty','whole_sell_discount','attributes','minimum_qty','size_all','color_all']);

        // Set Attrubutes

        $keys = '';
        $values = '';
        if(!empty($prod->license_qty))
        {
        $lcheck = 1;
            foreach($prod->license_qty as $ttl => $dtl)
            {
                if($dtl < 1)
                {
                    $lcheck = 0;
                }
                else
                {
                    $lcheck = 1;
                    break;
                }
            }
                if($lcheck == 0)
                {
                    return 0;
                }
        }

        // Set Size

        $size = '';
        if(!empty($prod->size))
        {
        $size = trim($prod->size[0]);
        }

        // Set Color

        $color = '';
        if(!empty($prod->color))
        {
        $color = $prod->color[0];
        $color = str_replace('#','',$color);
        }

        if($prod->stock_check == 0){
            if(empty($size))
            {

                if(!empty($prod->size_all))
                {
                $size = trim(explode(',',$prod->size_all)[0]);
                }
                $size = str_replace(' ','-',$size);
            }

            if(empty($color))
            {
                if(!empty($prod->color_all))
                {
                    $color =str_replace('#','',explode(',',$prod->color_all)[0]) ;
                }
            }
        }

        if($prod->user_id != 0){

        $prc = $prod->price + $this->gs->fixed_commission + ($prod->price/100) * $this->gs->percentage_commission ;
        $prod->price = round($prc,2);
        }

        // Set Attribute

            if (!empty($prod->attributes))
            {
                $attrArr = json_decode($prod->attributes, true);

                $count = count($attrArr);
                $i = 0;
                $j = 0;
                      if (!empty($attrArr))
                      {
                          foreach ($attrArr as $attrKey => $attrVal)
                          {

                            if (is_array($attrVal) && array_key_exists("details_status",$attrVal) && $attrVal['details_status'] == 1) {
                                if($j == $count - 1){
                                    $keys .= $attrKey;
                                }else{
                                    $keys .= $attrKey.',';
                                }
                                $j++;

                                foreach($attrVal['values'] as $optionKey => $optionVal)
                                {

                                    $values .= $optionVal . ',';

                                    $prod->price += $attrVal['prices'][$optionKey];
                                    break;

                                }

                            }
                          }

                      }

                }
                $keys = rtrim($keys, ',');
                $values = rtrim($values, ',');

        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = new Cart($oldCart);
        if(!empty($cart->items)){
            if(!empty($cart->items[$id.$size.$color.str_replace(str_split(' ,'),'',$values)])){
                $minimum_qty = (int)$prod->minimum_qty;
                if($cart->items[$id.$size.$color.str_replace(str_split(' ,'),'',$values)]['qty'] < $minimum_qty ){
                    return redirect()->route('front.cart')->with('unsuccess',__('Minimum Quantity is:').' '.$prod->minimum_qty);
                }
            }
            else{
                $minimum_qty = (int)$prod->minimum_qty;
                if($prod->minimum_qty != null){
                    if(1 < $minimum_qty){
                        return redirect()->route('front.cart')->with('unsuccess',__('Minimum Quantity is:').' '.$prod->minimum_qty);
                    }
                }
            }
        }else{
            $minimum_qty = (int)$prod->minimum_qty;
            if($prod->minimum_qty != null){
                if(1 < $minimum_qty){
                    return redirect()->route('front.cart')->with('unsuccess',__('Minimum Quantity is:').' '.$prod->minimum_qty);
                }
            }
        }

        $cart->add($prod, $prod->id, $size ,$color, $keys, $values);

        if($cart->items[$id.$size.$color.str_replace(str_split(' ,'),'',$values)]['dp'] == 1)
        {
            return redirect()->route('front.cart')->with('unsuccess',__('This item is already in the cart.'));
        }
        if($cart->items[$id.$size.$color.str_replace(str_split(' ,'),'',$values)]['stock'] < 0)
        {
            return redirect()->route('front.cart')->with('unsuccess',__('Out Of Stock.'));
        }
        if($cart->items[$id.$size.$color.str_replace(str_split(' ,'),'',$values)]['size_qty'])
        {
            if($cart->items[$id.$size.$color.str_replace(str_split(' ,'),'',$values)]['qty'] > $cart->items[$id.$size.$color.str_replace(str_split(' ,'),'',$values)]['size_qty'])
            {
                return redirect()->route('front.cart')->with('unsuccess',__('Out Of Stock.'));
            }
        }

        $cart->totalPrice = 0;
        foreach($cart->items as $data)
        $cart->totalPrice += $data['price'];
        Session::put('cart',$cart);
        return redirect()->route('front.cart');
    }


    public function addnumcart(Request $request)
    {
        
        $id = $_GET['id'];
        $qty = $_GET['qty'];
        $size = str_replace(' ','-',$_GET['size']);
        $color = $_GET['color'];
        $size_qty = $_GET['size_qty'];
        $size_price = (double)$_GET['size_price'];
        $size_key = $_GET['size_key'];
        $keys =  $_GET['keys'];
        $values = $_GET['values'];
        $prices = $_GET['prices'];
        $affilate_user = isset($_GET['affilate_user']) ? $_GET['affilate_user'] : '0';
        $keys = $keys == "" ? '' :implode(',',$keys);
        $values = $values == "" ? '' : implode(',',$values );
        $curr = $this->curr;

        $size_price = ($size_price / $curr->value);
        $prod = Product::where('id','=',$id)->first(['id','user_id','slug','name','photo','size','size_qty','size_price','color','price','stock','type','file','link','license','license_qty','measure','whole_sell_qty','whole_sell_discount','attributes','minimum_qty','stock_check','size_all','color_all']);
        if($prod->type != 'Physical'){
            $qty = 1;
        }

       

        if($prod->user_id != 0){
        $prc = $prod->price + $this->gs->fixed_commission + ($prod->price/100) * $this->gs->percentage_commission ;
        $prod->price = round($prc,2);
        }
        if(!empty($prices))
        {
         foreach($prices as $data){
            $prod->price += ($data / $curr->value);
            }

        }

        if(!empty($prod->license_qty))
        {
        $lcheck = 1;
            foreach($prod->license_qty as $ttl => $dtl)
            {
                if($dtl < 1)
                {
                    $lcheck = 0;
                }
                else
                {
                    $lcheck = 1;
                    break;
                }
            }
                if($lcheck == 0)
                {
                    return 0;
                }
        }


        if(empty($size))
        {
            if(!empty($prod->size))
            {
            $size = trim($prod->size[0]);
            }
            $size = str_replace(' ','-',$size);
        }

        if($size_qty == '0' && $prod->stock_check == 1)
        {

            return 0;
        }

        if(empty($color))
        {
            if(!empty($prod->color))
            {
            $color = $prod->color[0];
            }
        }


        if($prod->stock_check == 0){
            if(empty($size))
            {

                if(!empty($prod->size_all))
                {
                $size = trim(explode(',',$prod->size_all)[0]);
                }
                $size = str_replace(' ','-',$size);
            }

            if(empty($color))
            {
                if(!empty($prod->color_all))
                {
                    $color = explode(',',$prod->color_all)[0];
                }
            }
        }


        $color = str_replace('#','',$color);
        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = new Cart($oldCart);
    
        if(!empty($cart->items)){
            if(!empty($cart->items[$id.$size.$color.str_replace(str_split(' ,'),'',$values)])){
                $minimum_qty = (int)$prod->minimum_qty;
                if($cart->items[$id.$size.$color.str_replace(str_split(' ,'),'',$values)]['qty'] < $minimum_qty ){
                    $data = array();
                    $data[1] = true;
                    $data[2] = $minimum_qty;
                    return response()->json($data);
                }
            }
            else{
                 
                if($prod->minimum_qty != null){
                    $minimum_qty = (int)$prod->minimum_qty;
                    if($qty < $minimum_qty){
                        $data = array();
                        $data[1] = true;
                        $data[2] = $minimum_qty;
                        return response()->json($data);
                    }
                }
            }
        }else{ 
           
            if($prod->minimum_qty != null){
                $minimum_qty = (int)$prod->minimum_qty;
                if($qty < $minimum_qty){
                    $data = array();
                    $data[3] = true;
                    $data[4] = $minimum_qty;
                    return response()->json($data);
                }
            }
        }

        $cart->addnum($prod, $prod->id, $qty, $size,$color,$size_qty,$size_price,$size_key,$keys,$values,$affilate_user);
     
        
        if($cart->items[$id.$size.$color.str_replace(str_split(' ,'),'',$values)]['dp'] == 1)
        {
            return 'digital';
        }
        if($cart->items[$id.$size.$color.str_replace(str_split(' ,'),'',$values)]['stock'] < 0)
        {

            return 0;
        }
        if($prod->stock_check == 1){
            if($cart->items[$id.$size.$color.str_replace(str_split(' ,'),'',$values)]['size_qty'])
            {
                if($cart->items[$id.$size.$color.str_replace(str_split(' ,'),'',$values)]['qty'] > $cart->items[$id.$size.$color.str_replace(str_split(' ,'),'',$values)]['size_qty'])
                {
                    return 0;
                }
            }
        }


        $cart->totalPrice = 0;
        foreach($cart->items as $data)
        $cart->totalPrice += $data['price'];
        Session::put('cart',$cart);
        $data[0] = count($cart->items);
        $data[1] = $cart->totalPrice;
        $data[1] = \PriceHelper::showCurrencyPrice($data[1] * $curr->value);
        return response()->json($data);
    }

    public function addtonumcart(Request $request)
    {

        $id = $_GET['id'];
        $qty = $_GET['qty'];
        $size = str_replace(' ','-',$_GET['size']);
        $color = $_GET['color'];
        $size_qty = $_GET['size_qty'];
        $size_price = (double)$_GET['size_price'];
        $size_key = $_GET['size_key'];
        $affilate_user = isset($_GET['affilate_user']) ? $_GET['affilate_user'] : '0';
        $keys =  $_GET['keys'];
        $keys = explode(",",$keys);
        $values = $_GET['values'];
        $values = explode(",",$values);
        $prices = $_GET['prices'];
        $prices = explode(",",$prices);
        $keys = $keys == "" ? '' :implode(',',$keys);
        $values = $values == "" ? '' : implode(',',$values );
        $curr = $this->curr;
        $size_price = ($size_price / $curr->value);
        $prod = Product::where('id','=',$id)->first(['id','user_id','slug','name','photo','size','size_qty','size_price','color','price','stock','type','file','link','license','license_qty','measure','whole_sell_qty','whole_sell_discount','attributes','minimum_qty','stock_check','size_all','color_all']);
        if($prod->type != 'Physical'){
            $qty = 1;
        }


        if($prod->user_id != 0){
        $prc = $prod->price + $this->gs->fixed_commission + ($prod->price/100) * $this->gs->percentage_commission ;
        $prod->price = round($prc,2);
        }
        if(!empty($prices)){
            if(!empty($prices[0])){
                foreach($prices as $data){
                    $prod->price += ($data / $curr->value);
                }
            }
        }

        if(!empty($prod->license_qty))
        {
        $lcheck = 1;
            foreach($prod->license_qty as $ttl => $dtl)
            {
                if($dtl < 1)
                {
                    $lcheck = 0;
                }
                else
                {
                    $lcheck = 1;
                    break;
                }
            }
                if($lcheck == 0)
                {
                    return 0;
                }
        }
        if(empty($size))
        {
            if(!empty($prod->size))
            {
            $size = trim($prod->size[0]);
            }
            $size = str_replace(' ','-',$size);
        }

        if($size_qty == '0')
        {
            return redirect()->route('front.cart')->with('unsuccess',__('Out Of Stock.'));
        }

        if(empty($color))
        {
            if(!empty($prod->color))
            {
            $color = $prod->color[0];

            }
        }
        if($prod->stock_check == 0){
            if(empty($size))
            {

                if(!empty($prod->size_all))
                {
                $size = trim(explode(',',$prod->size_all)[0]);
                }
                $size = str_replace(' ','-',$size);
            }

            if(empty($color))
            {
                if(!empty($prod->color_all))
                {
                    $color = explode(',',$prod->color_all)[0];
                }
            }
        }



        $color = str_replace('#','',$color);

        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = new Cart($oldCart);

        if(!empty($cart->items)){
            if(!empty($cart->items[$id.$size.$color.str_replace(str_split(' ,'),'',$values)])){
                $minimum_qty = (int)$prod->minimum_qty;
                if($cart->items[$id.$size.$color.str_replace(str_split(' ,'),'',$values)]['qty'] < $minimum_qty ){
                    return redirect()->route('front.cart')->with('unsuccess',__('Minimum Quantity is:').' '.$prod->minimum_qty);
                }
            }
            else{
                if($prod->minimum_qty != null){
                    $minimum_qty = (int)$prod->minimum_qty;
                    if($qty < $minimum_qty){
                        return redirect()->route('front.cart')->with('unsuccess',__('Minimum Quantity is:').' '.$prod->minimum_qty);
                    }
                }
            }
        }else{
            $minimum_qty = (int)$prod->minimum_qty;
            if($prod->minimum_qty != null){
                if($qty < $minimum_qty){
                    return redirect()->route('front.cart')->with('unsuccess',__('Minimum Quantity is:').' '.$prod->minimum_qty);
                }
            }
        }

        $cart->addnum($prod, $prod->id, $qty, $size,$color,$size_qty,$size_price,$size_key,$keys,$values,$affilate_user);

        if($cart->items[$id.$size.$color.str_replace(str_split(' ,'),'',$values)]['dp'] == 1)
        {
            return redirect()->route('front.cart')->with('unsuccess',__('This item is already in the cart.'));
        }
        if($cart->items[$id.$size.$color.str_replace(str_split(' ,'),'',$values)]['stock'] < 0)
        {
            return redirect()->route('front.cart')->with('unsuccess',__('Out Of Stock.'));
        }
        if($prod->stock_check == 1){
            if($cart->items[$id.$size.$color.str_replace(str_split(' ,'),'',$values)]['size_qty'])
            {
                if($cart->items[$id.$size.$color.str_replace(str_split(' ,'),'',$values)]['qty'] > $cart->items[$id.$size.$color.str_replace(str_split(' ,'),'',$values)]['size_qty'])
                {
                    return redirect()->route('front.cart')->with('unsuccess',__('Out Of Stock.'));
                }
            }
        }


        $cart->totalPrice = 0;
        foreach($cart->items as $data)
        $cart->totalPrice += $data['price'];
        Session::put('cart',$cart);
        return redirect()->route('front.cart')->with('success',__('Successfully Added To Cart.'));
    }



    public function addbyone()
    {
        if (Session::has('coupon')) {
            Session::forget('coupon');
        }
        $curr = $this->curr;
        $id = $_GET['id'];
        $itemid = $_GET['itemid'];
        $size_qty = $_GET['size_qty'];
        $size_price = $_GET['size_price'];
        $prod = Product::where('id','=',$id)->first(['id','user_id','slug','name','photo','size','size_qty','size_price','color','price','stock','type','file','link','license','license_qty','measure','whole_sell_qty','whole_sell_discount','attributes','stock_check',]);

        if($prod->user_id != 0){
        $prc = $prod->price + $this->gs->fixed_commission + ($prod->price/100) * $this->gs->percentage_commission ;
        $prod->price = round($prc,2);
        }

            if (!empty($prod->attributes))
            {
                $attrArr = json_decode($prod->attributes, true);
                $count = count($attrArr);
                $j = 0;
                      if (!empty($attrArr))
                      {
                          foreach ($attrArr as $attrKey => $attrVal)
                          {

                            if (is_array($attrVal) && array_key_exists("details_status",$attrVal) && $attrVal['details_status'] == 1) {

                                foreach($attrVal['values'] as $optionKey => $optionVal)
                                {
                                    $prod->price += $attrVal['prices'][$optionKey];
                                    break;
                                }

                            }
                          }

                      }

                }

        if(!empty($prod->license_qty))
        {
        $lcheck = 1;
            foreach($prod->license_qty as $ttl => $dtl)
            {
                if($dtl < 1)
                {
                    $lcheck = 0;
                }
                else
                {
                    $lcheck = 1;
                    break;
                }
            }
                if($lcheck == 0)
                {
                    return 0;
                }
        }
        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = new Cart($oldCart);
        $cart->adding($prod, $itemid,$size_qty,$size_price);

        if($prod->stock_check == 1){
            if($cart->items[$itemid]['stock'] < 0)
            {

                return 0;
            }
            if(!empty($size_qty))
            {
                if($cart->items[$itemid]['qty'] > $cart->items[$itemid]['size_qty'])
                {

                    return 0;
                }
            }
        }

        $cart->totalPrice = 0;
        foreach($cart->items as $data)
        $cart->totalPrice += $data['price'];
        Session::put('cart',$cart);
        $data[0] = $cart->totalPrice;

        $data[3] = $data[0];


        $data[1] = $cart->items[$itemid]['qty'];
        $data[2] = $cart->items[$itemid]['price'];
        $data[0] = \PriceHelper::showCurrencyPrice($data[0] * $curr->value);
        $data[2] = \PriceHelper::showCurrencyPrice($data[2] * $curr->value);
        $data[3] = \PriceHelper::showCurrencyPrice($data[3] * $curr->value);
        $data[4] = $cart->items[$itemid]['discount'] == 0 ? '' : '('.$cart->items[$itemid]['discount'].'% '.__('Off').')';
        return response()->json($data);
    }

    public function reducebyone()
    {
        if (Session::has('coupon')) {
            Session::forget('coupon');
        }
        $curr = $this->curr;
        $id = $_GET['id'];
        $itemid = $_GET['itemid'];
        $size_qty = $_GET['size_qty'];
        $size_price = $_GET['size_price'];
        $prod = Product::where('id','=',$id)->first(['id','user_id','slug','name','photo','size','size_qty','size_price','color','price','stock','type','file','link','license','license_qty','measure','whole_sell_qty','whole_sell_discount','attributes']);
        if($prod->user_id != 0){
        $prc = $prod->price + $this->gs->fixed_commission + ($prod->price/100) * $this->gs->percentage_commission ;
        $prod->price = round($prc,2);
        }

            if (!empty($prod->attributes))
            {
                $attrArr = json_decode($prod->attributes, true);
                $count = count($attrArr);
                $j = 0;
                if (!empty($attrArr))
                {
                    foreach ($attrArr as $attrKey => $attrVal)
                    {
                        if (is_array($attrVal) && array_key_exists("details_status",$attrVal) && $attrVal['details_status'] == 1) {

                            foreach($attrVal['values'] as $optionKey => $optionVal)
                            {
                                $prod->price += $attrVal['prices'][$optionKey];
                                break;
                            }

                        }
                    }

                }
            }

        if(!empty($prod->license_qty))
        {
        $lcheck = 1;
            foreach($prod->license_qty as $ttl => $dtl)
            {
                if($dtl < 1)
                {
                    $lcheck = 0;
                }
                else
                {
                    $lcheck = 1;
                    break;
                }
            }
                if($lcheck == 0)
                {
                    return 0;
                }
        }
        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = new Cart($oldCart);
        $cart->reducing($prod, $itemid,$size_qty,$size_price);
        $cart->totalPrice = 0;
        foreach($cart->items as $data)
        $cart->totalPrice += $data['price'];

        Session::put('cart',$cart);
        $data[0] = $cart->totalPrice;

        $data[3] = $data[0];

        $data[1] = $cart->items[$itemid]['qty'];
        $data[2] = $cart->items[$itemid]['price'];
        $data[0] = \PriceHelper::showCurrencyPrice($data[0] * $curr->value);
        $data[2] = \PriceHelper::showCurrencyPrice($data[2] * $curr->value);
        $data[3] = \PriceHelper::showCurrencyPrice($data[3] * $curr->value);
        $data[4] = $cart->items[$itemid]['discount'] == 0 ? '' : '('.$cart->items[$itemid]['discount'].'% '.__('Off').')';
        return response()->json($data);
    }

    public function removecart($id)
    {
        $curr = $this->curr;
        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = new Cart($oldCart);
        $cart->removeItem($id);
        Session::forget('cart');
            Session::forget('already');
            Session::forget('coupon');
            Session::forget('coupon_total');
            Session::forget('coupon_total1');
            Session::forget('coupon_percentage');
        if (count($cart->items) > 0) {
            Session::put('cart', $cart);
                $data[0] = $cart->totalPrice;
                $data[3] = $data[0];


                if($this->gs->currency_format == 0){
                    $data[0] = $curr->sign.round($data[0] * $curr->value,2);
                    $data[3] = $curr->sign.round($data[3] * $curr->value,2);

                }
                else{
                    $data[0] = round($data[0] * $curr->value,2).$curr->sign;
                    $data[3] = round($data[3] * $curr->value,2).$curr->sign;
                }

            $data[1] = count($cart->items);
            return response()->json($data);
        } else {
            
            $data[0] = 0;

            if($this->gs->currency_format == 0){
                $data[1] = $curr->sign.round($data[0] * $curr->value,2);

            }
            else{
                $data[1] = round($data[0] * $curr->value,2).$curr->sign;
            }

            return response()->json($data);
        }
    }



    public function country_tax(Request $request)
    {

        if($request->country_id){
            if($request->state_id != 0 ){
                $state = State::findOrFail($request->state_id);
                $tax = $state->tax;
                $data[11] = $state->id ;
                $data[12] = 'state_tax';
            }else{
                $country = Country::findOrFail($request->country_id);
                $tax = $country->tax;
                $data[11] = $country->id;
                $data[12] = 'country_tax';
            }
           }else{
               $tax = 0;
           }

        $tax = $tax;
        Session::put('is_tax',$tax);


        $gs = Generalsetting::findOrFail(1);

        $total = (float)preg_replace('/[^0-9\.]/ui','',$_GET['total']);

        $stotal = ($total * $tax) / 100;

        $sstotal = $stotal * $this->curr->value;
        Session::put('current_tax',$sstotal);

        $total = $total + $stotal;

            if (Session::has('currency'))
            {
              $curr = Currency::find(Session::get('currency'));
            }
            else
            {
                $curr = Currency::where('is_default','=',1)->first();
            }

            $data[0]= $total;
            $data[1] = $tax;

            $data[0] = round($total,2);

            if(Session::has('coupon')){
                $data[0] = round($total - Session::get('coupon'),2);
            }

        return response()->json($data);

    }


}
