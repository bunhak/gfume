<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use DB;
use Validator;

class OrderController extends Controller
{
    public function addToCart(Request $request){
        $validator = Validator::make($request->all(),[
            'qty' => 'required',
            'item_detail_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }
        $exchange_rate = DB::select("select id,exchange_rate from exchange_rates where money_type = 'KHR' order by created_at desc limit 1")[0];
        $bonus = DB::select("select id,bonus from bonuses order by created_at desc limit 1")[0];
        $user = $request->user();
        $currentCarts = DB::select(`select * from orders where created_by = '$user' and status = 'CART' and is_deleted = false`);
        $item_detail = DB::select(`select * from item_details where id = '$request->item_detail_id' limit 1`)[0];
        $qty = floatval($request->qty);
        $total = $item_detail->price * $qty;
        $total_KHR = $total * $exchange_rate->exchange_rate;
        $bonus_USD = round(($total * $bonus->bonus) / 100,2,PHP_ROUND_HALF_DOWN );
        $bonus_KHR = $bonus_USD * $exchange_rate->exchange_rate;
        if(sizeof($currentCarts) > 0){
            $currentCart = $currentCarts[0];
            $order_details = DB::select(`select * from order_detailes where item_detail_id = '$item_detail->id' and order_id = '$currentCart->id'`);
            if(sizeof($order_details) > 0){
                $order_detail = $order_details[0];
                $order_detail->exchange_rate_id = $exchange_rate->id;
                $order_detail->exchange_rate = $exchange_rate->exchange_rate;
                $order_detail->bonus_id = $bonus->id;
                $order_detail->bonus = $bonus_USD;
                $order_detail->bonus_KHR = $bonus_KHR;
                $order_detail->total = $total;
                $order_detail->total_KHR = $total_KHR;
                $order_detail->updated_by = $user->id;
                $order_detail->qty = $qty;
                $order_detail->save();
            }
            else{
                $order_detail = new OrderDetail();
                $order_detail->order_id = $currentCart->id;
                $order_detail->qty = $qty;
                $order_detail->item_id = $item_detail->item_id;
                $order_detail->item_detail_id = $request->item_detail_id;
                $order_detail->exchange_rate_id = $exchange_rate->id;
                $order_detail->exchange_rate = $exchange_rate->exchange_rate;
                $order_detail->bonus_id = $bonus->id;
                $order_detail->bonus = $bonus_USD;
                $order_detail->bonus_KHR = $bonus_KHR;
                $order_detail->total = $total;
                $order_detail->total_KHR = $total_KHR;
                $order_detail->created_by = $user->id;
                $order_detail->save();
            }
        }
        else{
            $currentCart = new Order();
            $currentCart->created_by = $user->id;
            $currentCart->save();
            $order_detail = new OrderDetail();
            $order_detail->order_id = $currentCart->id;
            $order_detail->qty = $qty;
            $order_detail->item_id = $item_detail->item_id;
            $order_detail->item_detail_id = $request->item_detail_id;
            $order_detail->exchange_rate_id = $exchange_rate->id;
            $order_detail->exchange_rate = $exchange_rate->exchange_rate;
            $order_detail->bonus_id = $bonus->id;
            $order_detail->bonus = $bonus_USD;
            $order_detail->bonus_KHR = $bonus_KHR;
            $order_detail->total = $total;
            $order_detail->total_KHR = $total_KHR;
            $order_detail->created_by = $user->id;
            $order_detail->save();
        }
    }

    public function getCurrentCart(Request $request){
        $user = $request->user();
        $current_carts = DB::select(`select * from orders where created_by ='$user->id' and is_deleted = false and status = 'CART'`);
        $current_cart = null;
        if(sizeof($current_carts) > 0){
            $current_cart = $current_carts[0];
            $order_details = DB::select(`select * from order_details where order_id = '$current_cart->id'`);
            foreach ($order_details as $order_detail){
                $current_cart->total += $order_detail->total;
                $current_cart->total_KHR += $order_detail->total_KHR;
            }
            $current_cart->save();

        }


    }
}
