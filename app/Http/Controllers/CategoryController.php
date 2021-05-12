<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\UserSubCategory;
use Illuminate\Http\Request;
use App\Services\MobileFormatService;
use Validator;
use DB;

class CategoryController extends Controller
{

    // Admin and Seller
    public function createCategory(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }

        $category = new Category();
        $category->name = $request->name;
        $category->created_by = $request->user()->id;
        $category->save();
        return response()->json([
            'status' => 'success',
            "code"=> 200,
            "message"=> "OK",
            'data' => $category
        ],200);
    }



    // User
    public function getCategoryHome(Request $request){
        $limit = $request->limit ? $request->limit : 20;
        $cat = DB::select("SELECT c.id AS id,c.name AS name,c.click_count as click_count, f.url as image FROM categories c
                            LEFT JOIN files f ON f.module_id = c.id
                            WHERE is_deleted = FALSE
                            ORDER BY c.click_count DESC
                            LIMIT ".$limit);
        $catFirst = [];
        $cateSecond = [];
        for($i =0 ;$i < sizeof($cat);$i ++ ){
            $temp = [
                'id' => $cat[$i]->id,
                'image' => env('APP_URL').'/'.$cat[$i]->image,
                'click_count' => $cat[$i]->click_count,
                'name' => $cat[$i]->name

            ];
            if($i < (sizeof($cat) / 2)){
                array_push($catFirst,$temp);
            }else{
                array_push($cateSecond,$temp);
            }
        }
        $result = [
            'categoryFirst' => $catFirst,
            'categorySecond' => $cateSecond
        ];
        return MobileFormatService::formatWithoutPagination($result);
    }

    public function getUserSubCategory(Request $request){
        $limit = $request->limit ? (int)$request->limit : 10;
        $page = $request->page ? (int)$request->page : 1;
        $user_id = $request->user_id ? $request->user_id : null;
        $mac_address = $request->mac_address ? $request->mac_address : null;
        $default_sub_categories = DB::select("select udsc.id as id,sc.name as name,udsc.rank as rank,udsc.sub_category_id from user_default_sub_categories udsc
                INNER JOIN sub_categories sc on sc.id = udsc.sub_category_id
                order by rank");
        if($user_id){
            $user_sub_categories = DB::select("select usc.id as id,sc.name as name,usc.rank as rank,usc.sub_category_id as sub_category_id from user_sub_categories usc
                INNER JOIN sub_categories sc on sc.id = usc.sub_category_id
                where is_display = true and user_id = '".$user_id."' order by rank limit ".$limit." offset ".($limit * ($page - 1)));
            $total = UserSubCategory::where('is_display','=',true)->where('user_id','=',$user_id)->count();
        }else{
            $user_sub_categories = DB::select("select usc.id as id,sc.name as name,usc.rank as rank,usc.sub_category_id as sub_category_id from user_sub_categories usc
                INNER JOIN sub_categories sc on sc.id = usc.sub_category_id
                where is_display = true and mac_address = '".$mac_address."' order by rank limit ".$limit." offset ".($limit * ($page - 1)));
            $total = UserSubCategory::where('is_display','=',true)->where('mac_address','=',$mac_address)->count();
        }
        if(sizeof($user_sub_categories) == 0){
            foreach ($default_sub_categories as $default_sub_category){
                $user_sub_category = new UserSubCategory();
                $user_sub_category->sub_category_id = $default_sub_category->sub_category_id;
                $user_sub_category->rank = $default_sub_category->rank;
                $user_sub_category->mac_address = $mac_address;
                $user_sub_category->save();
                $mock = [
                    "id" => $user_sub_category->id,
                    "name" => $default_sub_category->name,
                    "sub_category_id" => $user_sub_category->sub_category_id,
                    "rank" => $user_sub_category->rank
                ];

                if(sizeof($user_sub_categories) < $limit){
                    array_push($user_sub_categories,$mock);
                }
            }
            $total = sizeof($default_sub_categories);
        }
        $last_page = ceil($total / $limit);
        return MobileFormatService::formatWithPagination($user_sub_categories,'user_sub_categories',$page,$last_page,$limit,$total);
    }

}
