<?php

namespace App\Http\Controllers;

use App\Models\ItemDetail;
use Illuminate\Http\Request;
use Validator;

class ItemDetailController extends Controller
{
    public function createItemDetail(Request $request){
        $validator = Validator::make($request->all(),[
            "item_details" => 'required',
            "item_id" => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }

        $itemDetails = [];
        for($i =0;$i<sizeof($request->item_details);$i++){
            $it = $request->item_details[$i];
            $item_detail = new ItemDetail();
            $item_detail->item_id = $request->item_id;
            $item_detail->qty = array_key_exists('qty', $it) ?  $it['qty'] : 0;
            $item_detail->price = array_key_exists('price', $it) ?  $it['price'] : 0;
            $item_detail->size_id = array_key_exists('size_id', $it) ?  $it['size_id'] : null;
            $item_detail->color_id = array_key_exists('color_id', $it) ?  $it['color_id'] : null;
            $item_detail->created_by = $request->user()->id;
            $item_detail->save();
            array_push($itemDetails,$item_detail);
        }

        return response()->json([
            'status' => 'success',
            "code"=> 200,
            "message"=> "OK",
            'data' => $itemDetails
        ],200);
    }
}
