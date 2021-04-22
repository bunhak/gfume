<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\Request;
use Validator;

class ColorController extends Controller
{
    public function createColor(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|string',
            'item_id' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }

        $color = new Color();
        $color->name = $request->name;
        $color->item_id = $request->item_id;
        $color->created_by = $request->user()->id;
        $color->save();
        return response()->json([
            'status' => 'success',
            "code"=> 200,
            "message"=> "OK",
            'data' => $color
        ],200);
    }
}
