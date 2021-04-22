<?php

namespace App\Http\Controllers;

use App\Models\Size;
use Illuminate\Http\Request;
use Validator;

class SizeController extends Controller
{
    public function createSize(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }

        $size = new Size();
        $size->name = $request->name;
        $size->item_id = $request->item_id;
        $size->created_by = $request->user()->id;
        $size->save();
        return response()->json([
            'status' => 'success',
            "code"=> 200,
            "message"=> "OK",
            'data' => $size
        ],200);
    }
}
