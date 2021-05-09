<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GlobalSearchRank;
use DB;

class GlobalSearchRankController extends Controller
{
    public static function getGlobalSearchRankRecursive(&$array,$limit,$increase){
        $searchText = '';
        foreach ($array as $a){
            echo $a['search'];
            $searchText.= " And search <> '".$a['search']."' ";
        }
        $globalSearchRanks = DB::select("select * from global_search_ranks where count > 0 ".$searchText."order by date_search desc,count desc limit 1");
        if(sizeof($globalSearchRanks) < 1 || $increase == $limit){
            return $array;
        }else{
            $mock = [];
            $globalSearchRank = $globalSearchRanks[0];
            $mock['search'] = $globalSearchRank->search;
            $compares = DB::select("select * from global_search_ranks where search = '".$globalSearchRank->search."' order by date_search desc limit 2");
            if(sizeof($compares) > 1){
                $compare = $compares[1];
                if($globalSearchRank->count > $compare->count){
                    $mock['status'] = 'UP';
                }else if($globalSearchRank->count < $compare->count){
                    $mock['status'] = 'DOWN';
                }else if($globalSearchRank->count == $compare->count){
                    $mock['status'] = 'NORMAL';
                }
            }else{
                $mock['status'] = 'UP';
            }
            $increase++;
            array_push($array,$mock);
            GlobalSearchRankController::getGlobalSearchRankRecursive($array,$limit,$increase);
        }
    }
}
