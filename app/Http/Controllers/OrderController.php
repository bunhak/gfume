<?php

namespace App\Http\Controllers;
use App\Models\Item;
use App\Models\ItemDetail;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Cart;
use App\Services\MobileFormatService;
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
        $item_detail = DB::select("select * from item_details where id = '$request->item_detail_id' limit 1")[0];
        $item = Item::where('id','=',$item_detail->item_id)->first();
        $qty = floatval($request->qty);
        $date = date("Y-m-d H:i:s");
        $discounts = DB::select("SELECT * from discounts where active = true and is_deleted = false and item_id = '$item->id' and start_date < '$date' and end_date > '$date'");
        $discount = 0;
        $discount_id = null;
        if(sizeof($discounts) > 0){
            $discount = $discounts[0]->discount;
            $discount_id = $discounts[0]->id;
            $sub_total = $item_detail->price * $qty;
            $total = $sub_total - round(($sub_total * $discount) / 100,2 );
        }else{
            $sub_total = $item_detail->price * $qty;
            $total = $item_detail->price * $qty;
        }
        $bonus_total = round(($total * $bonus->bonus) / 100 , 2);
        $carts = Cart::where('item_detail_id','=',$item_detail->id)
            ->where('user_id','=',$user->id)
            ->where('is_deleted','=',false)
            ->where('status','=','CART')->get();
        if(sizeof($carts) > 0){
            $cart = $carts[0];
            $cart->exchange_rate_id = $exchange_rate->id;
            $cart->exchange_rate = $exchange_rate->exchange_rate;
            $cart->bonus_id = $bonus->id;
            $cart->bonus = $bonus->bonus;
            $cart->bonus_total = $bonus_total;
            $cart->sub_total = $sub_total;
            $cart->total = $total;
            $cart->discount_id = $discount_id;
            $cart->discount = $discount;
            $cart->price = $item_detail->price;
            $cart->updated_by = $user->id;
            $cart->qty = $qty;
            $cart->save();
        }
        else{
            $cart = new Cart();
            $delivery_fee = DB::select("select * from delivery_fees where item_id = '$item->id'")[0];
            $cart->delivery_fee_id = $delivery_fee->id;
            $cart->delivery_fee = $delivery_fee->delivery_fee;
            $cart->qty = $qty;
            $cart->shop_id = $item->shop_id;
            $cart->item_id = $item_detail->item_id;
            $cart->item_detail_id = $request->item_detail_id;
            $cart->exchange_rate_id = $exchange_rate->id;
            $cart->exchange_rate = $exchange_rate->exchange_rate;
            $cart->bonus_id = $bonus->id;
            $cart->bonus = $bonus->bonus;
            $cart->bonus_total = $bonus_total;
            $cart->price = $item_detail->price;
            $cart->discount_id = $discount_id;
            $cart->discount = $discount;
            $cart->sub_total = $sub_total;
            $cart->total = $total;
            $cart->user_id = $user->id;
            $cart->created_by = $user->id;
            $cart->save();
        }
        return MobileFormatService::formatWithoutPagination($cart);
    }

    public function editCart(Request $request){
        $validator = Validator::make($request->all(),[
            'qty' => 'required',
            'id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }
        $user = $request->user();
        $cart = Cart::where('id','=',$request->id)
            ->where('user_id','=',$user->id)
            ->where('is_deleted','=',false)
            ->where('status','=','CART')->first();
        $exchange_rate = DB::select("select id,exchange_rate from exchange_rates where money_type = 'KHR' order by created_at desc limit 1")[0];
        $bonus = DB::select("select id,bonus from bonuses order by created_at desc limit 1")[0];

        $item_detail = DB::select("select * from item_details where id = '$cart->item_detail_id' limit 1")[0];
        $item = Item::where('id','=',$item_detail->item_id)->first();
        $qty = floatval($request->qty);
        $date = date("Y-m-d H:i:s");
        $discounts = DB::select("SELECT * from discounts where active = true and is_deleted = false and item_id = '$item->id' and start_date < '$date' and end_date > '$date'");
        $discount = 0;
        $discount_id = null;
        if(sizeof($discounts) > 0){
            $discount = $discounts[0]->discount;
            $discount_id = $discounts[0]->id;
            $sub_total = $item_detail->price * $qty;
            $total = $sub_total - round(($sub_total * $discount) / 100,2 );
        }else{
            $sub_total = $item_detail->price * $qty;
            $total = $item_detail->price * $qty;
        }
        $bonus_total = round(($total * $bonus->bonus) / 100 , 2);
        $cart->exchange_rate_id = $exchange_rate->id;
        $cart->exchange_rate = $exchange_rate->exchange_rate;
        $cart->bonus_id = $bonus->id;
        $cart->bonus = $bonus->bonus;
        $cart->bonus_total = $bonus_total;
        $cart->sub_total = $sub_total;
        $cart->total = $total;
        $cart->discount_id = $discount_id;
        $cart->discount = $discount;
        $cart->price = $item_detail->price;
        $cart->updated_by = $user->id;
        $cart->qty = $qty;
        $cart->save();
        return MobileFormatService::formatWithoutPagination($cart);
    }

    public function deleteFromCart(Request $request){
        $validator = Validator::make($request->all(),[
            'id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }
        $user = $request->user();
        $cart = Cart::where('id','=',$request->id)
            ->where('user_id','=',$user->id)
            ->where('is_deleted','=',false)
            ->where('status','=','CART')->first();
        $cart->is_deleted = true;
        $cart->save();
        return MobileFormatService::formatWithoutPagination($cart);
    }

    public function calculateOrder(Request $request){
        $user = $request->user();
        $current_carts = DB::select("select * from orders where created_by ='$user->id' and is_deleted = false and status = 'CART'");
        $current_cart = null;
        if(sizeof($current_carts) > 0){
            $current_cart = Order::where('id','=',$current_carts[0]->id)->first();
            $order_details = DB::select("select * from order_details where order_id = '$current_cart->id'");
            $shops =[];
            $current_cart->sub_total = 0;
            foreach ($order_details as $order_detail){
                $current_cart->sub_total += $order_detail->total;
                $shops[$order_detail->shop_id] = $order_detail->shop_id;
            }
            $current_cart->delivery_fee = 0;
            foreach ($shops as $shop){
                $delivery_fees = DB::select("select * from delivery_item_fees where shop_id = '$shop' and order_id = '$current_cart->id'");
                if(sizeof($delivery_fees) > 0){
                    $delivery_fee = $delivery_fees[0];
                    $current_cart->delivery_fee += $delivery_fee->delivery_fee;
                }
            }
            $current_cart->total = $current_cart->sub_total - $current_cart->delivery_fee;
            $current_cart->save();
        }
        return true;
    }

    public function getCurrentCart(Request $request){
        $ids = $request->ids ? $request->ids : [];
        $user = $request->user();
        $current_cart = [
            'items' => [],
            'total' => [
                "USD" => 0,
                "KHR" => 0
            ]
        ];
        if(sizeof($ids) > 0){
            $carts = Cart::whereIn('id',$ids)
                ->where('user_id','=',$user->id)
                ->where('is_deleted','=',false)
                ->where('status','=','CART')->get();
        }
        else{
            $carts = Cart::where('user_id','=',$user->id)
                ->where('is_deleted','=',false)
                ->where('status','=','CART')->get();
        }
        if(sizeof($carts) > 0){
            foreach ($carts as $cart){
                $item = [];
                $item_detail = DB::table('item_details')
                    ->join('colors', 'item_details.color_id', '=', 'colors.id')
                    ->join('sizes', 'item_details.size_id', '=', 'sizes.id')
                    ->join('items', 'item_details.item_id', '=', 'items.id')
                    ->select('item_details.id as id','item_details.item_id as item_id', 'colors.name as color', 'colors.id as color_id', 'sizes.name as size','items.name as item')
                    ->where('item_details.id','=',$cart->item_detail_id)
                    ->first();
                $image = DB::select("SELECT CONCAT('".env('APP_URL')."','/',url) as image FROM files WHERE module_id = '".$item_detail->color_id."' AND module_name = 'color' LIMIT 1")[0]->image;
                $item['id'] = $cart->id;
                $item['item_id'] = $item_detail->item_id;
                $item['item_detail_id'] = $item_detail->id;
                $item['image'] = $image;
                $item['discount'] = $cart->discount;
                $item['name'] = $item_detail->item;
                $item['color'] = $item_detail->color;
                $item['size'] = $item_detail->size;
                $item['qty'] = $cart->qty;
                $item['sub_total'] = [
                    "USD" => $cart->sub_total,
                    "KHR" => $cart->sub_total * $cart->exchange_rate
                ];
                $item['total'] = [
                    "USD" => $cart->total,
                    "KHR" => $cart->total * $cart->exchange_rate
                ];
                $item['bonus'] = [
                    "USD" => $cart->bonus_total,
                    "KHR" => $cart->bonus_total * $cart->exchange_rate
                ];
                $current_cart['total']["USD"] += $cart->total;
                $current_cart['total']["KHR"] += $cart->total * $cart->exchange_rate;
                array_push($current_cart['items'],$item);
            }
        }
        $current_cart['total']["USD"] = round($current_cart['total']["USD"],2);
        return MobileFormatService::formatWithoutPagination($current_cart);

    }
}
