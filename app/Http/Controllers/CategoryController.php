<?php

namespace App\Http\Controllers;

use App\Models\Category;
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

}
