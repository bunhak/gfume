<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{

    public function getCategoryHome(Request $request){
        $cat = Category::orderBy('click_count','desc')->take(20)->get();
        $catFirst = [];
        $cateSecond = [];
        for($i =0 ;$i < sizeof($cat);$i ++ ){
            $temp = [
                'id' => $cat[$i]->id,
                'image' => 'http://test.gfume.com/'.$cat[$i]->image,
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
