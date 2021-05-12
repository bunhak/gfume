<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class ItemSearchRankController extends Controller
{
    public static function getItemSearchRankRecursive(&$array,$limit,$increase,$category_name){
        $searchText = '';
        foreach ($array as $a){
            $searchText.= " And i.id <> '".$a->id."' ";
        }
        $itemSearchRanks = DB::select("SELECT i.id AS id, i.name AS name FROM item_search_ranks isr
                                INNER JOIN items i ON i.id = isr.item_id
                                INNER JOIN sub_sub_categories ssc ON i.sub_sub_category_id = ssc.id
                                INNER JOIN categories c ON c.id = ssc.category_id
                                WHERE c.name LIKE '%".$category_name."%' ".$searchText."
                                ORDER BY isr.date_search DESC,isr.count DESC
                                LIMIT 1");
        if(sizeof($itemSearchRanks) < 1 || $increase == $limit){
            return $array;
        }else{
            $item = $itemSearchRanks[0];
            $exchange_rate = DB::select("select exchange_rate from exchange_rates where money_type = 'KHR' order by created_at desc limit 1")[0]->exchange_rate;
            $image = DB::select("SELECT CONCAT('".env('APP_URL')."','/',url) as image FROM files WHERE module_id = '".$item->id."' AND image_type = 'slide' LIMIT 1");
            $price = DB::select("SELECT price FROM item_details WHERE item_id = '".$item->id."' order by price limit 1");
            $item->image = $image[0]->image;
            $date = date("Y-m-d H:i:s");
            $discounts = DB::select("SELECT * from discounts where active = true and is_deleted = false and item_id = '$item->id' and start_date < '$date' and end_date > '$date'");
            $full_price = $price[0]->price;
            $discount = null;
            $item->discount = DB::select("SELECT * from discounts where active = true and is_deleted = false and item_id = '$item->id' and is_default = true order by created_at desc limit 1")[0]->discount;
            if(sizeof($discounts) > 0){
                $item->discount_limit_date = $discounts[0]->end_date;
                $item->discount = $discounts[0]->discount;
                $discount = round(($full_price * $item->discount) / 100,2 );
                $item->price = [
                    "USD" => $full_price - $discount,
                    "KHR" => ($full_price - $discount) * $exchange_rate
                ];
            }
            else{
                $discount = round(($full_price * $item->discount) / 100,2 );
                $item->discount_limit_date = null;
                $item->price = [
                    "USD" => $full_price - $discount,
                    "KHR" => ($full_price - $discount) * $exchange_rate
                ];
            }
            $item->full_price = [
                "USD" => $full_price,
                "KHR" => $full_price * $exchange_rate
            ];
            $item->rate = rand(0, 1000);
            $item->star = mt_rand(0 * 2, 5 * 2) / 2;
            $increase++;
            array_push($array,$item);
            ItemSearchRankController::getItemSearchRankRecursive($array,$limit,$increase,$category_name);
        }
    }
}
