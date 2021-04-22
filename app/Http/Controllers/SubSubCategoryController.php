<?php

namespace App\Http\Controllers;

use App\Models\SubSubCategory;
use Illuminate\Http\Request;
use Validator;
use App\Models\Category;
use App\Models\SubCategory;

class SubSubCategoryController extends Controller
{
    public function createSubSubCategory(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|string',
            'sub_category_id' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }
        $sub_cat = SubCategory::where('id','=',$request->sub_category_id)->first();
        $cat = Category::where('id','=',$sub_cat->category_id)->first();
        $sub_sub_category = new SubSubCategory();
        $sub_sub_category->name = $request->name;
        $sub_sub_category->category_id = $cat->id;
        $sub_sub_category->category_name = $cat->name;
        $sub_sub_category->sub_category_id = $sub_cat->id;
        $sub_sub_category->sub_category_name = $sub_cat->name;
        $sub_sub_category->created_by = $request->user()->id;
        $sub_sub_category->save();
        return response()->json([
            'status' => 'success',
            "code"=> 200,
            "message"=> "OK",
            'data' => $sub_sub_category
        ],200);
    }
}
