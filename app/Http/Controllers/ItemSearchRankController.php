<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class ItemSearchRankController extends Controller
{
    public static function getItemSearchRankRecursive(&$array,$limit,$increase,$category_name){
        $searchText = '';
        foreach ($array as $a){
            $searchText.= " And isr.item_id <> '".$a->id."' ";
        }
        $itemSearchRanks = DB::select("SELECT isr.item_id AS id FROM item_search_ranks isr
                                INNER JOIN item_sub_sub_categories issc on issc.item_id = isr.item_id
                                INNER JOIN sub_sub_categories ssc ON issc.sub_sub_category_id = ssc.id
                                INNER JOIN categories c ON c.id = ssc.category_id
                                WHERE c.name LIKE '%".$category_name."%' ".$searchText."
                                ORDER BY isr.date_search DESC,isr.count DESC
                                LIMIT 1");
        if(sizeof($itemSearchRanks) < 1 || $increase == $limit){
            return $array;
        }else{
            $exchange_rate = DB::select("select exchange_rate from exchange_rates where money_type = 'KHR' order by created_at desc limit 1")[0]->exchange_rate;
            $item = ItemController::getDetailItem($itemSearchRanks[0]->id,$exchange_rate);
            $increase++;
            array_push($array,$item);
            ItemSearchRankController::getItemSearchRankRecursive($array,$limit,$increase,$category_name);
        }
    }

    public static function getItemSearchRankBySubCategoryIdRecursive(&$array,$limit,$increase,$sub_category_id){
        $searchText = '';
        foreach ($array as $a){
            $searchText.= " And isr.item_id <> '".$a->id."' ";
        }
        $itemSearchRanks = DB::select("SELECT isr.item_id AS id FROM item_search_ranks isr
                                INNER JOIN item_sub_sub_categories issc on issc.item_id = isr.item_id
                                INNER JOIN sub_sub_categories ssc ON issc.sub_sub_category_id = ssc.id
                                WHERE ssc.sub_category_id = '".$sub_category_id."' ".$searchText."
                                ORDER BY isr.date_search DESC,isr.count DESC
                                LIMIT 1");
        if(sizeof($itemSearchRanks) < 1 || $increase == $limit){
            return $array;
        }else{
            $exchange_rate = DB::select("select exchange_rate from exchange_rates where money_type = 'KHR' order by created_at desc limit 1")[0]->exchange_rate;
            $item = ItemController::getDetailItem($itemSearchRanks[0]->id,$exchange_rate);
            $increase++;
            array_push($array,$item);
            ItemSearchRankController::getItemSearchRankBySubCategoryIdRecursive($array,$limit,$increase,$sub_category_id);
        }
    }
}
