<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function getCategoryHome(Request $request){
        $cat = Category::orderBy('click_count','desc')->take(20)->get();
        $catFirst = [];
        $cateSecond = [];
        for($i =0 ;$i < sizeof($cat);$i ++ ){
            $temp = [
                'id' => $cat[$i]->id,
                'image' => env('APP_URL').$cat[$i]->image,
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
            'status' => 'success',
            "code"=> 200,
            "message"=> "OK",
            "data" => [
                'categoryFirst' => $catFirst,
                'categorySecond' => $cateSecond
            ]
        ];
        return response()->json($result);
    }
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
    public function mockData(){
        $a=['img/Fashion@1X.png','img/interior@1X.png'];
        for($i = 0;$i < 30;$i++){
            $t = rand(0,1);
            $cat = new Category();
            $cat->name = 'test'.$i;
            $cat->image = $a[$t];
            $cat->click_count = rand(0, 100000);
            $cat->save();

        }

    }

}
