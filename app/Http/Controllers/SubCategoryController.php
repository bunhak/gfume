<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Validator;

class SubCategoryController extends Controller
{
    public function createSubCategory(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|string',
            'category_id' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }
        $cat = Category::where('id','=',$request->category_id)->first();
        $sub_category = new SubCategory();
        $sub_category->name = $request->name;
        $sub_category->category_id = $request->category_id;
        $sub_category->category_name = $cat->name;
        $sub_category->created_by = $request->user()->id;
        $sub_category->save();
        return response()->json([
            'status' => 'success',
            "code"=> 200,
            "message"=> "OK",
            'data' => $sub_category
        ],200);
    }
}
