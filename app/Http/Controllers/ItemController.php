<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Size;
use App\Models\SizeType;
use App\Models\SubCategory;
use App\Models\SubSubCategory;
use Illuminate\Http\Request;
use Validator;

class ItemController extends Controller
{

    public function mockData(){

        //mock category
        $a=['img/Fashion@1X.png','img/interior@1X.png'];
        for($i = 0;$i < 15;$i++){
            $t = rand(0,1);
            $cat = new Category();
            $cat->name = 'l1('.$i.')';
            $cat->image = $a[$t];
            $cat->click_count = rand(0, 100000);
            $cat->save();
            for($ii = 0;$ii < 4;$ii++){
                $tt = rand(0,1);
                $cat2 = new SubCategory();
                $cat2->name = 'l1('.$i.') l2('.$ii.')';
                $cat2->category_id = $cat->id;
                $cat2->image = $a[$tt];
                $cat2->click_count = rand(0, 100000);
                $cat2->save();
                for($iii = 0;$iii < 5;$iii++){
                    $ttt = rand(0,1);
                    $cat3 = new SubSubCategory();
                    $cat3->name ='l1('.$i.') l2('.$ii.') l3('.$i.')';
                    $cat3->sub_category_id = $cat2->id;
                    $cat3->image = $a[$ttt];
                    $cat3->click_count = rand(0, 100000);
                    $cat3->save();
                }
            }
        }

        //mock brand
        $a=['img/adidas.png','img/nike.png','img/jordan.png'];
        for($i =0;$i<3;$i++){
            $brand = new Brand();
            $brand->name = 'brand '.$i;
            $brand->image = $a[$i];
            $brand->save();
        }

        //mock color
        $a=['red','yellow','blue','black','white'];
        for($i = 0;$i<5;$i++){
            $color = new Color();
            $color->name = $a[$i];
            $color->save();
        }

        // mock size text
        $sizeType = new SizeType();
        $sizeType->name = 'text';
        $sizeType->save();
        $a=['xs','s','m','l','xl'];
        for ($i = 0;$i < sizeof($a);$i++){
            $size = new Size();
            $size->name = $a[$i];
            $size->size_type_id = $sizeType->id;
            $size->order = $i;
        }

        // mock size number
        $sizeType = new SizeType();
        $sizeType->name = 'number';
        $sizeType->save();
        for ($i = 10;$i < 100;$i++){
            $size = new Size();
            $size->name = [$i];
            $size->size_type_id = $sizeType->id;
            $size->order = $i;
        }








    }

    public function addNewItem(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|string',
            'description' => 'required|string',
            'brand_id' => 'required|string',
            'shop_id' => 'required|string',
            ''
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }
    }
}
