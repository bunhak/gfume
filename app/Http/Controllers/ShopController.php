<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Address;
use Illuminate\Http\Request;
use Validator;

class ShopController extends Controller
{
    public function createShop(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }

        $shop = new Shop();
        $shop->name = $request->name;
        $shop->created_by = $request->user()->id;
        $shop->user_id = $request->user()->id;
        $shop->save();

        if($request->address == true){
            $address = new Address();
            $address->module_id = $shop->id;
            $address->module_name = 'shop';
            $address->lat = $request->lat;
            $address->lng = $request->lng;
            $address->location = $request->location;
            $address->is_default = true;
            $address->save();
        }



        return response()->json([
            'status' => 'success',
            "code"=> 200,
            "message"=> "OK",
            'data' => $shop
        ],200);
    }
}
