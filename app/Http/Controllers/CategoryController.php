<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function getCategoryHome(){
        $cat = Category::orderBy('click_count','desc')->take(20)->get();
        $catFirst = [];
        $cateSecond = [];
        for($i =0 ;$i < sizeof($cat);$i ++ ){
            $temp = [
                'id' => $cat[$i]->id,
                'image' => 'http://app.gfume.com/'.$cat[$i]->image,
                'click_count' => $cat[$i]->click_count

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

        $a=['public/Fashion@1X.png','public/interior@1X.png'];
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
